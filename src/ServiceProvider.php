<?php

namespace SiteRig\Brevo;

use Edalzell\Forma\Forma;
use SiteRig\Brevo\Fieldtypes\FormFields;
use SiteRig\Brevo\Fieldtypes\BrevoAttribute;
use SiteRig\Brevo\Fieldtypes\BrevoList;
use SiteRig\Brevo\Http\Controllers\ConfigController;
use SiteRig\Brevo\Listeners\FormSubmission;
use Statamic\Events\SubmissionCreated;
use Statamic\Facades\Permission;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $fieldtypes = [
        FormFields::class,
        BrevoAttribute::class,
        BrevoList::class,
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

        $this->mergeConfigFrom(__DIR__ . '/../config/sendinblue.php', 'brevo');

        $this->publishes([
            __DIR__ . '/../config/sendinblue.php' => config_path('sendinblue.php'),
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
