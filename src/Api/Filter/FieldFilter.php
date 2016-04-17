<?php

namespace JoeBengalen\Tables\Api\Filter;

use Aura\Filter\SubjectFilter;
use JoeBengalen\Tables\Model\FieldType;

class FieldFilter extends SubjectFilter
{
    /**
     * Initialize FieldFilter.
     */
    protected function init()
    {
        $this->validate('name')->is('alnum');
        $this->validate('name')->is('strlenMin', 3);

        $this->validate('type')->is('alnum');
        $this->validate('type')->is('inValues', FieldType::getTypes());

        $this->validate('length')->isBlankOr('int');
        $this->sanitize('length')->toBlankOr('int');

        $this->validate('allowNull')->is('bool');
        $this->sanitize('allowNull')->to('bool');

        $this->validate('default')->isBlankOr('string');
        $this->sanitize('default')->toBlankOr('string');

        $this->validate('comment')->isBlankOr('string');
        $this->sanitize('comment')->toBlankOr('string');

        $this->validate('isPrimaryKey')->is('bool');
        $this->sanitize('isPrimaryKey')->to('bool');

        $this->validate('autoIncrement')->is('bool');
        $this->sanitize('autoIncrement')->to('bool');
    }
}
