<?php namespace PacketiX\Protocol\Time;

use \PacketiX\VPNException;

function get_current_stamp() {
  if (PHP_INT_SIZE < 8) {
    $intsize = PHP_INT_SIZE;
    throw new VPNException("integer type(sizeof:$intsize) cannot represent 64bit timestamp");
  }

  if (sscanf(microtime(), '%f %u', $msec, $sec) != 2) {
    throw new VPNException('get UNIX timestamp failed');
  }
  return $sec * 1000 + intval($msec * 1000) - 32400000;
}
