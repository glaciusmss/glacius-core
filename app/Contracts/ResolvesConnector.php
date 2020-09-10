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
     * @return mixed
     */
    public function findConnector(string $identifier): Connector;

    public function getConnectors(): Enumerable;

    public function setConnectors(Enumerable $connectors);

    public function getAllIdentifiers(): Enumerable;
}
