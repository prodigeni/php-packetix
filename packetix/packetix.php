<?php
namespace PacketiX {
require_once('admin/readonly.php');

class VPNException extends \Exception {}
}

namespace {
// Use TCP/443 as default connection port.
function packetix_readonly_admin($host, $port = 443, $hub = false) {
  if (array_search('ssl', stream_get_transports()) == false) {
    throw new \PacketiX\VPNException('PacketiX VPN requires SSL connectivity');
  }

  $client = new \PacketiX\Admin\Readonly($host, $port);
  if ($hub) {
    $client->hub($hub);
  }

  return $client;
}
}
