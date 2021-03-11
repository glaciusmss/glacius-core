<?php

namespace App\Services\Connectors;

use App\Contracts\ServiceConnector;
use App\Contracts\ResolvesConnector;
use Illuminate\Support\Collection;

class ManagerBuilder
{
    protected $identifier;
    protected $connectorType = ServiceConnector::class;
    protected $managerClass;
    protected $serviceMethod;

    public function setIdentifier(string $identifier): ManagerBuilder
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function setConnectorType(string $connectorType): ManagerBuilder
    {
        $this->connectorType = $connectorType;
        return $this;
    }

    public function setManagerClass(string $managerClass): ManagerBuilder
    {
        $this->managerClass = $managerClass;
        return $this;
    }

    public function setServiceMethod(string $serviceMethod): ManagerBuilder
    {
        $this->serviceMethod = $serviceMethod;
        return $this;
    }

    public function makeService($service)
    {
        if (is_string($service)) {
            return app($service);
        }

        return $service;
    }

    public function build()
    {
        $connector = app(ResolvesConnector::class)->findConnector($this->identifier, $this->connectorType);

        $service = $this->makeService(
            $connector->{$this->serviceMethod}()
        );

        if (is_array($service)) {
            $service = Collection::wrap($service)->map(function ($s) {
                return $this->makeService($s);
            });
        }

        return app()->makeWith($this->managerClass, [
            'identifier' => $this->identifier,
            'service' => $service,
            'connector' =>  $connector
        ]);
    }
}
