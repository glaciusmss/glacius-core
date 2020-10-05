<?php

namespace App\Services\Connectors;

use App\Contracts\BotConnector;
use App\Contracts\ServiceConnector;
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
        $isClassImplementServiceConnector = in_array(ServiceConnector::class, class_implements($class), true);
        $isClassImplementBotConnector = in_array(BotConnector::class, class_implements($class), true);

        if (! $isClassImplementServiceConnector  && ! $isClassImplementBotConnector) {
            throw new \InvalidArgumentException("$class has to implement ".ServiceConnector::class." or ".BotConnector::class);
        }

        $this->connectors->push($class);

        return $this;
    }

    public function findConnector(string $identifier, string $connectorType = ServiceConnector::class)
    {
        $connector = $this->makeConnectors()
            ->first(static function ($connector) use ($connectorType, $identifier) {
                return $connector instanceof $connectorType && $connector->getConnectorIdentifier() === $identifier;
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
            ->map(static function (ServiceConnector $connector) {
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
