<?php

namespace SiteRig\Brevo\Fieldtypes;

use SiteRig\Brevo\Brevo;
use Statamic\Fieldtypes\Relationship;

class SibList extends Relationship
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
        if ($id && $sib_list = $this->brevo->getLists($id)) {
            return $sib_list;
        }

        return [];
    }
}
