<?php
declare(strict_types=1);

namespace Iotubby\Model\Enums;


use MyCLabs\Enum\Enum;

/**
 * @method static DeviceStatus OFFLINE()
 * @method static DeviceStatus OFF()
 * @method static DeviceStatus ON()
 */
class DeviceStatus extends Enum
{

    const OFFLINE = 0;
    const OFF = 1;
    const ON = 2;

}