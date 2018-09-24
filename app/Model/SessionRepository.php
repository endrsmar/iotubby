<?php
declare(strict_types=1);

namespace Iotubby\Model;

use Iotubby\Model\Entities\Device;
use Iotubby\Model\Entities\Session;

interface SessionRepository
{

    /**
     * @param int $id
     * @return Session|null
     */
    public function getSession(int $id): ?Session;

    /**
     * @param Device $device
     * @return Session[]
     */
    public function getSessionsForDevice(Device $device): array;

    /**
     * @param Session $session
     */
    public function save(Session $session): void;


}