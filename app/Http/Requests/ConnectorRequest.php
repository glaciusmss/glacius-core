<?php


namespace App\Http\Requests;


use App\Contracts\RequestValidation;
use App\Exceptions\NotSupportedException;
use App\Services\Connectors\ConnectorManager;
use Illuminate\Support\Arr;

class ConnectorRequest extends BaseRequest
{
    protected $uriToFuncMapper = [
        'post_oauth' => 'onInstall',
        'delete_oauth' => 'onDeleteAuth',
        'post_callback' => 'onInstallCallback',
        'get_oauth' => 'onInstallCallback'
    ];

    public function rules()
    {
        // find rules from oauth configurations
        if (!$functionName = $this->mapUriToFunction()) {
            throw new NotSupportedException('this url is not supported');
        }

        $connectorManager = app(ConnectorManager::class);

        $connector = $connectorManager->resolveConnector($this->route('identifier'));
        $configurations = $connectorManager->makeService($connector->getAuthService())->configurations();

        $rules = Arr::get($configurations, "validation.$functionName", []);

        return $this->wrapRules($rules);
    }

    protected function mapUriToFunction()
    {
        $uri = Arr::last($this->segments());
        $method = strtolower($this->getMethod());

        return Arr::get($this->uriToFuncMapper, $method . '_' . $uri);
    }

    protected function wrapRules($rules): array
    {
        if (is_array($rules)) {
            return $rules;
        }

        if (is_string($rules)) {
            $rules = app($rules);
        }

        if (!($rules instanceof RequestValidation)) {
            throw new \InvalidArgumentException("validation rules ($rules) has to be an array or implement " . RequestValidation::class);
        }

        return $rules->rules();
    }
}
