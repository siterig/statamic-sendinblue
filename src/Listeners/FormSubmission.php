<?php

namespace SiteRig\Brevo\Listeners;

use SiteRig\Brevo\Brevo;
use Statamic\Events\SubmissionCreated;

class FormSubmission
{
    private $brevo = null;

    public function handle(SubmissionCreated $event)
    {
        $this->brevo = new Brevo;
        $this->brevo->addSubscriber($this->getFormConfig($event->submission->form()->handle()), $event->submission->data());
    }

    private function getFormConfig(string $handle)
    {
        return collect(config('sendinblue.forms', []))->firstWhere('form', $handle) ?? [];
    }
}
