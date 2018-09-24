<?php
declare(strict_types=1);

namespace Iotubby\Model;

use Iotubby\Model\Entities\Device;

interface DeviceRepository
{

    /**
     * @param int $deviceId
     * @return Device|null
     */
    public function getDevice(int $deviceId): ?Device;

    /**
     * @return Device[]
     */
    public function getDevices(): array;

    /**
     * @param Device $device
     */
    public function save(Device $device): void;

    /**
     * @param Device $device
     */
    public function delete(Device $device): void;


}