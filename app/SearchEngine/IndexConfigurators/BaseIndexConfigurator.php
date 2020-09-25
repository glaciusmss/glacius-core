<?php


namespace App\SearchEngine\IndexConfigurators;


use ScoutElastic\IndexConfigurator;
use ScoutElastic\Migratable;

abstract class BaseIndexConfigurator extends IndexConfigurator
{
    use Migratable;

    /**
     * @var array
     */
    protected $settings = [
        //
    ];
}
