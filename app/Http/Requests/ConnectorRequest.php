<?php

namespace App\Http\Requests;

use App\Contracts\RequestValidation;
use App\Enums\ServiceMethod;
use App\Exceptions\NotSupportedException;
use App\Services\Connectors\ManagerBuilder;
use App\Services\Connectors\OAuthManager;
use Illuminate\Support\Arr;

class ConnectorRequest extends BaseRequest
{
    protected $uriToFuncMapper = [
        'post_oauth' => 'onInstall',
        'delete_oauth' => 'onDeleteAuth',
        'post_callback' => 'onInstallCallback',
        'get_oauth' => 'onInstallCallback',
    ];

    public function rules()
    {
        // find rules from oauth configurations
        if (! $functionName = $this->mapUriToFunction()) {
            throw new NotSupportedException('this url is not supported');
        }

        $rules = $this->getOAuthManager()->getConfiguration("validation.$functionName", []);

        $rules = $this->wrapRules($rules);

        if ($functionName === 'onInstall') {
            return Arr::add($rules, 'rtn_url', 'required');
        }

        return $rules;
    }

    protected function getOAuthManager(): OAuthManager
    {
        return app(ManagerBuilder::class)
            ->setIdentifier($this->route('identifier'))
            ->setManagerClass(OAuthManager::class)
            ->setServiceMethod(ServiceMethod::AuthService)
            ->build();
    }

    protected function mapUriToFunction()
    {
        $uri = Arr::last($this->segments());
        $method = strtolower($this->getMethod());

        return Arr::get($this->uriToFuncMapper, $method.'_'.$uri);
    }

    protected function wrapRules($rules): array
    {
        if (is_array($rules)) {
            return $rules;
        }

        if (is_string($rules)) {
            $rules = app($rules);
        }

        if (! ($rules instanceof RequestValidation)) {
            throw new \InvalidArgumentException("validation rules ($rules) has to be an array or implement ".RequestValidation::class);
        }

        return $rules->rules();
    }
}
