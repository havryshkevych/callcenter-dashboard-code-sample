<?php declare(strict_types=1);

namespace App\Service\Monolog;

use AMQPChannel;
use AMQPConnection;
use AMQPConnectionException;
use AMQPExchange;
use AMQPExchangeException;

class Logstash extends AMQPExchange
{
    public function __construct(string $host, string $port, string $login, string $password, string $vhost)
    {
        try {
            $connection = new AMQPConnection([
                'host' => $host,
                'port' => $port,
                'login' => $login,
                'password' => $password,
                'vhost' => $vhost,
            ]);
            $connection->connect();
            parent::__construct(new AMQPChannel($connection));
            $this->setName('logstash');
        } catch (AMQPConnectionException | AMQPExchangeException) {
        }
    }
}
