<?php
declare(strict_types=1);

namespace Iotubby\Model\Entities;


interface Entity
{

    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @param int $id
     * @return mixed
     */
    public function setId(int $id);

    /**
     * @return array
     */
    public function toArray(): array;

}