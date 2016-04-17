<?php

namespace JoeBengalen\Tables\Model;

use Aura\Sql\ExtendedPdo;
use InvalidArgumentException;
use JoeBengalen\Assert\Assert;
use JoeBengalen\Tables\Model\Exception\DuplicateEntity;
use JoeBengalen\Tables\Model\Exception\EntityNotFound;
use PDOException;

class TableRepository
{
    /**
     * @var ExtendedPdo
     */
    protected $database;

    /**
     * @var FieldRepository
     */
    protected $fieldModel;

    /**
     * Create TableRepository.
     *
     * @param ExtendedPdo $database
     * @param FieldRepository  $fieldModel
     */
    public function __construct(ExtendedPdo $database, FieldRepository $fieldModel)
    {
        $this->database = $database;
        $this->fieldModel = $fieldModel;
    }

    /**
     * Get Tables.
     *
     * @return Table[]
     *
     * @throws InvalidArgumentException
     * @throws PDOException
     */
    public function getTables()
    {
        $query = "SELECT id, name FROM `table`";
        $data = $this->database->fetchAll($query);

        return array_map([$this, 'buildTable'], $data);
    }

    /**
     * Get Table by id.
     *
     * @param numeric $id
     *
     * @return Table
     *
     * @throws InvalidArgumentException
     * @throws EntityNotFound
     * @throws PDOException
     */
    public function getTableById($id)
    {
        Assert::isNumeric($id);

        $query = "SELECT id, name FROM `table` WHERE id = :id";
        $params = [
            'id' => (int) $id,
        ];
        $data = $this->database->fetchOne($query, $params);

        if (empty($data)) {
            throw new EntityNotFound(Table::class, 'id', $id);
        }

        return $this->buildTable($data);
    }

    /**
     * Add Table.
     *
     * @param Table $table
     *
     * @return int Inserted id
     *
     * @throws DuplicateEntity
     * @throws PDOException
     */
    public function addTable(Table $table)
    {
        $this->validateTable($table);

        $query = "INSERT INTO `table` (name) VALUES (:name)";
        $params = [
            'name' => $table->getName(),
        ];
        $this->database->perform($query, $params);

        return $this->database->lastInsertId();
    }

    /**
     * Update Table.
     *
     * @param Table $table
     *
     * @return int Affected rows
     *
     * @throws DuplicateEntity
     * @throws PDOException
     */
    public function updateTable(Table $table)
    {
        $this->validateTable($table);

        $query = "UPDATE `table` SET name = :name WHERE id = :id";
        $params = [
            'id' => $table->getId(),
            'name' => $table->getName(),
        ];
        $result = $this->database->perform($query, $params);

        return $result->rowCount();
    }

    /**
     * Delete Table.
     *
     * @param Table $table
     *
     * @return int Affected rows
     *
     * @throws PDOException
     */
    public function deleteTable(Table $table)
    {
        $this->fieldModel->deleteFieldsByTable($table);

        $query = "DELETE FROM `table` WHERE id = :id";
        $params = [
            'id' => $table->getId(),
        ];
        $result = $this->database->perform($query, $params);

        return $result->rowCount();
    }

    /**
     * Build Table.
     *
     * @param array $data
     *
     * @return Table
     *
     * @throws InvalidArgumentException
     */
    public function buildTable($data)
    {
        Assert::keyExists($data, 'id');
        Assert::keyExists($data, 'name');

        return new Table(
            $data['id'],
            $data['name']
        );
    }

    /**
     * Validate Table.
     *
     * @param Table $table
     *
     * @throws DuplicateEntity
     */
    protected function validateTable(Table $table)
    {
        $query = "SELECT id FROM `table` WHERE name = :name";
        $params = [
            'name' => $table->getName(),
        ];

        if ($table->getId()) {
            $query .= ' AND id != :id';
            $params['id'] = $table->getId();
        }

        $data = $this->database->fetchOne($query, $params);

        if ($data) {
            throw new DuplicateEntity(Table::class, 'name', $table->getName());
        }
    }
}
