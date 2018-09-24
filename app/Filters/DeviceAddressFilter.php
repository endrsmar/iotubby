<?php
declare(strict_types=1);

namespace Iotubby\Filters;


use Iotubby\Model\Entities\Device;

class DeviceAddressFilter
{

    public function __invoke(Device $device)
    {
        return $device->getAddress().':'.$device->getPort();
    }

}