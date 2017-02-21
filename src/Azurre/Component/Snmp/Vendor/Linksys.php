<?php
/**
 * @date    03.02.2017
 * @version 0.1
 * @author  Aleksandr Milenin azrr.mail@gmail.com
 */

namespace Azurre\Component\Snmp\Vendor;


class Linksys extends Base{

    protected $interfacesNamesOid = 'iso.3.6.1.2.1.31.1.1.1.1';

    /**
     * Get list of interfaces
     *
     * @param bool $addNumber
     * @param bool $addIndex
     *
     * @return array
     */
    public function getInterfaces($addNumber = false, $addIndex = true)
    {
        $list = parent::getInterfaces($addNumber, $addIndex);
        if ($list) {
            foreach ($list as &$iface){
                $iface = is_numeric($iface) ? "vlan {$iface}" : $iface;
            }
        }

        return $list;
    }

}