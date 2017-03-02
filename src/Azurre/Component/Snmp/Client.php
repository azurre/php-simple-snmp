<?php
/**
 * @date    26.10.2016
 * @version 0.1
 * @author  Aleksandr Milenin azrr.mail@gmail.com
 */

namespace Azurre\Component\Snmp;


use Azurre\Component\Snmp\Vendor\Base as BaseVendor;

/**
 * Class Client
 * @package Azurre\Component\Snmp
 *
 * @method string getVendorName()
 * @method string getModel()
 * @method string getSystem()
 * @method string getSystemName()
 * @method int|string getUptime($format = '%ad %hh %im %ss')
 * @method array getInterfacesRx()
 * @method array getInterfacesTx()
 * @method array getInterfacesSpeed()
 * @method array getInterfaces()
 * @method array getInterfacesStatus($raw = false)
 * @method array getInterfacesMacs()
 * @method array getFdbList()
 * @method array get($oid, $version = null)
 * @method array getList($oid = '', $withOids = true, $raw = false)
 */
class Client {

    /** @var BaseVendor */
    protected $vendorInstance;
    protected $vendor;

    protected $tryDetectVendor = true;

    public function __construct($host, $community = 'public', $version = null)
    {
        // Init base vendor
        $this->vendorInstance = new BaseVendor($host, $community, $version);
    }

    /**
     * @param string $method
     * @param array  $args
     *
     * @return mixed|string
     */
    public function __call($method, $args)
    {
        if (is_callable([$this->vendorInstance, $method])) {
            return call_user_func_array([$this->vendorInstance, $method], $args);
        }

        return "Method '{$method}' is not supported on this device";
    }

    /**
     * @param string $vendor
     *
     * @return $this
     * @throws \Exception
     */
    public function setVendor($vendor)
    {
        $this->vendorInstance = $this->getVendorInstance($vendor);

        return $this;
    }

    /**
     * @param string $vendor
     *
     * @return \Azurre\Component\Snmp\Vendor\Base
     * @throws \Exception
     */
    protected function getVendorInstance($vendor)
    {
        $vendorClass = __NAMESPACE__ . '\Vendor\\' . static::toCamelCase($vendor,'-', true);
        if (class_exists($vendorClass)) {
            return new $vendorClass($this->vendorInstance->getHost(), $this->vendorInstance->getCommunity(), $this->vendorInstance->getVersion());
        }

        throw new \Exception('Vendor not found');
    }


    /**
     * Try to detect device by system OID
     *
     * @return bool
     * @throws \Exception
     */
    public function autodetect()
    {
        $system = $this->vendorInstance->getSystem();
        $patterns   = require(__DIR__ . '/Vendor/patterns.php');

        foreach ($patterns as $pattern => $vendorClass) {
            if (preg_match($pattern, $system)) {
                $this->vendorInstance = $this->getVendorInstance($vendorClass);

                return true;
            }
        }

        return false;
    }

    /**
     * @param string $string
     * @param string $separator
     * @param bool   $firstCharCaps
     *
     * @return string
     */
    public static function toCamelCase($string, $separator = '-', $firstCharCaps = false)
    {
        $words = explode($separator, strtolower($string));

        $return = '';
        foreach ($words as $word) {
            $return .= ucfirst($word);
        }

        return $firstCharCaps ? ucfirst($return) : lcfirst($return);
    }

    /**
     * Return available methods
     *
     * @return array
     */
    public function getMethodsAvailable()
    {
        $list = get_class_methods($this->vendorInstance);
        $list = array_filter($list, function($method){
            return is_callable([$this->vendorInstance, $method]) && $method !== '__construct';
        });
        sort($list);

        return $list;
    }
}