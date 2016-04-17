<?php

namespace JoeBengalen\Tables\Api\Transformer;

use JoeBengalen\Tables\Model\Index;

class IndexTransformer extends AbstractTransformer
{
    /**
     * Transform Index.
     *
     * @param Index $index
     *
     * @return array
     */
    protected function transform(Index $index)
    {
        return [
            'id' => $index->getId(),
            'fieldId' => $index->getFieldId(),
            'name' => $index->getName(),
            'unique' => $index->getUnique(),
        ];
    }
}
