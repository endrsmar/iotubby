<?php
declare(strict_types=1);

namespace Iotubby\Model;


use Iotubby\Model\Entities\Device;
use Iotubby\Model\Enums\DeviceStatus;
use Nette\Database\Context;
use Nette\Database\Table\ActiveRow;

class MysqlDeviceRepository implements DeviceRepository
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
     * @return Device[]
     */
    public function getDevices(): array
    {
        $res = [];
        foreach ($this->database->table(Device::TABLE_NAME) as $row) {
            $res[] = $this->rowToEntity($row);
        }
        return $res;
    }

    /**
     * @param int $id
     * @return Device|null
     */
    public function getDevice(int $id): ?Device
    {
        if (!($row = $this->database->table(Device::TABLE_NAME)->where(Device::COLUMMN_ID, $id)->fetch())) {
            return null;
        }
        return $this->rowToEntity($row);
    }

    /**
     * @param Device $device
     */
    public function save(Device $device): void
    {
        if (!is_null($device->getId())) {
            $this->database->table(Device::TABLE_NAME)->where(Device::COLUMMN_ID, $device->getId())->update($device->toArray());
        } else {
            $row = $this->database->table(Device::TABLE_NAME)->insert($device->toArray());
            $device->setId($row->id);
        }
    }

    /**
     * @param Device $device
     */
    public function delete(Device $device): void
    {
        if (is_null($device->getId())) {
            return;
        }
        $this->database->table(Device::TABLE_NAME)->where(Device::COLUMMN_ID, $device->getId())->delete();
    }

    /**
     * @param ActiveRow $row
     * @return Device
     */
    private function rowToEntity(ActiveRow $row): Device
    {
        return new Device(
            $row->{Device::COLUMMN_ID},
            $row->{Device::COLUMN_NAME},
            $row->{Device::COLUMN_ADDRESS},
            $row->{Device::COLUMN_PORT},
            new DeviceStatus($row->{Device::COLUMN_STATUS}),
            $row->{Device::COLUMN_CONSUMED_TOTAL},
            $row->{Device::COLUMN_SESSION_ID},
            $row->{Device::COLUMN_FAILED_ATTEMPTS}
        );
    }


}