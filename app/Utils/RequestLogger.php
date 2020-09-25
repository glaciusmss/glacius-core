<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 12/24/2019
 * Time: 6:04 PM.
 */

namespace App\Utils;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Spatie\HttpLogger\LogProfile;
use Spatie\HttpLogger\LogWriter;

class RequestLogger implements LogWriter, LogProfile
{
    public function shouldLogRequest(Request $request): bool
    {
        return in_array(strtolower($request->method()), config('http-logger.method'), true);
    }

    public function logRequest(Request $request)
    {
        $method = strtoupper($request->getMethod());
        $uri = $request->getPathInfo();
        $ip = $request->getClientIp();
        $header = Arr::except($request->headers->all(), config('http-logger.except'));
        $body = $request->except(config('http-logger.except'));
        $message = [
            'ip' => $ip,
            'method' => $method,
            'uri' => $uri,
            'header' => $header,
            'body' => $body,
        ];
        \Log::channel('requestlog')->info(json_encode($message, JSON_UNESCAPED_SLASHES));
    }
}
