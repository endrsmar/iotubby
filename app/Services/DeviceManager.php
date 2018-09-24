<?php
declare(strict_types=1);

namespace Iotubby\Services;


use Iotubby\Model\Entities\Device;

interface DeviceManager
{

    /**
     * @param Device $device
     */
    public function turnOn(Device $device): void;

    /**
     * @param Device $device
     */
    public function turnOff(Device $device): void;

    /**
     * @param Device $device
     */
    public function pullMeasurements(Device $device): void;

}