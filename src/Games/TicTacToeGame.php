<?php

namespace App\Games;

use Ratchet\ConnectionInterface;


enum TicTacToeType {
    case ThreeByThree;
}

class TicTacToeGame extends Game
{
    private array $board = [
        [],
        [],
        []
    ];
    

    public function __construct(TicTacToeType $type)
    {
        if($type !== TicTacToeType::ThreeByThree){
            throw new \InvalidArgumentException('Game type not supported');
        }
    }
    private function getState():string
    {
        return 'O turn';
    }

    public function draw():string
    {
        return json_encode(['board' => $this->board, 'state' => $this->getState()]);
    }

    public function onOpen(ConnectionInterface $conn)
    {
        echo "New connection! ({$conn->resourceId})\n";

        $conn->send($this->draw());
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        echo $msg;

        $from->send($this->draw());
    }

    public function onClose(ConnectionInterface $conn)
    {
        echo "Bye!";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
