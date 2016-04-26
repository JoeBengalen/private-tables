<?php

use Phinx\Migration\AbstractMigration;

class Initial extends AbstractMigration
{
    /**
     * Change to the initial database state.
     */
    public function change()
    {
        $this->table('table')
                ->addColumn('name', 'string', ['length' => 255])
                ->addIndex('name', ['unique' => true])
                ->create();

        $this->table('field')
                ->addColumn('tableId', 'integer')
                ->addColumn('name', 'string', ['length' => 255])
                ->addColumn('type', 'string', ['length' => 15])
                ->addColumn('length', 'integer', ['null' => true])
                ->addColumn('allowNull', 'boolean', ['default' => false])
                ->addColumn('default', 'string', ['length' => 255, 'null' => true])
                ->addColumn('comment', 'string', ['length' => 255, 'null' => true])
                ->addColumn('isPrimaryKey', 'boolean')
                ->addColumn('autoIncrement', 'boolean')
                ->addForeignKey('tableId', 'table', 'id')
                ->create();

        $this->table('index')
                ->addColumn('fieldId', 'integer')
                ->addColumn('name', 'string', ['length' => 255])
                ->addColumn('unique', 'boolean')
                ->addForeignKey('fieldId', 'field', 'id')
                ->create();

        $this->table('foreignKey')
                ->addColumn('fieldId', 'integer')
                ->addColumn('referencedFieldId', 'integer')
                ->addColumn('onUpdate', 'string', ['length' => 10])
                ->addColumn('onDelete', 'string', ['length' => 10])
                ->addForeignKey('fieldId', 'field', 'id')
                ->addForeignKey('referencedFieldId', 'field', 'id')
                ->create();
    }
}
