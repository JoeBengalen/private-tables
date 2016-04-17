<?php

namespace JoeBengalen\Tables\Model;

use Aura\Sql\ExtendedPdo;

class IndexRepository
{
    /**
     * @var ExtendedPdo
     */
    protected $database;

    /**
     * Create IndexRepository.
     *
     * @param ExtendedPdo $database
     */
    public function __construct(ExtendedPdo $database)
    {
        $this->database = $database;
    }
}
