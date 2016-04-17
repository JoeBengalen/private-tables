<?php

namespace JoeBengalen\Tables\Model\Exception;

use Exception;
use JoeBengalen\Assert\Helper;

class EntityNotFound extends Exception
{
    /**
     * @var string
     */
    protected $entity;

    /**
     * @var string
     */
    protected $field;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * EntityNotFoundException.
     *
     * @param string $entity
     * @param string $field
     * @param mixed $value
     */
    public function __construct($entity, $field, $value)
    {
        $this->entity = (string) $entity;
        $this->field = (string) $field;
        $this->value = $value;

        parent::__construct(sprintf(
            'Entity %s with %s %s (%s) not found',
            $this->entity,
            $this->field,
            Helper::valueToString($this->value),
            Helper::typeToString($this->value)
        ));
    }

    /**
     * Get entity.
     *
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Get field.
     *
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Get value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
