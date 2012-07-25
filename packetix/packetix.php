<?php namespace PacketiX;
require_once('client/readonly.php');

class VPNException extends \Exception {}

// Use TCP/443 as default connection port.
function create($host, $port = 443, $hub = false) {
  if (array_search('ssl', stream_get_transports()) == false) {
    throw new VPNException('PacketiX VPN requires SSL connectivity');
  }

  $client = new Client\Readonly($host, $port);
  if ($hub) {
    $client->hub($hub);
  }

  return $client;
}

