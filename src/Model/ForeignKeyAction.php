<?php

namespace JoeBengalen\Tables\Model;

class ForeignKeyAction
{
    /**
     * @var string
     */
    const CASCADE = 'CASCADE';

    /**
     * @var string
     */
    const RESTRICT = 'RESTRICT';

    /**
     * @var string
     */
    const SET_NULL = 'SET_NULL';

    /**
     * @var string
     */
    const NO_ACTION = 'NO_ACTION';

    /**
     * Get ForeignKeyActions.
     *
     * @return string[]
     */
    public static function getActions()
    {
        return [
            CASCADE,
            RESTRICT,
            SET_NULL,
            NO_ACTION,
        ];
    }
}
