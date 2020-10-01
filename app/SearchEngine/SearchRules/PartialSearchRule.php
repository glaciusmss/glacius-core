<?php

namespace App\SearchEngine\SearchRules;

use ScoutElastic\SearchRule;

class PartialSearchRule extends SearchRule
{
    public function buildQueryPayload()
    {
        return [
            'must' => [
                'query_string' => [
                    'query' => '*'.$this->builder->query.'*',
                ],
            ],
        ];
    }
}
