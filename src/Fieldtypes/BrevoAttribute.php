<?php

namespace SiteRig\Brevo\Fieldtypes;

use SiteRig\Brevo\Brevo;
use Statamic\Fieldtypes\Relationship;

class BrevoAttribute extends Relationship
{
    private $brevo = null;

    protected $canCreate = false;

    public function __construct()
    {
        $this->brevo = new Brevo;
    }

    public function getIndexItems($request)
    {
        return $this->brevo->getAttributes();
    }

    protected function toItemArray($id)
    {
        if ($id && $brevo_attribute = $this->brevo->getAttributes($id)) {
            return $brevo_attribute;
        }

        return[];
    }
}
