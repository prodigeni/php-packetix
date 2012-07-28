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

  private static function get_os_info($pack) {
    return array(
      'Kernel' => Detail\lookup($pack, 'KernelName'),
      'KernelVersion' => Detail\lookup($pack, 'KernelVersion'),
      'Product' => Detail\lookup($pack, 'OsProductName'),
      'ServicePack' => Detail\lookup($pack, 'OsServicePack'),
      'SystemName' => Detail\lookup($pack, 'OsSystemName'),
      'Type' => Detail\lookup($pack, 'OsType'),
      'VendorName' => Detail\lookup($pack, 'OsVendorName'),
      'Version' => Detail\lookup($pack, 'OsVersion'),
    );
  }

  public function get_server_info() {
    $ret = $this->connection->call('GetServerInfo');
    return array(
      'OSInfo' => self::get_os_info($ret),
      'BuildInfo' => Detail\lookup($ret, 'ServerBuildInfoString'),
      'Build' => Detail\lookup($ret, 'ServerBuildInt'),
      'HostName' => Detail\lookup($ret, 'ServerHostName'),
      'Product' => Detail\lookup($ret, 'ServerProductName'),
      'Type' => Detail\lookup($ret, 'ServerType'),
      'Version' => Detail\lookup($ret, 'ServerVerInt'),
      'VersionString' => Detail\lookup($ret, 'ServerVersionString'),
    );
  }

  public static function get_traffic($pack) {
    return array(
      'Recv' => array(
        'BroadcastBytes' => Detail\lookup($pack, 'Recv.BroadcastBytes'),
        'BroadcastCount' => Detail\lookup($pack, 'Recv.BroadcastCount'),
        'UnicastBytes' => Detail\lookup($pack, 'Recv.UnicastBytes'),
        'UnicastCount' => Detail\lookup($pack, 'Recv.UnicastCount'),
      ),
      'Send' => array(
        'BroadcastBytes' => Detail\lookup($pack, 'Send.BroadcastBytes'),
        'BroadcastCount' => Detail\lookup($pack, 'Send.BroadcastCount'),
        'UnicastBytes' => Detail\lookup($pack, 'Send.UnicastBytes'),
        'UnicastCount' => Detail\lookup($pack, 'Send.UnicastCount'),
      ),
    );
  }

  public static function get_meminfo($pack) {
    return array(
      'TotalMemory' => Detail\lookup($pack, 'TotalMemory'),
      'UsedMemory' => Detail\lookup($pack, 'UsedMemory'),
      'FreeMemory' => Detail\lookup($pack, 'FreeMemory'),
      'TotalPhys' => Detail\lookup($pack, 'TotalPhys'),
      'UsedPhys' => Detail\lookup($pack, 'UsedPhys'),
      'FreePhys' => Detail\lookup($pack, 'FreePhys'),
    );
  }

  public function get_server_status() {
    $ret = $this->connection->call('GetServerStatus');
    return array(
      'Traffic' => self::get_traffic($ret),
      'MemInfo' => self::get_meminfo($ret),
      'Tcp' => array(
        'Connections' => Detail\lookup($ret, 'NumTcpConnections'),
        'ConnectionsLocal' => Detail\lookup($ret, 'NumTcpConnectionsLocal'),
        'ConnectionsRemote' => Detail\lookup($ret, 'NumTcpConnectionsRemote'),
      ),
      'Hub' => array(
        'Total' => Detail\lookup($ret, 'NumHubTotal'),
        'Standalone' => Detail\lookup($ret, 'NumHubStandalone'),
        'Static' => Detail\lookup($ret, 'NumHubStatic'),
        'Dynamic' => Detail\lookup($ret, 'NumHubDynamic'),
      ),
      'Session' => array(
        'Total' => Detail\lookup($ret, 'NumSessionsTotal'),
        'Local' => Detail\lookup($ret, 'NumSessionsLocal'),
        'Remote' => Detail\lookup($ret, 'NumSessionsRemote'),
      ),
      'MacTables' => Detail\lookup($ret, 'NumMacTables'),
      'IpTables' => Detail\lookup($ret, 'NumIpTables'),
      'Users' => Detail\lookup($ret, 'NumUsers'),
      'Groups' => Detail\lookup($ret, 'NumGroups'),
      'CurrentTime' => Detail\lookup($ret, 'CurrentTime'),
      'CurrentTick' => Detail\lookup($ret, 'CurrentTick'),
      'StartTime' => Detail\lookup($ret, 'StartTime'),
      'Licenses' => array(
        'Bridge' => Detail\lookup($ret, 'AssignedBridgeLicenses'),
        'Client' => Detail\lookup($ret, 'AssignedClientLicenses'),
        'BridgeTotal' => Detail\lookup($ret, 'AssignedBridgeLicensesTotal'),
        'ClientTotal' => Detail\lookup($ret, 'AssignedClientLicensesTotal'),
      ),
    );
  }

  public function enum_listener() {
    $ret = $this->connection->call('EnumListener');

    $a = array();
    for ($i = 0; $i < count($ret['Enables']); ++$i) {
      $a[$i] = array(
        'Enabled' => (boolean)Detail\lookup($ret, 'Enables', $i),
        'Errors' => Detail\lookup($ret, 'Errors', $i),
        'Port' => Detail\lookup($ret, 'Ports', $i),
      );
    }
    return $a;
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

  public function get_hub($hubname) {
    $ret = $this->connection->call('GetHub', array(
      'HubName' => array(new Detail\String($hubname))));
    return array(
      'Name' => Detail\lookup($ret, 'HubName'),
      'Type' => Detail\lookup($ret, 'HubType'),
      'MaxSession' => Detail\lookup($ret, 'MaxSession'),
      'Online' => (boolean)Detail\lookup($ret, 'Online'),
    );
  }

  public function enum_hub() {
    $ret = $this->connection->call('EnumHub');
    $a = array();
    for ($i = 0; $i < count($ret['HubName']); ++$i) {
      $a[$i] = array(
        'Online' => (boolean)Detail\lookup($ret, 'Online', $i),
        'Name' => Detail\lookup($ret, 'HubName', $i),
        'Type' => Detail\lookup($ret, 'HubType', $i),
        'CreateTime' => Detail\lookup($ret, 'CreatedTime', $i),
        'LastCommunicateTime' => Detail\lookup($ret, 'LastCommTime', $i),
        'LastLoginTime' => Detail\lookup($ret, 'LastLoginTime', $i),
        'Login' => Detail\lookup($ret, 'NumLogin', $i),
        'Sessions' => Detail\lookup($ret, 'NumSessions', $i),
        'Users' => Detail\lookup($ret, 'NumUsers', $i),
        'Groups' => Detail\lookup($ret, 'NumGroups', $i),
        'MacTables' => Detail\lookup($ret, 'NumMacTables', $i),
        'IpTables' => Detail\lookup($ret, 'NumIpTables', $i),
      );
    }
    return $a;
  }

  public function get_hub_radius($hubname) {
    $ret = $this->connection->call('GetHubRadius', array(
      'HubName' => array(new Detail\String($hubname))));
    if (!Detail\lookup($ret, 'RadiusServerName')) {
      throw new VPNException('Radius server not configured');
    }
    return array(
      'Name' => $hubname, // server does not set correct value
      'ServerName' => Detail\lookup($ret, 'RadiusServerName'),
      'Port' => Detail\lookup($ret, 'RadiusPort'),
      'Secret' => Detail\lookup($ret, 'RadiusSecret'),
      'RetryInterval' => Detail\lookup($ret, 'RadiusRetryInterval'),
    );
  }

  public static function get_ip($pack, $i = 0) {
    return array(); // TODO: impl
  }

  public function enum_connection() {
    $ret = $this->connection->call('EnumConnection');

    $a = array();
    for ($i = 0; $i < count($ret['Hostname']); ++$i) {
      $a[$i] = array_merge(self::get_ip($ret, $i), array(
        'HostName' => Detail\lookup($ret, 'Hostname', $i),
        'CID' => Detail\lookup($ret, 'Name', $i),
        'Port' => Detail\lookup($ret, 'Port', $i),
        'Type' => Detail\lookup($ret, 'Type', $i),
        'ConnectedTime' => Detail\lookup($ret, 'ConnectedTime', $i),
      ));
    }
    return $a;
  }

  public function get_connection($cid) {
    $ret = $this->connection->call('GetConnectionInfo', array(
      'Name' => array(new Detail\String($cid))));
    return array_merge(self::get_ip($ret), array(
      'Port' => Detail\lookup($ret, 'Port'),
      'Type' => Detail\lookup($ret, 'Type'),
      'HostName' => Detail\lookup($ret, 'Hostname'),
      'ConnectedTime' => Detail\lookup($ret, 'ConnectedTime'),
      'Server' => array(
        'Name' => Detail\lookup($ret, 'ServerStr'),
        'Version' => Detail\lookup($ret, 'ServerVer'),
        'Build' => Detail\lookup($ret, 'ServerBuild'),
      ),
      'Client' => array(
        'Name' => Detail\lookup($ret, 'ClientStr'),
        'Version' => Detail\lookup($ret, 'ClientVer'),
        'Build' => Detail\lookup($ret, 'ClientBuild'),
      ),
    ));
  }

  public function get_hub_status($hubname) {
    $ret = $this->connection->call('GetHubStatus', array(
      'HubName' => array(new Detail\String($hubname))));
    return array(
      'Traffic' => self::get_traffic($ret),
      'Name' => Detail\lookup($ret, 'HubName'),
      'Type' => Detail\lookup($ret, 'HubType'),
      'Online' => (boolean)Detail\lookup($ret, 'Online'),
      'CreateTime' => Detail\lookup($ret, 'CreatedTime'),
      'LastCommunicateTime' => Detail\lookup($ret, 'LastCommTime'),
      'LastLoginTime' => Detail\lookup($ret, 'LastLoginTime'),
      'AccessLists' => Detail\lookup($ret, 'NumAccessLists'),
      'Users' => Detail\lookup($ret, 'NumUsers'),
      'Groups' => Detail\lookup($ret, 'NumGroups'),
      'Session' => array(
        'Sessions' => Detail\lookup($ret, 'NumSessions'),
        'BridgeSessions' => Detail\lookup($ret, 'NumSessionsBridge'),
        'ClientSessions' => Detail\lookup($ret, 'NumSessionsClient'),
      ),
      'IpTables' => Detail\lookup($ret, 'NumIpTables'),
      'MacTables' => Detail\lookup($ret, 'NumMacTables'),
      'Logins' => Detail\lookup($ret, 'NumLogin'),
      'SecureNAT' => (boolean)Detail\lookup($ret, 'SecureNATEnabled'),
    );
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
