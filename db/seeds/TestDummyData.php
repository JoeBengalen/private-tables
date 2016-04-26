<?php

use Phinx\Seed\AbstractSeed;

class TestDummyData extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $this->insert('table', [
            [
                'id' => 1,
                'name' => 'table1',
            ],
            [
                'id' => 2,
                'name' => 'table2',
            ]
        ]);

        $this->insert('field', [
            [
                'id' => 1,
                'tableId' => 1,
                'name' => 'id',
                'type' => 'INTEGER',
            ],
            [
                'id' => 2,
                'tableId' => 1,
                'name' => 'fieldname',
                'type' => 'STRING',
                'length' => 255,
            ]
        ]);

        $this->insert('index', [
            [
                'id' => 1,
                'fieldId' => 1,
                'name' => 'index1',
                'unique' => true,
            ],
            [
                'id' => 2,
                'fieldId' => 1,
                'name' => 'index2',
                'unique' => false,
            ]
        ]);
    }
}
