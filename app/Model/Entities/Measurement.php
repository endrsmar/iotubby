<?php
declare(strict_types=1);

namespace Iotubby\Model\Entities;


class Measurement implements Entity
{

    public const TABLE_NAME = 'measurement',
                 ID_COLUMN = 'id',
                 DEVICE_ID_COLUMN = 'device_id',
                 TIME_COLUMN = 'time',
                 VALUE_COLUMN = 'value',
                 COLUMN_SESSION_ID = 'session_id';
    /**
     * @var int|null
     */
    private $id;
    /**
     * @var int
     */
    private $deviceId;
    /**
     * @var \DateTime
     */
    private $time;
    /**
     * @var float
     */
    private $value;
    /**
     * @var int
     */
    private $sessionId;

    /**
     * @param int|null $id
     * @param int $deviceId
     * @param \DateTime $time
     * @param float $value
     * @param int $sessionId
     */
    public function __construct(?int $id, int $deviceId, \DateTime $time, float $value, int $sessionId)
    {
        $this->id = $id;
        $this->deviceId = $deviceId;
        $this->time = $time;
        $this->value = $value;
        $this->sessionId = $sessionId;
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
     * @return Measurement
     */
    public function setId(int $id)
    {
        if ($this->id) {
            throw new \Exception('Entity already has id');
        }
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getDeviceId(): int
    {
        return $this->deviceId;
    }

    /**
     * @return \DateTime
     */
    public function getTime(): \DateTime
    {
        return $this->time;
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getSessionId(): int
    {
        return $this->sessionId;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::DEVICE_ID_COLUMN => $this->deviceId,
            self::TIME_COLUMN => $this->time,
            self::VALUE_COLUMN => $this->value,
            self::COLUMN_SESSION_ID => $this->sessionId
        ];
    }

}