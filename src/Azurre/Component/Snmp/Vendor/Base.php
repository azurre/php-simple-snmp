<?php
/**
 * @date    26.10.2016
 * @version 0.1
 * @author  Aleksandr Milenin azrr.mail@gmail.com
 */

namespace Azurre\Component\Snmp\Vendor;

/**
 * Basic functions for the most cases
 */
class Base {

    /**
     * Types of OID
     */
    const
        TYPE_INT   = 'INT',
        TYPE_FLOAT = 'FLOAT',
        TYPE_BOOL  = 'BOOL',
        TYPE_MAC   = 'MAC';

    protected $host, $community, $version;

    /**
     * @var int Connection timeout (seconds)
     */
    protected $timeout = 1;

    /**
     * @var int The number of times to retry if timeouts occur
     */
    protected $retry = 1;

    /**
     * @var string OID for autodetect SNMP version
     */
    protected $detectOid           = 'iso.3.6.1.2.1.1.1.0';
    protected $systemOid           = 'iso.3.6.1.2.1.1.1.0';
    protected $systemNameOid       = 'iso.3.6.1.2.1.1.5.0';
    protected $uptimeOid           = 'iso.3.6.1.2.1.1.3.0';
    protected $interfacesNamesOid  = 'iso.3.6.1.2.1.2.2.1.2';
    protected $interfacesSpeedOid  = 'iso.3.6.1.2.1.2.2.1.5';
    protected $interfacesMacsOid   = 'iso.3.6.1.2.1.2.2.1.6';
    protected $interfacesStatusOid = 'iso.3.6.1.2.1.2.2.1.8';
    protected $interfacesRxOid     = 'iso.3.6.1.2.1.2.2.1.10';
    protected $interfacesTxOid     = 'iso.3.6.1.2.1.2.2.1.16';
    protected $fdbOid              = 'iso.3.6.1.2.1.17.7.1.2.2.1.2';

    /**
     * Base constructor
     *
     * @param string $host      SNMP host
     * @param string $community SNMP community ("password" for SNMP version 1/2)
     * @param string $version   Version of SNMP protocol
     */
    public function __construct($host, $community, $version)
    {
        $this->host      = $host;
        $this->community = $community;
        $this->version   = $version ? $version : $this->detectVersion();
    }

    /**
     * Get device system description (model)
     *
     * @return string
     */
    public function getSystem()
    {
        $parts = explode(PHP_EOL, $this->get($this->systemOid));
        $desc  = reset($parts);

        return trim($desc);
    }

    /**
     * Get system name
     *
     * @return string
     */
    public function getSystemName()
    {
        return $this->get($this->systemNameOid);
    }

    /**
     * Get device uptime
     *
     * @param string|false $format
     *
     * @return string|int Date in $format or number of seconds if $format is empty
     */
    public function getUptime($format = '%ad %hh %im %ss')
    {
        $uptime = $this->get($this->uptimeOid);
        $uptime = round($uptime / 100);

        if (empty($format)) {
            return $uptime;
        }

        $ZeroTime = new \DateTime('@0');
        $Uptime   = new \DateTime("@$uptime");

        return $ZeroTime->diff($Uptime)->format($format);
    }

    /**
     * Get list of interfaces
     *
     * @param bool $addNumber
     * @param bool $addIndex
     *
     * @return array
     */
    public function getInterfaces($addNumber = true, $addIndex = true)
    {
        $list = $this->getList($this->interfacesNamesOid, false);

        if (!$addIndex) {
            return $list;
        }

        $names = [];
        foreach ($list as &$item) {
            if (!isset($names[ $item ])) {
                $names[ $item ] = 1;
            }
            $key = $names[ $item ]++;
            $item .= $addNumber ? "({$key})" : '';
        }

        return $list;
    }

    /**
     * Interfaces status
     *
     * @param bool $raw
     *
     * @return array
     */
    public function getInterfacesStatus($raw = false)
    {
        $list = $this->getList($this->interfacesStatusOid, false);
        if ($raw) {
            return $list;
        }

        foreach ($list as &$item) {
            $item = $item == 1 ? 1 : 0;
        }

        return $this->cast($list, static::TYPE_BOOL);
    }

    /**
     * The total number of octets received on the interface, including framing characters
     *
     * @return array
     */
    public function getInterfacesRx()
    {
        return $this->getList($this->interfacesRxOid, false);
    }

    /**
     * The total number of octets transmitted out of the interface, including framing characters
     *
     * @return array
     */
    public function getInterfacesTx()
    {
        return $this->getList($this->interfacesTxOid, false);
    }

    /**
     * Speed of interfaces (Mbps)
     *
     * @return array
     */
    public function getInterfacesSpeed()
    {
        $list = $this->getList($this->interfacesSpeedOid, false);
        foreach ($list as &$item) {
            $item = round($item / 1000000);
        }

        return $list;
    }

    /**
     * Get mac addresses of interfaces
     *
     * @return array
     */
    public function getInterfacesMacs()
    {
        $list = $this->getList($this->interfacesMacsOid, false);
        $list = $this->cast($list, static::TYPE_MAC);

        return $list;
    }

    /**
     * Get Forwarding Data Base of device
     *
     * @return array
     */
    public function getFdbList()
    {
        $list = $this->getList($this->fdbOid, true);
        if (!$list) {
            return false;
        }

        $ports = [];
        foreach ($list as $path => $port) {
            $path  = str_replace("{$this->fdbOid}.", '', $path);
            $parts = explode('.', $path);
            $vlan  = $parts[0];
            unset($parts[0]);
            $parts = array_map(function ($item) {
                return str_pad(dechex($item), 2, '0', STR_PAD_LEFT);
            }, $parts);

            $mac = implode(':', $parts);

            $ports[] = [
                'port' => $port,
                'vlan' => $vlan,
                'mac'  => $mac
            ];
        }

        return $ports;
    }

