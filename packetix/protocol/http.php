<?php namespace PacketiX\Protocol\HTTP;

class VPNHTTPException extends \PacketiX\VPNException {}

function send_request($soc, $method, $target, $body, $opts = array()) {
  $method = strtoupper($method);
  $header = "$method $target HTTP/1.1\r\n";

  $has_CL = false;
  foreach ($opts as $key => $value) {
    if ($key == "Content-Length") { $has_CL = true; }

    $header .= $key.': '.$value."\r\n";
  }

  if (!$has_CL) {
    $header .= 'Content-Length: '.sprintf('%d', strlen($body))."\r\n";
  }

  $raw = $header."\r\n".$body;
  return fwrite($soc, $raw) == strlen($raw);
}

function recv_response($soc) {
  return HTTPresponse::recv_response_impl($soc);
}

class HTTPresponse {
  public static function recv_response_impl($soc) {
    $getline = function() use ($soc) {
      return chop(fgets($soc));
    };

    preg_match('/^HTTP\/(\d)\.(\d) (\d+) (.+)$/', $getline(), $m);
    if ($m[1] != '1' || $m[2] != '1') {
      throw new VPNHTTPException("now supports only HTTP/1.1 but HTTP/$m[1].$m[2] got.");
    }

    $res = new HTTPresponse;
    sscanf($m[3], '%u', $res->code_);
    $res->codeline_ = $m[4];

    while (strlen($line = $getline()) != 0) {
      preg_match('/^(.+): (.+)$/', $line, $m);
      $res->opts_[$m[1]] = $m[2];
    }

    if ($res->opt('Content-Length') == NULL) {
      throw new VPNHTTPException('fatal: reply header has no Content-Length field');
    }

    sscanf($res->opt('Content-Length'), '%u', $len);
    $res->content_ = fread($soc, $len);
    return $res;
  }

  private $code_, $codeline_, $opts_, $content_;

  public function code() {
    return $this->code_;
  }
  public function codeline() {
    return $this->codeline_;
  }
  public function opt($key) {
    return $this->opts_[$key];
  }
  public function content() {
    return $this->content_;
  }
}
