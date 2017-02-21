<?php
/**
 * @date    21.02.2017
 * @version 0.1
 * @author  Aleksandr Milenin azrr.mail@gmail.com
 */

namespace Azurre\Component\Snmp\Vendor;


class MikrotikRb260gs extends Mikrotik{

    public function getInterfaces($addNumber = false, $addIndex = false)
    {
        return parent::getInterfaces($addNumber, $addIndex);
    }

    public function getFdbList()
    {
        return __FUNCTION__ . ' not supported';
    }

}