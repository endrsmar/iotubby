<?php
declare(strict_types=1);

namespace Iotubby\Model;


use DateTime;
use Iotubby\Model\Entities\Device;
use Iotubby\Model\Entities\Measurement;
use Iotubby\Model\Entities\Session;

interface MeasurementRepository
{

    /**
     * @param Device $device
     * @param DateTime|null $since
     * @param DateTime|null $until
     * @return Measurement[]
     */
    public function getMeasurementsForDevice(Device $device, ?DateTime $since = null, ?DateTime $until = null): array;

    /**
     * @param int $sessionId
     * @return Measurement|null
     */
    public function getLastMeasurementInSession(int $sessionId): ?Measurement;

    /**
     * @param Device $measurement
     */
    public function save(Measurement $measurement): void;

    /**
     * @param Measurement[] $measurements
     */
    public function saveBulk(array $measurements): void;

}