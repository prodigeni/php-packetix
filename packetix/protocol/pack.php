<?php namespace PacketiX\Protocol\Detail;

class VPNPackException extends \PacketiX\VPNException {}

abstract class Type {
  protected $value;
  public function __construct($value) {
    $this->value = $value;
  }

  public function get_value() {
    return $this->value;
  }
}

class Int extends Type {}
class Int64 extends Type {}
class Data extends Type {}
class String extends Type {}
class Ustring extends Type {}

class RawIStream {
  private $raw;
  private $offset = 0;

  public function __construct($raw) {
    $this->raw = $raw;
  }

  private function get_int_impl($size) {
    if (PHP_INT_SIZE < $size) {
      $intsize = PHP_INT_SIZE;
      throw new VPNPackException("integer type(sizeof:$intsize) cannot represent value");
    }

    $raw = $this->get_raw($size);

    $value = 0;
    for ($i = 0; $i < $size; ++$i) {
      $value = ($value << 8) + ord($raw[$i]);
    }
    return $value;
  }

  public function get_raw($count) {
    $bytes = substr($this->raw, $this->offset, $count);
    $this->offset += $count;
    return $bytes;
  }

  public function get_int() { return $this->get_int_impl(4); }
  public function get_int64() { return $this->get_int_impl(8); }
  public function get_boolean() { return $this->get_int() == 1; }
  public function get_string($offset = 0) {
    $len = $this->get_int() - $offset;
    return $this->get_raw($len);
  }
}

function deserialize($raw) {
  $typer = array(
    0 => function($stream) { // Int
      return new Int($stream->get_int());
    },
    1 => function($stream) { // Data
      return new Data($stream->get_string());
    },
    2 => function($stream) { // String
      return new String($stream->get_string());
    },
    3 => function($stream) { // Unicode String
      return new Ustring($stream->get_string());
    },
    4 => function($stream) { // Int64
      return new Int64($stream->get_int64());
    }
  );

  $stream = new RawIStream($raw);
  $p = array();

  $count = $stream->get_int(); // element count
  for ($i = 0; $i < $count; ++$i) {
    $name = $stream->get_string(1);
    $f = $typer[$stream->get_int()];
    $vcount = $stream->get_int(); // value count

    $v = array();
    for ($j = 0; $j < $vcount; ++$j) {
      $v[$j] = $f($stream);
    }
    $p[$name] = $v;
  }

  return $p;
}

class RawOStream {
  private $raw = '';
  private $len = 0;

  public function get_raw() { return $this->raw; }

  private function set_int_impl($val, $size) {
    if (PHP_INT_SIZE < $size) {
      $intsize = PHP_INT_SIZE;
      throw new VPNPackException("integer type(sizeof:$intsize) cannot represent value");
    }

    $tmp = '';
    for ($i = $size; $i--; ) {
      $tmp .= chr(($val >> ($i * 8)) % 256);
    }
    $this->set_raw($tmp);
  }

  public function set_raw($raw) {
    $this->raw .= $raw;
    $this->len += strlen($raw);
  }

  public function set_int($val) { $this->set_int_impl($val, 4); }
  public function set_int64($val) { $this->set_int_impl($val, 8); }
  public function set_boolean($val) { $this->set_int($val ? 1 : 0); }
  public function set_string($val, $offset = 0) {
    $this->set_int(strlen($val) + $offset);
    $this->set_raw($val);
  }
}

function serialize($p) {
  $typer = array(
    'PacketiX\Protocol\Detail\Int' => array(0, function($ostr, $v) {
      $ostr->set_int($v->get_value());
    }),
    'PacketiX\Protocol\Detail\Data' => array(1, function($ostr, $v) {
      $ostr->set_string($v->get_value());
    }),
    'PacketiX\Protocol\Detail\String' => array(2, function($ostr, $v) {
      $ostr->set_string($v->get_value());
    }),
    'PacketiX\Protocol\Detail\Ustring' => array(3, function($ostr, $v) {
      $ostr->set_string($v->get_value());
    }),
    'PacketiX\Protocol\Detail\Int64' => array(4, function($ostr, $v) {
      $ostr->set_int64($v->get_value());
    })
  );

  $ostr = new RawOStream;
  $ostr->set_int(count($p));
  foreach ($p as $key => $value) {
    if (!count($value)) { continue; }

    $t = $typer[get_class($value[0])];
    $ostr->set_string($key, 1);
    $ostr->set_int($t[0]);
    $ostr->set_int(count($value));

    foreach ($value as $v) {
      $t[1]($ostr, $v);
    }
  }

  return $ostr->get_raw();
}
