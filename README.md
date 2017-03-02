# The simple library for comfortable using SNMP

# Usage

```php
$loader = require_once __DIR__ . '/vendor/autoload.php';

$host = '10.90.90.90';

echo '<pre>';
$SnmpClient = new \Azurre\Component\Snmp\Client($host, 'public', SNMP::VERSION_2c);
$SnmpClient->autodetect(); // You can try to detect vendor automatically
//$SnmpClient->setVendor('Dlink-DES-3200-26'); // or set it manually

echo 'System:' . $SnmpClient->getSystem() . '\n';
echo 'System name: ' . $SnmpClient->getSystemName() . '\n';
echo 'Uptime: ' . $SnmpClient->getUptime() . '\n';
echo 'Uptime (s) ' . $SnmpClient->getUptime(false) . '\n';
echo 'Model: ' . $SnmpClient->getModel() . '\n';
echo 'Vendor: ' . $SnmpClient->getVendorName() . '\n';

$interfaces = $SnmpClient->getInterfaces();

echo "Interfaces list:\n";
print_r( $interfaces );

echo "Interfaces status:\n";
print_r( array_combine($interfaces, $SnmpClient->getInterfacesStatus(true)) );

echo "Interfaces speed:\n";
print_r( array_combine($interfaces, $SnmpClient->getInterfacesSpeed()) );

echo "Interfaces RX:\n";
print_r( array_combine($interfaces, $SnmpClient->getInterfacesRx()) );

echo "Interfaces TX:\n";
print_r( array_combine($interfaces, $SnmpClient->getInterfacesTx()) );

echo "Interfaces MAC's:\n";
print_r( array_combine($interfaces, $SnmpClient->getInterfacesMacs()));

echo "FDB list:\n";
print_r( $SnmpClient->getFdbList() );

echo "Device driver method list:\n";
print_r( $SnmpClient->getMethodsAvailable() );

echo 'Not supported method: ' . $SnmpClient->getMoney() . '\n';
```


