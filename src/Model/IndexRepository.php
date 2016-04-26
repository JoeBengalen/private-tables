<?php

namespace JoeBengalen\Tables\Model;

use Aura\Sql\ExtendedPdo;
use InvalidArgumentException;
use JoeBengalen\Assert\Assert;
use JoeBengalen\Tables\Model\Exception\DuplicateEntity;
use PDOException;

class IndexRepository
{
    /**
     * @var ExtendedPdo
     */
    protected $database;

    /**
     * Create IndexRepository.
     *
     * @param ExtendedPdo $database
     */
    public function __construct(ExtendedPdo $database)
    {
        $this->database = $database;
    }

    /**
     * Get Indexes.
     *
     * @return Index[]
     *
     * @throws InvalidArgumentException
     * @throws PDOException
     */
    public function getIndexesByField(Field $field)
    {
        $query = "SELECT id,
                         fieldId,
                         name,
                         `unique`
                    FROM `index`
                   WHERE fieldId = :fieldId";
        $params = [
            'fieldId' => $field->getId(),
        ];
        $data = $this->database->fetchAll($query, $params);

        return array_map([$this, 'buildIndex'], $data);
    }

    /**
     * Add Index.
     *
     * @param Index $index
     *
     * @return int Inserted id
     *
     * @throws DuplicateEntity
     * @throws PDOException
     */
    public function addIndex(Index $index)
    {
        $this->validateIndex($index);

        $query = "INSERT INTO `index` (
                fieldId,
                name,
                `unique`
             ) VALUES (
                :indexId,
                :name,
                :unique
            )";
        $params = [
            'fieldId' => $index->getFieldId(),
            'name' => $index->getName(),
            'unique' => $index->getUnique(),
        ];
        $this->database->perform($query, $params);

        return $this->database->lastInsertId();
    }

    /**
     * Build Index.
     *
     * @param array $data
     *
     * @return Index
     *
     * @throws InvalidArgumentException
     */
    public function buildIndex($data)
    {
        Assert::keyExists($data, 'id');
        Assert::keyExists($data, 'fieldId');
        Assert::keyExists($data, 'name');
        Assert::keyExists($data, 'unique');

        $bool = [true, false, 0, 1, '0', '1'];
        Assert::inArray($data['unique'], $bool);

        return new Index(
            $data['id'],
            $data['fieldId'],
            $data['name'],
            (bool) (int) $data['unique']
        );
    }

    /**
     * Validate Index.
     *
     * @param Index $index
     *
     * @throws DuplicateEntity
     * @throws PDOException
     */
    protected function validateIndex(Index $index)
    {
        $query = "SELECT id FROM `index` WHERE fieldId = :fieldId AND name = :name";
        $params = [
            'fieldId' => $index->getFieldId(),
            'name' => $index->getName(),
        ];

        if ($index->getId()) {
            $query .= ' AND id != :id';
            $params['id'] = $index->getId();
        }

        $data = $this->database->fetchOne($query, $params);

        if ($data) {
            throw new DuplicateEntity(Index::class, 'name', $index->getName());
        }
    }
}
