<?php
declare(strict_types=1);

namespace Iotubby\Model;


use DateTime;
use Iotubby\Model\Entities\Device;
use Iotubby\Model\Entities\Measurement;
use Iotubby\Model\Entities\Session;
use Nette\Database\Context;
use Nette\Database\Table\ActiveRow;

class MysqlMeasurementRepository implements MeasurementRepository
{

    /**
     * @var Context
     */
    private $database;

    /**
     * @param Context $database
     */
    public function __construct(Context $database)
    {
        $this->database = $database;
    }

    /**
     * @param Device $device
     * @param DateTime|null $since
     * @param DateTime|null $until
     * @return Measurement[]
     */
    public function getMeasurementsForDevice(Device $device, ?DateTime $since = null, ?DateTime $until = null): array
    {
        $res = [];
        $selection = $this->database->table(Measurement::TABLE_NAME)->where(Measurement::DEVICE_ID_COLUMN, $device->getId());
        if ($since) {
            $selection->where(Measurement::TIME_COLUMN.' >= ?', $since);
        }
        if ($until) {
            $selection->where(Measurement::TIME_COLUMN. ' <= ?', $until);
        }
        foreach ($selection->order(Measurement::TIME_COLUMN.' ASC') as $row) {
            $res[] = $this->rowToEntity($row);
        }
        return $res;
    }

    /**
     * @param int $sessionId
     * @return Session|null
     */
    public function getLastMeasurementInSession(int $sessionId): ?Measurement
    {
        $row = $this->database->table(Measurement::TABLE_NAME)
                    ->where(Measurement::COLUMN_SESSION_ID, $sessionId)
                    ->order(Measurement::TIME_COLUMN.' DESC')
                    ->limit(1)
                    ->fetch();
        if (!$row) {
            return null;
        }
        return $this->rowToEntity($row);
    }

    /**
     * @param Measurement $measurement
     */
    public function save(Measurement $measurement): void
    {
        $this->saveBulk([$measurement]);
    }

    /**
     * @param Measurement[] $measurements
     */
    public function saveBulk(array $measurements): void
    {
        $insertData = [];
        foreach ($measurements as $measurement)
        {
            if ($measurement->getId()) {
                throw new \InvalidArgumentException('Measurement is immutable, cannot update it');
            }
            $insertData[] = $measurement->toArray();
        }
        if (!empty($insertData)) {
            $this->database->table(Measurement::TABLE_NAME)->insert($insertData);
        }
    }

    /**
     * @param ActiveRow $row
     * @return Measurement
     */
    private function rowToEntity(ActiveRow $row): Measurement
    {
        return new Measurement(
            $row->{Measurement::ID_COLUMN},
            $row->{Measurement::DEVICE_ID_COLUMN},
            $row->{Measurement::TIME_COLUMN},
            $row->{Measurement::VALUE_COLUMN},
            $row->{Measurement::COLUMN_SESSION_ID}
        );
    }

}