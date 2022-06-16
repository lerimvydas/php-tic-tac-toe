<?php

namespace App\Command;

use App\Games\TicTacToeGame;
use App\Games\TicTacToeType;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'SocketServer',
    description: 'Starts the socket server for the tic-tac-toe game',
)]
class SocketServerCommand extends Command
{
    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->note('Starting TicTacToe game socket server');

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new TicTacToeGame(TicTacToeType::ThreeByThree)
                )
            ),
            10000
        );

        $server->run();

        return Command::SUCCESS;
    }
}
