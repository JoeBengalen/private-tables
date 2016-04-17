<?php

namespace JoeBengalen\Tables\Api\Filter;

use Aura\Filter\SubjectFilter;

class IndexFilter extends SubjectFilter
{
    /**
     * Initialize IndexFilter.
     */
    protected function init()
    {
        $this->validate('name')->is('alnum');
        $this->validate('name')->is('strlenMin', 3);

        $this->validate('unique')->is('bool');
        $this->sanitize('unique')->to('bool');
    }
}