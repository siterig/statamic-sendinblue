<?php

namespace SiteRig\Brevo\Http\Controllers;

use Edalzell\Forma\ConfigController as BaseController;
use Illuminate\Support\Arr;

class ConfigController extends BaseController
{
    protected function postProcess(array $values): array
    {
        $formConfig = Arr::get($values, 'forms');

        return array_merge(
            $values,
            ['forms' => $formConfig]
        );
    }

    protected function preProcess(string $handle): array
    {
        $config = config('brevo');

        return array_merge(
            $config,
            ['forms' => Arr::get($config, 'forms', [])]
        );
    }
}
