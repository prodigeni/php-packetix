<?php namespace PacketiX\Client;
require_once(dirname(__FILE__).'/../protocol/protocol.php');

use \PacketiX\VPNException;
use \PacketiX\Protocol\Detail;

class Readonly {
  protected $connection;

  public function __construct($host, $port) {
    $this->connection = new \PacketiX\Protocol\Connection($host, $port);
  }

  public function hub($hub, $reconnect = true) {
    $this->connection->hubmode($hub, $reconnect);
    return $this;
  }
  public function pass($pw, $reconnect = true) {
    $this->connection->password($pw, $reconnect);
    return $this;
  }

  public function test($value = false) {
    if ($value == false) {
      $value = mt_rand();
    }

    $ret = $this->connection->call('Test', array(
      'IntValue' => array(new Detail\Int($value)),
      'StrValue' => array(new Detail\String(openssl_random_pseudo_bytes(8)))));

    if ($ret['IntValue'][0]->get_value() != $value
    || $ret['StrValue'][0]->get_value() != sprintf('%d', $value)) {
      throw new VPNException('PacketiX RPC test failed');
    }
    return true;
  }
}

