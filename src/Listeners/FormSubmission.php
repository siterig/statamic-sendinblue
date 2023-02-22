<?php

namespace SiteRig\Sendinblue\Listeners;

use SiteRig\Sendinblue\Sendinblue;
use Statamic\Events\SubmissionCreated;

class FormSubmission
{
    private $sendinblue = null;

    public function handle(SubmissionCreated $event)
    {
        $this->sendinblue = new Sendinblue;
        $this->sendinblue->addSubscriber($this->getFormConfig($event->submission->form()->handle()), $event->submission->data());
    }

    private function getFormConfig(string $handle)
    {
        return collect(config('sendinblue.forms', []))->firstWhere('form', $handle) ?? [];
    }
}
