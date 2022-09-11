<?php

namespace SiteRig\Sendinblue;

use Edalzell\Forma\Forma;
use SiteRig\Sendinblue\Fieldtypes\FormFields;
use SiteRig\Sendinblue\Fieldtypes\SibAttribute;
use SiteRig\Sendinblue\Fieldtypes\SibList;
use SiteRig\Sendinblue\Http\Controllers\ConfigController;
use SiteRig\Sendinblue\Listeners\FormSubmission;
use Statamic\Events\SubmissionCreated;
use Statamic\Facades\Permission;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $fieldtypes = [
        FormFields::class,
        SibAttribute::class,
        SibList::class,
    ];

    protected $listen = [
        SubmissionCreated::class => [FormSubmission::class],
    ];

    protected $routes = [
        'cp' => __DIR__ . '/../routes/cp.php',
    ];

    protected $scripts = [
        __DIR__ . '/../resources/dist/js/cp.js',
    ];

    public function boot()
    {
        parent::boot();

        $this
            ->bootAddonConfig()
            ->bootAddonPermissions();
    }

    protected function bootAddonConfig()
    {
        Forma::add('siterig/sendinblue', ConfigController::class);

        $this->mergeConfigFrom(__DIR__ . '/../config/sendinblue.php', 'sendinblue');

        $this->publishes([
            __DIR__ . '/../config/sendinblue.php' => config_path('sendinblue.php'),
        ], 'sendinblue-config');

        return $this;
    }

    protected function bootAddonPermissions()
    {
        $this->app->booted(function () {
            Permission::group('sendinblue', 'Sendinblue', function () {
                Permission::register('edit sendinblue configuration')->label(__('Edit Sendinblue Configuration'));
            });
        });

        return $this;
    }
}
