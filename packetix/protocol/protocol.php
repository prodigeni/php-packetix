<?php namespace PacketiX\Protocol;
require_once('pack.php');
require_once('http.php');
require_once('time.php');
require_once('platform.php');
require_once('error.php');

class VPNProtocolException extends \PacketiX\VPNException {};

class Connection {
  private $sockopen;

  public function __construct($host, $port) {
    $this->sockopen = function() use ($host, $port) {
      $soc = fsockopen('ssl://'.$host, $port);
      if (!$soc) {
        throw new VPNProtocolException("Socket open failed; ssl://$host; port $port");
      }
      return $soc;
    };

    $this->hashed_password = $this->sha0('');
  }

  public function __destruct() {
    if ($this->socket) {
      fclose($this->socket);
    }
  }

  private function send_signature() {
    if (!HTTP\send_request($this->socket, 'post', '/vpnsvc/connect.cgi', 'VPNCONNECT')) {
      throw new VPNProtocolException('Send signature failed');
    }
  }

  private $random;
  private function recv_hello() {
    $res = HTTP\recv_response($this->socket);
    if ($res->code() != 200 || $res->opt('Content-Type') != 'application/octet-stream') {
      throw new VPNProtocolException('Unexpected HTTP response header');
    }

    $pack = check_error(Detail\deserialize($res->content()));

    $ake = function($key) use ($pack) {
      return array_key_exists($key, $pack);
    };
    if (!$ake('hello') || !$ake('random') || !$ake('build') || !$ake('version')) {
      throw new VPNProtocolException('Bad Hello messages');
    }

    $this->random = $pack['random'][0]->get_value();
  }

  private function sha0($raw) {
    return openssl_digest($raw, 'sha', true);
  }

  private function secure_password() {
    return $this->sha0($this->hashed_password.$this->random);
  }

  private function enter_admin_session() {
    $p = array(
      'method' => array(new Detail\Type('string', 'admin')),
      'timestamp' => array(new Detail\Type('int64', Time\get_current_stamp())),
      'client_str' => array(new Detail\Type('string', 'PacketiX RPC API for PHP')),
      'client_ver' => array(new Detail\Type('int', '0')),
      'client_build' => array(new Detail\Type('int', '0'))
    );
    Platform\packing_winver($p);

    if ($this->hub) {
      $p['hubname'] = array($this->hub);
    }

    $secpw = $this->secure_password();
    $p['secure_password'] = array(new Detail\Type('data', $secpw));

    $raw = Detail\serialize($p);
    HTTP\send_request($this->socket, 'post', '/vpnsvc/vpn.cgi', $raw, array(
      // XXX: Date
      'Keep-Alive' => 'timeout=15; max=19',
      'Connection' => 'Keep-Alive',
      'Content-Type' => 'application/octet-stream'));

    $res = HTTP\recv_response($this->socket);
    if ($res->code() != 200 || $res->opt('Content-Type') != 'application/octet-stream') {
      throw new VPNProtocolException('Unexpected HTTP response header');
    }

    $up = check_error(Detail\deserialize($res->content()));
  }

  private $socket = false, $hub = false;
  private $hashed_password;
  private function connect() {
    $sockopen = $this->sockopen;
    $this->socket = $sockopen();

    try {
      $this->send_signature();
      $this->recv_hello();
      $this->enter_admin_session();
    } catch (VPNProtocolException $e) {
      fclose($this->socket);
      $this->socket = false;
      throw $e;
    }

    return $this->socket;
  }

  public function check_and_reconnect() {
    if (!$this->socket || feof($this->socket)) {
      $this->reconnect();
    }
  }
  public function reconnect() {
    if ($this->socket) { fclose($this->socket); }
    $this->connect();
  }

  public function hubmode($hub, $reconnect = true) {
    $this->hub = $hub;
    if ($reconnect) {
      $this->reconnect();
    }
  }
  public function password($pw, $reconnect = true) {
    $this->hashed_password = $this->sha0($pw);
    if ($reconnect) {
      $this->reconnect();
    }
  }

  public function call($function, $rpc = array()) {
    if (array_key_exists('function_name', $rpc)) {
      throw new VPNProtocolException('function_name should not specify by user');
    }
    $rpc['function_name'] = array(new Detail\Type('string', $function));
    $raw = Detail\serialize($rpc);

    $ostr = new Detail\RawOStream;
    $ostr->set_int(strlen($raw));

    $this->check_and_reconnect();
    fwrite($this->socket, $ostr->get_raw().$raw);

    $istr = new Detail\RawIStream(fread($this->socket, 4));
    $raw = fread($this->socket, $istr->get_int());

    return check_error(Detail\deserialize($raw));
  }
}
