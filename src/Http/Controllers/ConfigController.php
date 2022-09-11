<?php

namespace SiteRig\Sendinblue\Http\Controllers;

use Edalzell\Forma\ConfigController as BaseController;
use Illuminate\Support\Arr;

class ConfigController extends BaseController
{
    protected function postProcess(array $values): array
    {
        $formConfig = Arr::get($values, 'forms');

        return array_merge(
            $values,
            ['forms' => $formConfig[0]]
        );
    }

    protected function preProcess(string $handle): array
    {
        $config = config($handle);

        return array_merge(
            $config,
            ['forms' => [Arr::get($config, 'forms', [])]]
        );
    }
}
