<?php

namespace App\Games;

use Ratchet\MessageComponentInterface;

abstract class Game implements MessageComponentInterface
{

    abstract public function draw():string;

}
