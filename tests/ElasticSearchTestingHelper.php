<?php

namespace Tests;

use Illuminate\Database\Eloquent\Model;

class ElasticSearchTestingHelper
{
    public static function makeSearchResponse($datas, $model)
    {
        $response = [
            'took' => 139,
            'timed_out' => false,
            '_shards' => [
                'total' => count($datas),
                'successful' => count($datas),
                'skipped' => 0,
                'failed' => 0,
            ],
            'hits' => [
                'total' => [
                    'value' => count($datas),
                    'relation' => 'eq',
                ],
                'max_score' => 1,
                'hits' => [],
            ],
        ];

        foreach ($datas as $data) {
            $response['hits']['hits'][] = [
                '_index' => strtolower($model instanceof Model ? get_class($model) : $model),
                '_type' => strtolower($model instanceof Model ? get_class($model) : $model).'s',
                '_id' => $data['id'],
                '_score' => 1,
                '_source' => $data,
            ];
        }

        return $response;
    }
}
