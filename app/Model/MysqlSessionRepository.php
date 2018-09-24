<?php
declare(strict_types=1);

namespace Iotubby\Model;


use Iotubby\Model\Entities\Device;
use Iotubby\Model\Entities\Session;
use Nette\Database\Context;
use Nette\Database\Table\ActiveRow;
use DateTime;

class MysqlSessionRepository implements SessionRepository
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
     * @param int $id
     * @return Session|null
     */
    public function getSession(int $id): ?Session
    {
        if(!$row = $this->database->table(Session::TABLE_NAME)->where(Session::COLUMN_ID, $id)->fetch()) {
            return null;
        }
        return $this->rowToEntity($row);
    }

    /**
     * @param Device $device
     * @return Session[]
     */
    public function getSessionsForDevice(Device $device): array
    {
        $res = [];
        $selection = $this->database->table(Session::TABLE_NAME)
            ->where(Session::COLUMN_DEVICE_ID, $device->getId())
            ->order(Session::COLUMN_START.' ASC');
        foreach ($selection as $row)
        {
            $res[] = $this->rowToEntity($row);
        }
        return $res;
    }

    /**
     * @param Session $session
     */
    public function save(Session $session): void
    {
        if (!is_null($session->getId())) {
            $this->database->table(Session::TABLE_NAME)->where(Session::COLUMN_ID, $session->getId())->update($session->toArray());
        } else {
            $row = $this->database->table(Session::TABLE_NAME)->insert($session->toArray());
            $session->setId($row->{Session::COLUMN_ID});
        }
    }

    /**
     * @param ActiveRow $row
     * @return Session
     */
    private function rowToEntity(ActiveRow $row): Session
    {
        return new Session(
            $row->{Session::COLUMN_ID},
            $row->{Session::COLUMN_DEVICE_ID},
            $row->{Session::COLUMN_START},
            $row->{Session::COLUMN_END}
        );
    }

}