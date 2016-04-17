<?php

namespace JoeBengalen\Tables\Api\Filter;

use Aura\Filter\SubjectFilter;
use JoeBengalen\Tables\Model\ForeignKeyAction;

class ForeignKeyFilter extends SubjectFilter
{
    /**
     * Initialize ForeignKeyFilter.
     */
    protected function init()
    {
        $this->validate('referencedFieldId')->is('int');
        $this->sanitize('referencedFieldId')->to('int');

        $this->validate('onUpdate')->is('alnum');
        $this->validate('onUpdate')->is('inValues', ForeignKeyAction::getActions());

        $this->validate('onDelete')->is('alnum');
        $this->validate('onDelete')->is('inValues', ForeignKeyAction::getActions());
    }
}