<?php

namespace JoeBengalen\Tables\Model;

class FieldType
{
    /**
     * @var string
     */
    const BIGINTEGER = 'BIGINTEGER';

    /**
     * @var string
     */
    const BINARY = 'BINARY';

    /**
     * @var string
     */
    const BOOLEAN = 'BOOLEAN';

    /**
     * @var string
     */
    const CHAR = 'CHAR';

    /**
     * @var string
     */
    const DATE = 'DATE';

    /**
     * @var string
     */
    const DATETIME = 'DATETIME';

    /**
     * @var string
     */
    const DECIMAL = 'DECIMAL';

    /**
     * @var string
     */
    const FLOAT = 'FLOAT';

    /**
     * @var string
     */
    const INTEGER = 'INTEGER';

    /**
     * @var string
     */
    const STRING = 'STRING';

    /**
     * @var string
     */
    const TEXT = 'TEXT';

    /**
     * @var string
     */
    const TIME = 'TIME';

    /**
     * @var string
     */
    const TIMESTAMP = 'TIMESTAMP';

    /**
     * Get FieldTypes.
     *
     * @return string[]
     */
    public static function getTypes()
    {
        return [
            self::BIGINTEGER,
            self::BINARY,
            self::BOOLEAN,
            self::CHAR,
            self::DATE,
            self::DATETIME,
            self::DECIMAL,
            self::FLOAT,
            self::INTEGER,
            self::STRING,
            self::TEXT,
            self::TIME,
            self::TIMESTAMP,
        ];
    }
}
