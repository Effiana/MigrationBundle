<?php
/**
 * This file is part of the BrandOriented package.
 *
 * (c) Brand Oriented sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Dominik Labudzinski <dominik@labudzinski.com>
 */

namespace Effiana\MigrationBundle\Migration;


class Column
{
    const TARRAY = 'array';
    const SIMPLE_ARRAY = 'simple_array';
    const JSON_ARRAY = 'json_array';
    const JSON = 'json';
    const BIGINT = 'bigint';
    const BOOLEAN = 'boolean';
    const DATETIME = 'datetime';
    const DATETIME_IMMUTABLE = 'datetime_immutable';
    const DATETIMETZ = 'datetimetz';
    const DATETIMETZ_IMMUTABLE = 'datetimetz_immutable';
    const DATE = 'date';
    const DATE_IMMUTABLE = 'date_immutable';
    const TIME = 'time';
    const TIME_IMMUTABLE = 'time_immutable';
    const DECIMAL = 'decimal';
    const INTEGER = 'integer';
    const OBJECT = 'object';
    const SMALLINT = 'smallint';
    const STRING = 'string';
    const TEXT = 'text';
    const BINARY = 'binary';
    const BLOB = 'blob';
    const FLOAT = 'float';
    const GUID = 'guid';
    const DATEINTERVAL = 'dateinterval';
}