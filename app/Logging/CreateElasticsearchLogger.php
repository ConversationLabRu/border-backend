<?php

namespace App\Logging;

use Elastic\Elasticsearch\ClientBuilder;
use Monolog\Formatter\ElasticsearchFormatter;
use Monolog\Handler\ElasticsearchHandler;
use Monolog\Logger;

class CreateElasticsearchLogger
{
    /**
     * Create a custom Monolog instance.
     *
     * @param  array  $config
     * @return \Monolog\Logger
     */
    public function __invoke(array $config)
    {
        $logger = new Logger('elasticsearch');

        //create the client
        $client = ClientBuilder::create()
            ->setHosts(['http://elasticsearch:9200'])
            ->setBasicAuthentication('elastic', 'khCIUzQx6azizGWLksKV')  // Указываем логин и пароль
            ->build();

        $client->indices()->putMapping([
            'index' => 'user_logs',
            'body' => [
                'properties' => [
                    'tg_id' => ['type' => 'keyword'],
                    'username' => ['type' => 'text'],
                    'first_name' => ['type' => 'text']
                ]
            ]
        ]);

        //create the handler
        $options = [
            'index' => 'user_logs',
            'type' => '_doc'
        ];
        $handler = new ElasticsearchHandler($client, $options, Logger::INFO, true);
        $handler->setFormatter(new ElasticsearchFormatter("user_logs", "_doc"));

        $logger->setHandlers(array($handler));

        return $logger;
    }
}
