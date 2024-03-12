<?php

namespace SiteRig\Brevo\Fieldtypes;

use SiteRig\Brevo\Brevo;
use Statamic\Fieldtypes\Relationship;

class BrevoList extends Relationship
{
    private $brevo = null;

    protected $canCreate = false;

    public function __construct()
    {
        $this->brevo = new Brevo;
    }

    public function getIndexItems($request)
    {
        return $this->brevo->getLists();
    }

    protected function toItemArray($id)
    {
        if ($id && $brevo_list = $this->brevo->getLists($id)) {
            return $brevo_list;
        }

        return [];
    }
}
