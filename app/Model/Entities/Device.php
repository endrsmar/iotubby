<?php
declare(strict_types=1);

namespace Iotubby\Model\Entities;


use Iotubby\Helpers\IpValidator;
use Iotubby\Model\Enums\DeviceStatus;

class Device
{

    public const TABLE_NAME = 'device',
                 COLUMMN_ID = 'id',
                 COLUMN_NAME = 'name',
                 COLUMN_STATUS = 'status',
                 COLUMN_ADDRESS = 'address',
                 COLUMN_PORT = 'port',
                 COLUMN_CONSUMED_TOTAL = 'consumed_total',
                 COLUMN_SESSION_ID = 'session_id',
                 COLUMN_FAILED_ATTEMPTS = 'failed_attempts',
                 NAME_MAX_LENGHT = 255;

    /**
     * @var int|null
     */
    private $id;
    /**
     * @var string
     */
    private $name;
    /**
     * @var DeviceStatus
     */
    private $status;
    /**
     * @var string
     */
    private $address;
    /**
     * @var int
     */
    private $port;
    /**
     * @var float
     */
    private $consumedTotal;
    /**
     * @var int|null
     */
    private $sessionId;
    /**
     * @var int
     */
    private $failedAttempts;

    /**
     * @param int|null $id
     * @param string $name
     * @param string $address
     * @param int $port
     * @param DeviceStatus|null $status
     * @param float $consumedTotal
     * @param int|null $sessionId
     * @param int $failedAttempts
     */
    public function __construct(
        ?int $id,
        string $name,
        string $address,
        int $port,
        ?DeviceStatus $status = null,
        float $consumedTotal = 0.0,
        ?int $sessionId = null,
        int $failedAttempts = 0
    ) {
        $this->id = $id;
        $this->setName($name)
            ->setAddress($address)
            ->setPort($port);
        $this->status = $status ?: DeviceStatus::OFFLINE();
        $this->consumedTotal = $consumedTotal;
        $this->sessionId = $sessionId;
        $this->failedAttempts = $failedAttempts;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Device
     */
    public function setId(int $id): Device
    {
        if ($this->id) {
            throw new \Exception('Entity already has id');
        }
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Device
     */
    public function setName(string $name): Device
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('Name must not be empty');
        } elseif (strlen($name) > self::NAME_MAX_LENGHT) {
            throw new \InvalidArgumentException('Name can be maximum of '.self::NAME_MAX_LENGHT.' characters long');
        }
        $this->name = $name;
        return $this;
    }

    /**
     * @return DeviceStatus
     */
    public function getStatus(): DeviceStatus
    {
        return $this->status;
    }

    /**
     * @return Device
     */
    public function setOffline(): Device
    {
        $this->sessionId = null;
        $this->resetFailedAttempts();
        $this->status = DeviceStatus::OFFLINE();
        return $this;
    }

    /**
     * @return bool
     */
    public function isOffline(): bool
    {
        return $this->status->equals(DeviceStatus::OFFLINE());
    }

    /**
     * @return Device
     */
    public function setOff(): Device
    {
        $this->sessionId = null;
        $this->status = DeviceStatus::OFF();
        return $this;
    }

    /**
     * @return bool
     */
    public function isOff(): bool
    {
        return $this->status->equals(DeviceStatus::OFF());
    }

    /**
     * @param int $sessionId
     * @return Device
     */
    public function setOn(int $sessionId): Device
    {
        $this->sessionId = $sessionId;
        $this->status = DeviceStatus::ON();
        return $this;
    }

    /**
     * @return bool
     */
    public function isOn(): bool
    {
        return $this->status->equals(DeviceStatus::ON());
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     * @return Device
     */
    public function setAddress(string $address): Device
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @param int $port
     * @return Device
     */
    public function setPort(int $port): Device
    {
        if ($port < 0) {
            throw new \InvalidArgumentException('Port cannot be a negative number');
        }
        $this->port = $port;
        return $this;
    }

    /**
     * @return float
     */
    public function getConsumedTotal(): float
    {
        return $this->consumedTotal;
    }

    /**
     * @param float $consumedTotal
     * @return Device
     */
    public function setConsumedTotal(float $consumedTotal): Device
    {
        if ($consumedTotal < 0) {
            throw new \InvalidArgumentException('Total consumed cannot be a negative number');
        }
        $this->consumedTotal = $consumedTotal;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSessionId(): ?int
    {
        return $this->sessionId;
    }

    /**
     * @return int
     */
    public function getFailedAttempts(): int
    {
        return $this->failedAttempts;
    }

    /**
     * @return Device
     */
    public function incrementFailedAttempts(): Device
    {
        $this->failedAttempts += 1;
        return $this;
    }

    /**
     * @return Device
     */
    public function resetFailedAttempts(): Device
    {
        $this->failedAttempts = 0;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::COLUMN_NAME => $this->name,
            self::COLUMN_STATUS => $this->status->getValue(),
            self::COLUMN_ADDRESS => $this->address,
            self::COLUMN_PORT => $this->port,
            self::COLUMN_SESSION_ID => $this->sessionId,
            self::COLUMN_CONSUMED_TOTAL => $this->consumedTotal,
            self::COLUMN_FAILED_ATTEMPTS => $this->failedAttempts
        ];
    }

}