<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MakeRequestsCommand extends Command
{
    protected static $defaultName = 'app:make-requests';

    private HttpClientInterface $myProjectClient;

    public function __construct(HttpClientInterface $myProjectClient)
    {
        $this->myProjectClient = $myProjectClient;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Make HTTP requests and print statistics')
            ->addOption('count', 'c', InputOption::VALUE_REQUIRED, 'The count of requests to make', '10')
            ->addArgument('urls', InputArgument::IS_ARRAY, 'The URLs to query')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $requestCount = (int) $input->getOption('count');
        $urls = $input->getArgument('urls');

        $responses = array_combine($urls, array_fill(0, count($urls), []));

        for ($i = 0; $i < $requestCount; ++$i) {
            foreach ($urls as $url) {
                $responses[$url][] = $this->myProjectClient->request('GET', $url);
            }
        }

        $output->writeln('Sent all requests. Waiting for responses…');

        $statistics = array_combine($urls, array_fill(0, count($urls), []));
        foreach ($responses as $url => $urlResponses) {
            foreach ($urlResponses as $response) {
                try {
                    $statusCode = $response->getStatusCode();
                } catch (TransportExceptionInterface $transportException) {
                    $statusCode = 'error';
                }

                if (!array_key_exists($statusCode, $statistics[$url])) {
                    $statistics[$url][$statusCode] = [
                        'count' => 0,
                        'totalTime' => 0
                    ];
                }

                $statistics[$url][$statusCode]['count'] += 1;
                $statistics[$url][$statusCode]['totalTime'] += $response->getInfo('total_time');
            }
        }

        $table = new Table($output);
        $table->setHeaders(['URL', 'Status', 'Count', 'Average Time']);
        foreach ($statistics as $url => $urlStatistics) {
            ksort($urlStatistics);
            foreach ($urlStatistics as $statusCode => $requestStatistics) {
                $table->addRow([
                    $url,
                    $statusCode,
                    $requestStatistics['count'],
                    round($requestStatistics['totalTime'] / $requestStatistics['count'] * 1000).' ms',
                ]);
            }

            if ($url !== array_key_last($statistics)) {
                $table->addRow(new TableSeparator());
            }
        }
        $table->render();

        return Command::SUCCESS;
    }
}
