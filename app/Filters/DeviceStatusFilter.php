<?php
declare(strict_types=1);

namespace Iotubby\Filters;


use Iotubby\Model\Enums\DeviceStatus;

class DeviceStatusFilter
{

    public function __invoke(DeviceStatus $status)
    {
        if ($status->equals(DeviceStatus::OFFLINE())) {
            return 'Offline';
        } elseif ($status->equals(DeviceStatus::OFF())) {
            return 'Off';
        } else {
            return 'On';
        }
    }

}