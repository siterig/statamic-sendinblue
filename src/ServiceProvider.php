<?php

namespace SiteRig\Brevo;

use Edalzell\Forma\Forma;
use SiteRig\Brevo\Fieldtypes\FormFields;
use SiteRig\Brevo\Fieldtypes\SibAttribute;
use SiteRig\Brevo\Fieldtypes\SibList;
use SiteRig\Brevo\Http\Controllers\ConfigController;
use SiteRig\Brevo\Listeners\FormSubmission;
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

        $this->mergeConfigFrom(__DIR__ . '/../config/brevo.php', 'brevo');

        $this->publishes([
            __DIR__ . '/../config/brevo.php' => config_path('brevo.php'),
        ], 'brevo-config');

        return $this;
    }

    protected function bootAddonPermissions()
    {
        $this->app->booted(function () {
            Permission::group('brevo', 'Brevo', function () {
                Permission::register('edit brevo configuration')->label(__('Edit Brevo Configuration'));
            });
        });

        return $this;
    }
}
