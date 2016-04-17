<?php

namespace JoeBengalen\Tables\Api\Transformer;

abstract class AbstractTransformer
{
    /**
     * Wrap the tramsformed item or collection.
     *
     * @param array $data
     *
     * @return array
     */
    protected function wrap(array $data)
    {
        return $data;
    }

    /**
     * Transform single item.
     *
     * @param mixed $item
     *
     * @return array
     */
    public function item($item)
    {
        return $this->wrap($this->transform($item));
    }

    /**
     * Transform collection.
     *
     * @param mixed[] $collection
     *
     * @return array[]
     */
    public function collection($collection)
    {
        $result = [];
        foreach ($collection as $item) {
            $result[] = $this->transform($item);
        }

        return $this->wrap($result);
    }
}
