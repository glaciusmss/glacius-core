<?php

namespace App\Contracts;

use Illuminate\Support\Enumerable;

interface ResolvesConnector
{
    /**
     * @param object|string $class
     * @return $this
     */
    public function addConnector($class);

    /**
     * @param string $identifier
     * @param string $connectorType
     * @return ServiceConnector|BotConnector
     */
    public function findConnector(string $identifier, string $connectorType = ServiceConnector::class);

    public function getConnectors(): Enumerable;

    public function setConnectors(Enumerable $connectors);

    public function getAllIdentifiers(): Enumerable;
}
