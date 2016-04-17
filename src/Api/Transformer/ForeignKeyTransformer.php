<?php

namespace JoeBengalen\Tables\Api\Transformer;

use JoeBengalen\Tables\Model\ForeignKey;

class ForeignKeyTransformer extends AbstractTransformer
{
    /**
     * Transform ForeignKey.
     *
     * @param ForeignKey $foreignKey
     *
     * @return array
     */
    protected function transform(ForeignKey $foreignKey)
    {
        return [
            'id' => $foreignKey->getId(),
            'fieldId' => $foreignKey->getFieldId(),
            'referencedFieldId' => $foreignKey->getReferencedFieldId(),
            'onUpdate' => $foreignKey->getOnUpdate(),
            'onDelete' => $foreignKey->getOnDelete(),
        ];
    }
}
