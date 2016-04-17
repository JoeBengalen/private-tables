<?php

namespace JoeBengalen\Tables\Model;

use InvalidArgumentException;
use JoeBengalen\Assert\Assert;

class Index
{
    /**
     * @var int|null
     */
    protected $id;

    /**
     * @var int
     */
    protected $fieldId;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $unique;

    /**
     * Create Index.
     *
     * @param numeric|null $id
     * @param numeric      $fieldId
     * @param string       $name
     * @param bool         $unique
     *
     * @throws InvalidArgumentException
     */
    public function __construct($id, $fieldId, $name, $unique = false)
    {
        $this->setId($id);
        $this->setFieldId($fieldId);
        $this->setName($name);
        $this->setUnique($unique);
    }

    /**
     * Set id.
     *
     * @param numeric|null $id
     *
     * @throws InvalidArgumentException
     */
    protected function setId($id)
    {
        Assert::isNullOrNumeric($id);

        $this->id = (int) $id ?: null;
    }

    /**
     * Get id.
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fieldId.
     *
     * @param numeric $fieldId
     *
     * @throws InvalidArgumentException
     */
    protected function setFieldId($fieldId)
    {
        Assert::isNumeric($fieldId);

        $this->fieldId = (int) $fieldId;
    }

    /**
     * Get fieldId.
     *
     * @return int
     */
    public function getFieldId()
    {
        return $this->fieldId;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @throws InvalidArgumentException
     */
    public function setName($name)
    {
        Assert::isString($name);

        $this->name = $name;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set unique.
     *
     * @param bool $unique
     *
     * @throws InvalidArgumentException
     */
    public function setUnique($unique)
    {
        Assert::isBoolean($unique);

        $this->unique = $unique;
    }

    /**
     * Get unique.
     *
     * @return bool
     */
    public function getUnique()
    {
        return $this->unique;
    }
}