Output
```
System: D-Link DES-3200-26 Fast Ethernet Switch 
System name: D-Link 
Uptime: 0d 0h 3m 44s 
Uptime (s): 224 
Model: DES-3200-26 
Vendor: D-Link 

Interfaces list:
Array
(
    [0] => 100M Port 1
    [1] => 100M Port 2
    [2] => 100M Port 3
    [3] => 100M Port 4
    [4] => 100M Port 5
    [5] => 100M Port 6
    [6] => 100M Port 7
    [7] => 100M Port 8
    [8] => 100M Port 9
    [9] => 100M Port 10
    [10] => 100M Port 11
    [11] => 100M Port 12
    [12] => 100M Port 13
    [13] => 100M Port 14
    [14] => 100M Port 15
    [15] => 100M Port 16
    [16] => 100M Port 17
    [17] => 100M Port 18
    [18] => 100M Port 19
    [19] => 100M Port 20
    [20] => 100M Port 21
    [21] => 100M Port 22
    [22] => 100M Port 23
    [23] => 100M Port 24
    [24] => 1G Combo Port 1
    [25] => 1G Combo Port 2
    [26] => default
    [27] => test-vlan
    [28] => new-vlan
    [29] => rif0
)

Interfaces status:
Array
(
    [100M Port 1] => 1
    [100M Port 2] => 2
    [100M Port 3] => 1
    [100M Port 4] => 2
    [100M Port 5] => 2
    [100M Port 6] => 2
    [100M Port 7] => 2
    [100M Port 8] => 2
    [100M Port 9] => 2
    [100M Port 10] => 2
    [100M Port 11] => 2
    [100M Port 12] => 2
    [100M Port 13] => 2
    [100M Port 14] => 2
    [100M Port 15] => 2
    [100M Port 16] => 2
    [100M Port 17] => 2
    [100M Port 18] => 2
    [100M Port 19] => 2
    [100M Port 20] => 2
    [100M Port 21] => 2
    [100M Port 22] => 2
    [100M Port 23] => 2
    [100M Port 24] => 2
    [1G Combo Port 1] => 2
    [1G Combo Port 2] => 2
    [default] => 1
    [test-vlan] => 1
    [new-vlan] => 1
    [rif0] => 1
)

Interfaces speed:
Array
(
    [100M Port 1] => 100
    [100M Port 2] => 0
    [100M Port 3] => 100
    [100M Port 4] => 0
    [100M Port 5] => 0
    [100M Port 6] => 0
    [100M Port 7] => 0
    [100M Port 8] => 0
    [100M Port 9] => 0
    [100M Port 10] => 0
    [100M Port 11] => 0
    [100M Port 12] => 0
    [100M Port 13] => 0
    [100M Port 14] => 0
    [100M Port 15] => 0
    [100M Port 16] => 0
    [100M Port 17] => 0
    [100M Port 18] => 0
    [100M Port 19] => 0
    [100M Port 20] => 0
    [100M Port 21] => 0
    [100M Port 22] => 0
    [100M Port 23] => 0
    [100M Port 24] => 0
    [1G Combo Port 1] => 0
    [1G Combo Port 2] => 0
    [default] => 0
    [test-vlan] => 0
    [new-vlan] => 0
    [rif0] => 0
)

Interfaces RX:
Array
(
    [100M Port 1] => 85396
    [100M Port 2] => 0
    [100M Port 3] => 116520
    [100M Port 4] => 0
    [100M Port 5] => 0
    [100M Port 6] => 0
    [100M Port 7] => 0
    [100M Port 8] => 0
    [100M Port 9] => 0
    [100M Port 10] => 0
    [100M Port 11] => 0
    [100M Port 12] => 0
    [100M Port 13] => 0
    [100M Port 14] => 0
    [100M Port 15] => 0
    [100M Port 16] => 0
    [100M Port 17] => 0
    [100M Port 18] => 0
    [100M Port 19] => 0
    [100M Port 20] => 0
    [100M Port 21] => 0
    [100M Port 22] => 0
    [100M Port 23] => 0
    [100M Port 24] => 0
    [1G Combo Port 1] => 0
    [1G Combo Port 2] => 0
    [default] => 0
    [test-vlan] => 0
    [new-vlan] => 0
    [rif0] => 0
)

Interfaces TX:
Array
(
    [100M Port 1] => 109815
    [100M Port 2] => 0
    [100M Port 3] => 92945
    [100M Port 4] => 0
    [100M Port 5] => 0
    [100M Port 6] => 0
    [100M Port 7] => 0
    [100M Port 8] => 0
    [100M Port 9] => 0
    [100M Port 10] => 0
    [100M Port 11] => 0
    [100M Port 12] => 0
    [100M Port 13] => 0
    [100M Port 14] => 0
    [100M Port 15] => 0
    [100M Port 16] => 0
    [100M Port 17] => 0
    [100M Port 18] => 0
    [100M Port 19] => 0
    [100M Port 20] => 0
    [100M Port 21] => 0
    [100M Port 22] => 0
    [100M Port 23] => 0
    [100M Port 24] => 0
    [1G Combo Port 1] => 0
    [1G Combo Port 2] => 0
    [default] => 0
    [test-vlan] => 0
    [new-vlan] => 0
    [rif0] => 0
)

Interfaces MACs:
Array
(
    [100M Port 1] => 34:08:04:41:c0:00
    [100M Port 2] => 34:08:04:41:c0:00
    [100M Port 3] => 34:08:04:41:c0:00
    [100M Port 4] => 34:08:04:41:c0:00
    [100M Port 5] => 34:08:04:41:c0:00
    [100M Port 6] => 34:08:04:41:c0:00
    [100M Port 7] => 34:08:04:41:c0:00
    [100M Port 8] => 34:08:04:41:c0:00
    [100M Port 9] => 34:08:04:41:c0:00
    [100M Port 10] => 34:08:04:41:c0:00
    [100M Port 11] => 34:08:04:41:c0:00
    [100M Port 12] => 34:08:04:41:c0:00
    [100M Port 13] => 34:08:04:41:c0:00
    [100M Port 14] => 34:08:04:41:c0:00
    [100M Port 15] => 34:08:04:41:c0:00
    [100M Port 16] => 34:08:04:41:c0:00
    [100M Port 17] => 34:08:04:41:c0:00
    [100M Port 18] => 34:08:04:41:c0:00
    [100M Port 19] => 34:08:04:41:c0:00
    [100M Port 20] => 34:08:04:41:c0:00
    [100M Port 21] => 34:08:04:41:c0:00
    [100M Port 22] => 34:08:04:41:c0:00
    [100M Port 23] => 34:08:04:41:c0:00
    [100M Port 24] => 34:08:04:41:c0:00
    [1G Combo Port 1] => 34:08:04:41:c0:00
    [1G Combo Port 2] => 34:08:04:41:c0:00
    [default] => 00:00:00:00:00:00
    [test-vlan] => 00:00:00:00:00:00
    [new-vlan] => 00:00:00:00:00:00
    [rif0] => 34:08:04:41:c0:00
)

FDB list:
Array
(
    [0] => Array
        (
            [port] => 1
            [vlan] => 1
            [mac] => 00:1a:79:2c:76:00
        )

    [1] => Array
        (
            [port] => 3
            [vlan] => 1
            [mac] => 04:8d:38:b8:ba:00
        )

    <-- CUTTED -->

)

Device driver method list:
Array
(
    [0] => get
    [1] => getCommunity
    [2] => getFdbList
    [3] => getHost
    [4] => getInterfaces
    [5] => getInterfacesMacs
    [6] => getInterfacesRx
    [7] => getInterfacesSpeed
    [8] => getInterfacesStatus
    [9] => getInterfacesTx
    [10] => getList
    [11] => getModel
    [12] => getSystem
    [13] => getSystemName
    [14] => getTimeout
    [15] => getUptime
    [16] => getVendorName
    [17] => getVersion
    [18] => set
)

Not supported method: Method 'getMoney' is not supported on this device 
```
