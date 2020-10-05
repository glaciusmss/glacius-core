<?php


namespace App\Contracts;

interface Connector
{
    /**
     * the connector unique identifier.
     *
     * @return string
     */
    public function getConnectorIdentifier(): string;
}