    /**
     * Get info by OID (snmp_get)
     *
     * @param string $oid
     * @param string $version
     *
     * @return mixed
     */
    public function get($oid, $version = null)
    {
        $version  = $version ? $version : $this->version;
        $response = null;
        switch ($version) {
            default:
                $response = @snmpget($this->host, $this->community, $oid, $this->getTimeout(), $this->retry);
                break;

            case '2c':
                $response = @snmp2_get($this->host, $this->community, $oid, $this->getTimeout(), $this->retry);
                break;

            case '3':
                //@todo To implement snmp v3
//                $response = @snmp3_get($this->host, $this->community, $oid, $this->getTimeout(), $this->retry);
                break;

        }

        return $this->parse($response);
    }

    /**
     * Get info by OID (snmp_walk)
     *
     * @param string $oid
     * @param bool   $withOids true - keys of array will be OID
     * @param bool   $raw      Return raw data
     *
     * @return array
     */
    public function getList($oid = '', $withOids = true, $raw = false)
    {
        //@todo To implement snmp v3
        switch ($this->version) {
            default:
                if ($withOids) {
                    $response = @snmprealwalk($this->host, $this->community, $oid, $this->getTimeout(), $this->retry);
                } else {
                    $response = @snmpwalk($this->host, $this->community, $oid, $this->getTimeout(), $this->retry);
                }
                break;

            case '2c':
                if ($withOids) {
                    $response = @snmp2_real_walk($this->host, $this->community, $oid, $this->getTimeout(), $this->retry);
                } else {
                    $response = @snmp2_walk($this->host, $this->community, $oid, $this->getTimeout(), $this->retry);
                }
                break;
        }

        if (!$response) {
            return [];
        }

        if ($raw) {
            return $response;
        }

        foreach ($response as &$item) {
            $item = $this->parse($item);
        }

        return $response;
    }

    /**
     * Get SNMP connection timeout
     *
     * @param bool $sec true - timeout in seconds, false - timeout in microseconds
     *
     * @return int
     */
    public function getTimeout($sec = false)
    {
        if ($sec) {
            return $this->timeout;
        }

        return $this->timeout * 1000000;
    }

    /**
     * @todo Implement snmp_set
     *
     * @param $oid
     * @param $data
     */
    public function set($oid, $data)
    {

    }

    /**
     * Detect SNMP version
     * @todo To implement snmp v3
     *
     * @param bool $forceDetect
     *
     * @return string
     */
    public function detectVersion($forceDetect = false)
    {
        if (!$this->version || $forceDetect) {
            if ($this->get($this->detectOid, '2c')) {
                $this->version = '2c';
            } else if ($this->get($this->detectOid, '1')) {
                $this->version = '1';
            }
        }

        return $this->version;
    }

    /**
     * Parse SNMP response string
     *
     * @param $snmpString
     *
     * @return mixed
     */
    public function parse($snmpString)
    {
        if (empty($snmpString) || $snmpString === '""') {
            return '';
        }

        if (!preg_match('/^([\w\-]+): (.*?)$/is', $snmpString, $matches)) {
            return $snmpString;
        }

        $type  = strtoupper($matches[1]);
        $value = $matches[2];

        switch ($type) {
            case 'STRING':
            case 'OID':
            case 'IPADDRESS':
                return trim($value, '"');
            break;

            case 'INTEGER':
            case 'GAUGE32':
            case 'GAUGE64':
            case 'COUNTER32':
            case 'COUNTER64':
                $value = preg_replace('/[^\d]/', '', $value);

                return $this->cast($value, static::TYPE_INT);
            break;

            case 'HEX-STRING':
                return preg_replace('/[^a-f0-9]/i', '', $value);
            break;

            case 'TIMETICKS':
                $ticks = substr($value, 1, strrpos($value, ')') - 1);

                return $this->cast($ticks, static::TYPE_INT);
            break;

            default:
                //@todo log
            break;
        }

        return $value;
    }

    /**
     * Cast data type
     *
     * @param array|string $data
     * @param string       $type
     *
     * @return mixed
     */
    protected function cast($data, $type)
    {
        if (is_array($data)) {
            foreach ($data as &$item) {
                $item = $this->{__FUNCTION__}($item, $type);
            }

            return $data;
        }

        switch (strtoupper($type)) {
            case 'INT':
                return ($data <= PHP_INT_MAX) ? (int)$data : preg_replace('/[^\d]/', '', (string)$data);
            break;

            case 'FLOAT':
                return (float)$data;
            break;

            case 'BOOL':
                return (bool)$data;
            break;

            case 'MAC':
                return strtolower(wordwrap(preg_replace('/[^a-f0-9]/i', '', $data), 2, ':', 1));
            break;
        }

        return $data;
    }

    /**
     * Get device vendor
     *
     * @return string
     */
    public function getVendor()
    {
        return __FUNCTION__ . ' not supported';
    }

    /**
     * Get device model
     *
     * @return string
     */
    public function getModel()
    {
        return __FUNCTION__ . ' not supported';
    }

    /**
     * Get SNMP Host
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Get SNMP community string (password for v1/v2c)
     *
     * @return string
     */
    public function getCommunity()
    {
        return $this->community;
    }

    /**
     * Get SNMP version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

}