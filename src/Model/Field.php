<?php

namespace JoeBengalen\Tables\Model;

use InvalidArgumentException;
use JoeBengalen\Assert\Assert;
use LogicException;

class Field
{
    /**
     * @var int|null
     */
    protected $id;

    /**
     * @var int
     */
    protected $tableId;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var int|null
     */
    protected $length;

    /**
     * @var bool
     */
    protected $allowNull;

    /**
     * @var string|null
     */
    protected $default;

    /**
     * @var string|null
     */
    protected $comment;

    /**
     * @var bool
     */
    protected $isPrimaryKey;

    /**
     * @var bool
     */
    protected $autoIncrement;

    /**
     * Create Field.
     *
     * @param numeric|null $id
     * @param numeric      $tableId
     * @param string       $name
     * @param string       $type
     * @param numeric|null $length
     * @param bool         $allowNull
     * @param string|null  $default
     * @param string|null  $comment
     * @param bool         $isPrimaryKey
     * @param bool         $autoIncrement
     *
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function __construct(
        $id,
        $tableId,
        $name,
        $type,
        $length = null,
        $allowNull = false,
        $default = null,
        $comment = null,
        $isPrimaryKey = false,
        $autoIncrement = false
    ) {
        $this->setId($id);
        $this->setTableId($tableId);
        $this->setName($name);
        $this->setType($type);
        $this->setLength($length);
        $this->setAllowNull($allowNull);
        $this->setDefault($default);
        $this->setComment($comment);
        $this->setIsPrimaryKey($isPrimaryKey);
        $this->setAutoIncrement($autoIncrement);
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
     * Set tableId.
     *
     * @param numeric $tableId
     *
     * @throws InvalidArgumentException
     */
    protected function setTableId($tableId)
    {
        Assert::isNumeric($tableId);

        $this->tableId = (int) $tableId;
    }

    /**
     * Get tableId.
     *
     * @return int
     */
    public function getTableId()
    {
        return $this->tableId;
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
     * Set type.
     *
     * @param string $type
     *
     * @throws InvalidArgumentException
     */
    public function setType($type)
    {
        Assert::isString($type);
        Assert::inArray($type, FieldType::getTypes());

        $this->type = $type;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set length.
     *
     * @param numeric|null $length
     *
     * @throws InvalidArgumentException
     */
    public function setLength($length)
    {
        Assert::isNullOrNumeric($length);

        $this->length = (int) $length ?: null;
    }

    /**
     * Get length.
     *
     * @return int|null
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Set allowNull.
     *
     * @param bool $allowNull
     *
     * @throws InvalidArgumentException
     */
    public function setAllowNull($allowNull)
    {
        Assert::isBoolean($allowNull);

        $this->allowNull = $allowNull;
    }

    /**
     * Get allowNull.
     *
     * @return bool
     */
    public function getAllowNull()
    {
        return $this->allowNull;
    }

    /**
     * Set default.
     *
     * @param string|null $default
     *
     * @throws InvalidArgumentException
     */
    public function setDefault($default)
    {
        Assert::isNullOrString($default);

        $this->default = $default;
    }

    /**
     * Get default.
     *
     * @return string|null
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Set comment.
     *
     * @param string|null $comment
     *
     * @throws InvalidArgumentException
     */
    public function setComment($comment)
    {
        Assert::isNullOrString($comment);

        $this->comment = $comment;
    }

    /**
     * Get comment.
     *
     * @return string|null
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set isPrimaryKey.
     *
     * @param bool $isPrimaryKey
     *
     * @throws InvalidArgumentException
     */
    public function setIsPrimaryKey($isPrimaryKey)
    {        
        Assert::isBoolean($isPrimaryKey);

        $this->isPrimaryKey = $isPrimaryKey;
    }

    /**
     * Get isPrimaryKey.
     *
     * @return bool
     */
    public function getIsPrimaryKey()
    {
        return $this->isPrimaryKey;
    }

    /**
     * Set autoIncrement.
     *
     * @param bool $autoIncrement
     *
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function setAutoIncrement($autoIncrement)
    {
        Assert::isBoolean($autoIncrement);

        if ($autoIncrement && $this->getType() !== FieldType::INTEGER) {
            throw new LogicException(sprintf(
                'Cannot use autoIncrement for field type %s on field %s',
                $this->getType(),
                $this->getName()
            ));
        }

        $this->autoIncrement = $autoIncrement;
    }

    /**
     * Get autoIncrement.
     *
     * @return bool
     */
    public function getAutoIncrement()
    {
        return $this->autoIncrement;
    }
}
