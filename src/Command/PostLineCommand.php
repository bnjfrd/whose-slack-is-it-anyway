<?php

namespace WSIIA\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PostLineCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('post-line')
            ->setDescription('Posts a Whose Line is it Anyway line to a Slack workflow.');
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     * @throws TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = HttpClient::create();
        $client->request('POST', $this->getSlackURL(), [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'line' => $this->getLine(),
            ],
        ]);

        return 1;
    }

    /**
     * @return string
     */
    private function getLine(): string
    {
        $path = __DIR__ . '/../../lines.yaml';
        $lines = Yaml::parseFile($path)['lines'];

        return $lines[rand(0, count($lines) - 1)];
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    private function getSlackURL(): string
    {
        $path = __DIR__ . '/../../config.yaml';
        if (!file_exists($path)) {
            throw new \RuntimeException('Configuration file was not found. Please copy the config.yaml.dist file to a new config.yaml file.');
        }

        $config = Yaml::parseFile($path);

        return $config['slack_url'];
    }
}