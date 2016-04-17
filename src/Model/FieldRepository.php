<?php

namespace JoeBengalen\Tables\Model;

use Aura\Sql\ExtendedPdo;
use InvalidArgumentException;
use JoeBengalen\Assert\Assert;
use JoeBengalen\Tables\Model\Exception\DuplicateEntity;
use JoeBengalen\Tables\Model\Exception\EntityNotFound;
use PDOException;

class FieldRepository
{
    /**
     * @var ExtendedPdo
     */
    protected $database;

    /**
     * Create FieldRepository.
     *
     * @param ExtendedPdo $database
     */
    public function __construct(ExtendedPdo $database)
    {
        $this->database = $database;
    }

    /**
     * Get Fields.
     *
     * @return Field[]
     *
     * @throws InvalidArgumentException
     * @throws PDOException
     */
    public function getFields()
    {
        $query = "SELECT
                id,
                tableId,
                name,
                type,
                length,
                allowNull,
                `default`,
                comment,
                isPrimaryKey,
                `autoIncrement`
            FROM field";
        $data = $this->database->fetchAll($query);

        return array_map([$this, 'buildField'], $data);
    }

    /**
     * Get Fields by Table.
     *
     * @return Field[]
     *
     * @throws InvalidArgumentException
     * @throws PDOException
     */
    public function getFieldsByTable(Table $table)
    {
        $query = "SELECT
                id,
                tableId,
                name,
                type,
                length,
                allowNull,
                `default`,
                comment,
                isPrimaryKey,
                `autoIncrement`
            FROM field
            WHERE tableId = :tableId";
        $params = [
            'tableId' => $table->getId(),
        ];
        $data = $this->database->fetchAll($query, $params);

        $result = [];
        foreach ($data as $record) {
            $result[] = $this->buildField($record);
        }
        
        return $result;
    }

    /**
     * Get Field by id.
     *
     * @param numeric $id
     *
     * @return Field
     *
     * @throws InvalidArgumentException
     * @throws EntityNotFound
     * @throws PDOException
     */
    public function getFieldById($id)
    {
        Assert::isNumeric($id);

        $query = "SELECT
                id,
                tableId,
                name,
                type,
                length,
                allowNull,
                `default`,
                comment,
                isPrimaryKey,
                `autoIncrement`
            FROM field
            WHERE id = :id";
        $params = [
            'id' => (int) $id,
        ];
        $data = $this->database->fetchOne($query, $params);

        if (empty($data)) {
            throw new EntityNotFound(Field::class, 'id', $id);
        }

        return $this->buildField($data);
    }

    /**
     * Add Field.
     *
     * @param Field $field
     *
     * @return int Inserted id
     *
     * @throws DuplicateEntity
     * @throws PDOException
     */
    public function addField(Field $field)
    {
        $this->validateField($field);

        $query = "INSERT INTO field (
                tableId,
                name,
                type,
                length,
                allowNull,
                `default`,
                comment,
                isPrimaryKey,
                `autoIncrement`
            ) VALUES (
                :tableId,
                :name,
                :type,
                :length,
                :allowNull,
                :default,
                :comment,
                :isPrimaryKey,
                :autoIncrement
            )";
        $params = [
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
        $this->database->perform($query, $params);

        return $this->database->lastInsertId();
    }

    /**
     * Update Field.
     *
     * @param Field $field
     *
     * @return int Affected rows
     *
     * @throws DuplicateEntity
     * @throws PDOException
     */
    public function updateField(Field $field)
    {
        $this->validateField($field);

        $query = "UPDATE field
            SET
                tableId = :tableId,
                name = :name,
                type = :type,
                length = :length,
                allowNull = :allowNull,
                `default` = :default,
                comment = :comment,
                isPrimaryKey = :isPrimaryKey,
                `autoIncrement` = :autoIncrement
            WHERE id = :id";
        $params = [
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
        $result = $this->database->perform($query, $params);

        return $result->rowCount();
    }

    /**
     * Delete Field.
     *
     * @param Field $field
     *
     * @return int Affected rows
     *
     * @throws PDOException
     */
    public function deleteField(Field $field)
    {
        $query = "DELETE FROM field WHERE id = :id";
        $params = [
            'id' => $field->getId(),
        ];
        $result = $this->database->perform($query, $params);

        return $result->rowCount();
    }

    /**
     * Delete Fields by Table.
     *
     * @param Table $table
     *
     * @return int Affected rows
     *
     * @throws PDOException
     */
    public function deleteFieldsByTable(Table $table)
    {
        $query = "DELETE FROM field WHERE tableId = :tableId";
        $params = [
            'tableId' => $table->getId(),
        ];
        $result = $this->database->perform($query, $params);

        return $result->rowCount();
    }

    /**
     * Build Field.
     *
     * @param array $data
     *
     * @return Field
     *
     * @throws InvalidArgumentException
     */
    public function buildField($data)
    {
        Assert::keyExists($data, 'id');
        Assert::keyExists($data, 'tableId');
        Assert::keyExists($data, 'name');
        Assert::keyExists($data, 'type');
        Assert::keyExists($data, 'length');
        Assert::keyExists($data, 'allowNull');
        Assert::keyExists($data, 'default');
        Assert::keyExists($data, 'comment');
        Assert::keyExists($data, 'isPrimaryKey');
        Assert::keyExists($data, 'autoIncrement');

        $bool = [true, false, 0, 1, '0', '1'];
        Assert::inArray($data['allowNull'], $bool);
        Assert::inArray($data['isPrimaryKey'], $bool);
        Assert::inArray($data['autoIncrement'], $bool);

        return new Field(
            $data['id'],
            $data['tableId'],
            $data['name'],
            $data['type'],
            $data['length'],
            (bool) (int) $data['allowNull'],
            $data['default'],
            $data['comment'],
            (bool) (int) $data['isPrimaryKey'],
            (bool) (int) $data['autoIncrement']
        );
    }

    /**
     * Validate Field.
     *
     * @param Field $field
     *
     * @throws DuplicateEntity
     * @throws PDOException
     */
    protected function validateField(Field $field)
    {
        $query = "SELECT id FROM field WHERE tableId = :tableId AND name = :name";
        $params = [
            'tableId' => $field->getTableId(),
            'name' => $field->getName(),
        ];

        if ($field->getId()) {
            $query .= ' AND id != :id';
            $params['id'] = $field->getId();
        }

        $data = $this->database->fetchOne($query, $params);

        if ($data) {
            throw new DuplicateEntity(Field::class, 'name', $field->getName());
        }
    }
}
