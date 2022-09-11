<?php

namespace SiteRig\Sendinblue\Fieldtypes;

use SiteRig\Sendinblue\Sendinblue;
use Statamic\Fieldtypes\Relationship;

class SibAttribute extends Relationship
{
    private $sendinblue = null;

    protected $canCreate = false;

    public function __construct()
    {
        $this->sendinblue = new Sendinblue;
    }

    public function getIndexItems($request)
    {
        return $this->sendinblue->getAttributes();
    }

    protected function toItemArray($id)
    {
        if ($id && $sib_attribute = $this->sendinblue->getAttributes($id)) {
            return $sib_attribute;
        }

        return[];
    }
}
