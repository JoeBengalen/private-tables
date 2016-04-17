<?php

namespace JoeBengalen\Tables\Api\Transformer;

use JoeBengalen\Tables\Model\Table;

class TableTransformer extends AbstractTransformer
{
    /**
     * Transform Table.
     *
     * @param Table $table
     *
     * @return array
     */
    protected function transform(Table $table)
    {
        return [
            'id' => $table->getId(),
            'name' => $table->getName(),
        ];
    }
}
