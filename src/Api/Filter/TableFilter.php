<?php

namespace JoeBengalen\Tables\Api\Filter;

use Aura\Filter\SubjectFilter;

class TableFilter extends SubjectFilter
{
    /**
     * Initialize TableFilter.
     */
    protected function init()
    {
        $this->validate('name')->is('alnum');
        $this->validate('name')->is('strlenMin', 3);
    }
}