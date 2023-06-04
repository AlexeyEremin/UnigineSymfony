<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\HttpClient;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

class GetInformationUrlCommand extends Command
{
    protected static $defaultName = 'command:get-information-url';
    protected static $defaultDescription = 'Output information hash and date created';

    protected function configure(): void
    {
        $this
            ->addArgument('url', InputArgument::REQUIRED, 'URL address example: "https://vk.com/"')
            ->addArgument('datetime', InputArgument::REQUIRED, 'Date time format example (YmdHis): "20220202100000" ');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $url = $input->getArgument('url');
        $datetime = $input->getArgument('datetime');
        $client = HttpClient::create();
        $client->request('GET', $_ENV['APP_URL'] . '/encode-url', [
            'query' => [
                'url' => urlencode($url . '/' . $datetime)
            ]
        ]);

        $io->success('Command add new address');

        return Command::SUCCESS;
    }
}
