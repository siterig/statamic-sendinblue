<?php

namespace SiteRig\Sendinblue\Fieldtypes;

use SiteRig\Sendinblue\Sendinblue;
use Statamic\Fieldtypes\Relationship;

class SibList extends Relationship
{
    private $sendinblue = null;

    protected $canCreate = false;

    public function __construct()
    {
        $this->sendinblue = new Sendinblue;
    }

    public function getIndexItems($request)
    {
        return $this->sendinblue->getLists();
    }

    protected function toItemArray($id)
    {
        if ($id && $sib_list = $this->sendinblue->getLists($id)) {
            return $sib_list;
        }

        return [];
    }
}
