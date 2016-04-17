<?php

namespace JoeBengalen\Tables\Model;

use Aura\Sql\ExtendedPdo;

class ForeignKeyRepository
{
    /**
     * @var ExtendedPdo
     */
    protected $database;

    /**
     * Create ForeignKeyRepository.
     *
     * @param ExtendedPdo $database
     */
    public function __construct(ExtendedPdo $database)
    {
        $this->database = $database;
    }
}
