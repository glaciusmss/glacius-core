<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 10/15/2019
 * Time: 9:58 AM.
 */

namespace App\Services;


use App\Contracts\Processor as ProcessorContract;
use App\Enums\MarketplaceEnum;
use App\Enums\ProcessorType;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Str;

class ProcessorFactory
{
    protected $container;
    /* @var MarketplaceEnum $marketplace */
    protected $marketplace;
    /* @var ProcessorType $processorType */
    protected $processorType;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function setMarketplace(MarketplaceEnum $marketplace)
    {
        $this->marketplace = $marketplace;
        return $this;
    }

    public function setProcessorType(ProcessorType $processorType)
    {
        $this->processorType = $processorType;
        return $this;
    }

    /**
     * @return ProcessorContract|null
     */
    public function build()
    {
        $className = 'App\\Services\\' . ucfirst($this->marketplace->value) . '\\Processors\\' . Str::title($this->processorType->key) . 'Processor';

        if (!class_exists($className)) {
            return null;
        }

        return $this->container->make($className);
    }
}
