<?php

namespace JoeBengalen\Tables\Model;

use InvalidArgumentException;
use JoeBengalen\Assert\Assert;

class ForeignKey
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
     * @var int
     */
    protected $referencedFieldId;

    /**
     * @var string
     */
    protected $onUpdate;

    /**
     * @var string
     */
    protected $onDelete;

    /**
     * Create ForeignKey.
     *
     * @param numeric|null $id
     * @param numeric      $fieldId
     * @param numeric      $referencedFieldId
     * @param string       $onUpdate
     * @param string       $onDelete
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        $id,
        $fieldId,
        $referencedFieldId,
        $onUpdate = ForeignKeyAction::NO_ACTION,
        $onDelete = ForeignKeyAction::NO_ACTION
    ) {
        $this->setId($id);
        $this->setFieldId($fieldId);
        $this->setReferencedFieldId($referencedFieldId);
        $this->setOnUpdate($onUpdate);
        $this->setOnDelete($onDelete);
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
     * Set fieldId
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
     * Set referencedFieldId
     *
     * @param numeric $referencedFieldId
     *
     * @throws InvalidArgumentException
     */
    public function setReferencedFieldId($referencedFieldId)
    {
        Assert::isNumeric($referencedFieldId);

        $this->referencedFieldId = (int) $referencedFieldId;
    }

    /**
     * Get referencedFieldId.
     *
     * @return int
     */
    public function getReferencedFieldId()
    {
        return $this->referencedFieldId;
    }

    /**
     * Set onUpdate.
     *
     * @param string $onUpdate
     *
     * @throws InvalidArgumentException
     */
    public function setOnUpdate($onUpdate)
    {
        Assert::isString($onUpdate);
        Assert::inArray($onUpdate, ForeignKeyAction::getActions());

        $this->onUpdate = $onUpdate;
    }

    /**
     * Get onUpdate.
     *
     * @return string
     */
    public function getOnUpdate()
    {
        return $this->onUpdate;
    }

    /**
     * Set onDelete.
     *
     * @param string $onDelete
     *
     * @throws InvalidArgumentException
     */
    public function setOnDelete($onDelete)
    {
        Assert::isString($onDelete);
        Assert::inArray($onDelete, ForeignKeyAction::getActions());

        $this->onDelete = $onDelete;
    }

    /**
     * Get onDelete.
     *
     * @return string
     */
    public function getOnDelete()
    {
        return $this->onDelete;
    }
}
