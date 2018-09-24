<?php
declare(strict_types=1);

namespace Iotubby\Components\UserPanel;


interface UserPanelFactory
{

    function create(): UserPanel;

}