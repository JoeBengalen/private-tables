<?php

namespace JoeBengalen\Tables\Model;

use InvalidArgumentException;
use JoeBengalen\Assert\Assert;

class Table
{
    /**
     * @var int|null
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * Create Table.
     *
     * @param numeric|null $id
     * @param string       $name
     *
     * @throws InvalidArgumentException
     */
    public function __construct($id, $name)
    {
        $this->setId($id);
        $this->setName($name);
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
}
