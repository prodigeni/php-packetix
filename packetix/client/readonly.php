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

    if (Detail\lookup($ret, 'IntValue') != $value
    || Detail\lookup($ret, 'StrValue') != sprintf('%d', $value)) {
      throw new VPNException('PacketiX RPC test failed');
    }
    return true;
  }

  public function get_server_info() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetServerInfo');
  }

  public function get_server_status() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetServerStatus');
  }

  public function enum_listener() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('EnumListener');
  }

  public function get_farm_setting() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetFarmSetting');
  }

  public function get_farm_info() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetFarmInfo');
  }

  public function enum_farm_member() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('EnumFarmMember');
  }

  public function get_farm_connections_status() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetFarmConnectionsStatus');
  }

  public function get_server_cert() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetServerCert');
  }

  public function get_server_cipher() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetServerCipher');
  }

  public function get_hub() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetHub');
  }

  public function enum_hub() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('EnumHub');
  }

  public function get_hub_radius() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetHubRadius');
  }

  public function enum_connection() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('EnumConnection');
  }

  public function get_connection_info() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetConnectionInfo');
  }

  public function get_hub_status() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetHubStatus');
  }

  public function get_hub_log() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetHubLog');
  }

  public function enum_ca() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('EnumCa');
  }

  public function get_ca() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetCa');
  }

  public function get_link() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetLink');
  }

  public function enum_link() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('EnumLink');
  }

  public function get_link_status() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetLinkStatus');
  }

  public function enum_access() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('EnumAccess');
  }

  public function get_user() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetUser');
  }

  public function enum_user() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('EnumUser');
  }

  public function get_group() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetGroup');
  }

  public function enum_group() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('EnumGroup');
  }

  public function enum_session() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('EnumSession');
  }

  public function get_session_status() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetSessionStatus');
  }

  public function enum_mac_table() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('EnumMacTable');
  }

  public function enum_ip_table() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('EnumIpTable');
  }

  public function get_keep() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetKeep');
  }

  public function get_secure_nat_option() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetSecureNATOption');
  }

  public function enum_nat() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('EnumNAT');
  }

  public function enum_dhcp() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('EnumDHCP');
  }

  public function get_secure_nat_status() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetSecureNATStatus');
  }

  public function enum_ethernet() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('EnumEthernet');
  }

  public function enum_local_bridge() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('EnumLocalBridge');
  }

  public function get_bridge_support() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetBridgeSupport');
  }

  public function get_caps() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetCaps');
  }

  public function get_config() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetConfig');
  }

  public function get_default_hub_admin_options() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetDefaultHubAdminOptions');
  }

  public function get_hub_admin_options() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetHubAdminOptions');
  }

  public function get_hub_ext_options() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetHubExtOptions');
  }

  public function enum_l3_switch() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('EnumL3Switch');
  }

  public function enum_l3_if() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('EnumL3If');
  }

  public function enum_l3_table() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('EnumL3Table');
  }

  public function enum_crl() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('EnumCrl');
  }

  public function get_crl() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetCrl');
  }

  public function enum_log_file() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('EnumLogFile');
  }

  public function read_log_file() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('ReadLogFile');
  }

  public function enum_license_key() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('EnumLicenseKey');
  }

  public function get_license_status() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetLicenseStatus');
  }

  public function get_syslog() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetSysLog');
  }

  public function enum_eth_vlan() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('EnumEthVlan');
  }

  public function get_hub_msg() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetHubMsg');
  }

  public function get_admin_msg() {
    throw new VPNException('not implemented yet');
    $ret = $this->connection->call('GetAdminMsg');
  }
}
