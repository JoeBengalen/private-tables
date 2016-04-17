<?php

namespace JoeBengalen\Tables\Api\Transformer;

use JoeBengalen\Tables\Model\Field;

class FieldTransformer extends AbstractTransformer
{
    /**
     * Transform Field.
     *
     * @param Field $field
     *
     * @return array
     */
    protected function transform(Field $field)
    {
        return [
            'id' => $field->getId(),
            'tableId' => $field->getTableId(),
            'name' => $field->getName(),
            'type' => $field->getType(),
            'length' => $field->getLength(),
            'allowNull' => $field->getAllowNull(),
            'default' => $field->getDefault(),
            'comment' => $field->getComment(),
            'isPrimaryKey' => $field->getIsPrimaryKey(),
            'autoIncrement' => $field->getAutoIncrement(),
        ];
    }
}
