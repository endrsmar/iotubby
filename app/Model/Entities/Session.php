<?php
declare(strict_types=1);

namespace Iotubby\Model\Entities;


class Session implements Entity
{
    public const TABLE_NAME = 'session',
                 COLUMN_ID = 'id',
                 COLUMN_DEVICE_ID = 'device_id',
                 COLUMN_START = 'start',
                 COLUMN_END = 'end';

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
    private $start;
    /**
     * @var ?\DateTime
     */
    private $end;

    /**
     * @param int|null $id
     * @param int $deviceId
     * @param \DateTime $start
     * @param \DateTime|null $end
     */
    public function __construct(?int $id, int $deviceId, \DateTime $start, ?\DateTime $end)
    {
        $this->id = $id;
        $this->deviceId = $deviceId;
        $this->start = $start;
        $this->end = $end;
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
    public function setId(int $id): Session
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
    public function getStart(): \DateTime
    {
        return $this->start;
    }

    /**
     * @return Session
     */
    public function endSession(): Session
    {
        $this->end = new \DateTime();
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getEnd(): ?\DateTime
    {
        return $this->end;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'device_id' => $this->deviceId,
            'start' => $this->start,
            'end' => $this->end
        ];
    }


}