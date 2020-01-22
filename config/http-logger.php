<?php

return [

    /*
     * The log profile which determines whether a request should be logged.
     * It should implement `LogProfile`.
     */
    'log_profile' => \App\Utils\RequestLogger::class,

    /*
     * The log writer used to write the request to a log.
     * It should implement `LogWriter`.
     */
    'log_writer' => \App\Utils\RequestLogger::class,

    /*
     * Filter out body fields which will never be logged.
     */
    'except' => [
        'password',
        'password_confirmation',
        'authorization',
        'token',
    ],

    /*
     * The request method that should be logged
     */
    'method' => [
        'post',
        'put',
        'patch',
        'delete',
    ],
];
