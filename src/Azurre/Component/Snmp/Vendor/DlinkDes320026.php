<?php
/**
 * @date    04.02.2017
 * @version 0.1
 * @author  Aleksandr Milenin azrr.mail@gmail.com
 */

namespace Azurre\Component\Snmp\Vendor;


class DlinkDes320026 extends Dlink {

    protected $interfacesNamesOid = 'iso.3.6.1.2.1.47.1.1.1.1.2';

    /**
     * Get list of interfaces and VLAN's
     *
     * @param bool $addNumber
     * @param bool $addIndex
     *
     * @return array
     */
    public function getInterfaces($addNumber = false, $addIndex = false)
    {
        $list = array();
        for ($i = 1; $i <= 24; $i++) {
            $list[] = "100M Port {$i}";
        }

        for ($i = 1; $i <= 2; $i++) {
            $list[] = "1G Combo Port {$i}";
        }

        // Vlan's
        $list = array_merge($list, $this->getList('iso.3.6.1.2.1.17.7.1.4.3.1.1', false));

        $list[] = 'rif0';

        return $list;
    }


    public function getSystemName()
    {
        return 'D-Link';
    }

    public function getModel()
    {
        return 'DES-3200-26';
    }

    public function getVendorName()
    {
        return 'D-Link';
    }

}