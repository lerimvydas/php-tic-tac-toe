<?php

namespace App\Games;

use Ratchet\ConnectionInterface;

enum TicTacToeType
{
    case ThreeByThree;
}

class TicTacToeGame extends Game
{
    private string $state = '';
    private array $board;

    const winningCombinations = [
        [[0, 0], [0, 1], [0, 2]], [[1, 0], [1, 1], [1, 2]], [[2, 0], [2, 1], [2, 2]],
        [[0, 0], [1, 0], [2, 0]], [[0, 1], [1, 1], [2, 1]], [[0, 2], [1, 2], [2, 2]],
        [[0, 0], [1, 1], [2, 2]], [[0, 2], [1, 1], [2, 0]]
    ];

    public function __construct(TicTacToeType $type)
    {
        if ($type !== TicTacToeType::ThreeByThree) {
            throw new \InvalidArgumentException('Game type not supported');
        }

        $this->initBoard();
    }

    private function initBoard(): void
    {
        $this->board = [
            [null, null, null],
            [null, null, null],
            [null, null, null]
        ];
    }

    private function checkWin(string $player): ?array
    {
        foreach (self::winningCombinations as $winningCombination) {
            [$x, $y, $z] = $winningCombination;
            if ($this->board[$x[0]][$x[1]] === $player && $this->board[$y[0]][$y[1]] === $player && $this->board[$z[0]][$z[1]] === $player) {
                return $winningCombination;
            }
        }
        return null;
    }

    public function draw(): string
    {
        return json_encode(['board' => $this->board, 'state' => $this->state]);
    }

    private function canPlace(): ?array
    {
        foreach ($this->board as $rowKey => $row) {
            foreach ($row as $columnKey => $column) {
                if ($column === null) return [$rowKey, $columnKey];
            }
        }

        return null;
    }

    private function botPlace($default): void
    {
        $player = 'O';
        foreach (self::winningCombinations as $winningCombination) {
            [$x, $y, $z] = $winningCombination;
            $xv = $this->board[$x[0]][$x[1]];
            $yv = $this->board[$y[0]][$y[1]];
            $zv = $this->board[$z[0]][$z[1]];

            if (($xv === null || $xv === $player) && ($yv === null || $yv === $player) && ($zv === null || $zv === $player)) {
                if ($xv === null) {
                    $this->board[$x[0]][$x[1]] = 'O';
                    return;
                } else if ($yv === null) {
                    $this->board[$y[0]][$y[1]] = 'O';
                    return;
                } else {
                    $this->board[$z[0]][$z[1]] = 'O';
                    return;
                }
            }
        }

        $this->board[$default[0]][$default[1]] = 'O';
    }

    public function onOpen(ConnectionInterface $conn):void
    {
        echo "New connection! ({$conn->resourceId})\n";

        $conn->send($this->draw());
    }

    private function checkDraw($canPlace): void
    {
        if (!$canPlace && !$this->state) {
            $this->state = 'Draw!';
        }
    }

    private function decorateWinningBoard(array $winningCombination):void
    {
        [$x, $y, $z] = $winningCombination;
        $this->board[$x[0]][$x[1]] = "<div style=\"background-color:green;\">" . $this->board[$x[0]][$x[1]] . "</div>";
        $this->board[$y[0]][$y[1]] = "<div style=\"background-color:green;\">" . $this->board[$y[0]][$y[1]] . "</div>";
        $this->board[$z[0]][$z[1]] = "<div style=\"background-color:green;\">" . $this->board[$z[0]][$z[1]] . "</div>";
    }

    public function onMessage(ConnectionInterface $from, $msg): void
    {
        $message = json_decode($msg);

        switch ($message->action) {
            case 'place':
                if ($this->state) {
                    break;
                }

                $canPlace = $this->canPlace();
                $this->checkDraw($canPlace);

                if ($canPlace && $this->board[$message->rowKey][$message->columnKey] === null) {
                    $this->board[$message->rowKey][$message->columnKey] = 'X';
                } else if ($canPlace && $this->board[$message->rowKey][$message->columnKey]) {
                    break;
                }

                $didPlayerWin = $this->checkWin('X');

                if ($didPlayerWin) {
                    $this->state = 'Player X won';
                    $this->decorateWinningBoard($didPlayerWin);
                    break;
                }

                $canPlace = $this->canPlace();

                $this->checkDraw($canPlace);

                if ($canPlace) {
                    $this->botPlace($canPlace);
                    $didBotWin = $this->checkWin('O');

                    if ($didBotWin) {
                        $this->state = 'Player O won';
                        $this->decorateWinningBoard($didBotWin);
                    }
                }
                break;

            case 'new_game':
                $this->state = '';
                $this->initBoard();
                break;
        }

        $from->send($this->draw());
    }

    public function onClose(ConnectionInterface $conn): void
    {
        echo "Bye!\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
