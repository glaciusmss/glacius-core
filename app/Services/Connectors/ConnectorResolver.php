<?php

namespace App\Services\Connectors;

use App\Contracts\Connector;
use App\Contracts\ResolvesConnector;
use App\Exceptions\ConnectorNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;

class ConnectorResolver implements ResolvesConnector
{
    protected $connectors;

    public function __construct()
    {
        $this->connectors = Collection::make();
    }

    public function addConnector($class)
    {
        if (! in_array(Connector::class, class_implements($class), true)) {
            throw new \InvalidArgumentException("$class has to implement ".Connector::class);
        }

        $this->connectors->push($class);

        return $this;
    }

    public function findConnector(string $identifier): Connector
    {
        $connector = $this->makeConnectors()
            ->first(static function (Connector $connector) use ($identifier) {
                return $connector->getConnectorIdentifier() === $identifier;
            });

        return throw_unless(
            $connector,
            new ConnectorNotFoundException($identifier.' not found')
        );
    }

    public function getConnectors(): Enumerable
    {
        return $this->connectors;
    }

    public function setConnectors(Enumerable $connectors)
    {
        $this->connectors = $connectors;

        return $this;
    }

    public function getAllIdentifiers(): Enumerable
    {
        return $this->makeConnectors()
            ->map(static function (Connector $connector) {
                return $connector->getConnectorIdentifier();
            });
    }

    protected function makeConnectors()
    {
        return $this->connectors
            ->map(static function ($connector) {
                if (is_string($connector)) {
                    return app($connector);
                }

                return $connector;
            });
    }
}
