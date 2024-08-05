<?php

declare(strict_types=1);

namespace phpsap\saprfc\Traits;

use phpsap\classes\Api\Member;
use phpsap\classes\Api\Struct;
use phpsap\classes\Api\Table;
use phpsap\classes\Api\Value;
use phpsap\exceptions\SapLogicException;
use phpsap\interfaces\Api\IApiElement;
use phpsap\interfaces\Api\IStruct;
use phpsap\interfaces\Api\ITable;
use phpsap\interfaces\Api\IValue;
use phpsap\interfaces\exceptions\IInvalidArgumentException;

use function array_key_exists;
use function is_array;
use function sprintf;

/**
 * Trait ApiTrait
 * @package phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
trait ApiTrait
{
    /**
     * Create either Value, Struct or Table from a given remote function parameter
     * or return value.
     * @param string $name The name of the parameter or return value.
     * @param string $type The type of the parameter or return value.
     * @param string $direction The direction indicating whether it's a parameter or
     *                          return value.
     * @param array $def The complete API value defintion from the module.
     * @return Value|Struct|Table
     * @throws IInvalidArgumentException
     */
    private function createApiElement(string $name, string $type, string $direction, array $def): Value|Table|Struct
    {
        $optional = $def['optional'];
        if ($type === ITable::TYPE_TABLE) {
            return Table::create($name, $direction, $optional, $this->createMembers($def));
        }
        if ($type === IStruct::TYPE_STRUCT) {
            return Struct::create($name, $direction, $optional, $this->createMembers($def));
        }
        return Value::create($type, $name, $direction, $optional);
    }

    /**
     * Create either struct or table members from the def array of the remote function API.
     * @param array $def The complete API value defintion.
     * @return Member[] An array of Member objects.
     * @throws SapLogicException In case a datatype is missing in the mappings array.
     */
    private function createMembers(array $def): array
    {
        $result = [];
        if (array_key_exists('typedef', $def) && is_array($def['typedef'])) {
            foreach ($def['typedef'] as $name => $member) {
                $result[] = Member::create($this->mapType($member['type']), $name);
            }
        }
        return $result;
    }

    /**
     * Convert SAP Netweaver RFC types into PHP/SAP types.
     * @param string $type The remote function parameter type.
     * @return string The PHP/SAP internal data type.
     * @throws SapLogicException
     */
    private function mapType(string $type): string
    {
        $mapping = [
            'RFCTYPE_DATE'      => IValue::TYPE_DATE,
            'RFCTYPE_TIME'      => IValue::TYPE_TIME,
            'RFCTYPE_INT'       => IValue::TYPE_INTEGER,
            'RFCTYPE_NUM'       => IValue::TYPE_INTEGER,
            'RFCTYPE_INT1'      => IValue::TYPE_INTEGER,
            'RFCTYPE_INT2'      => IValue::TYPE_INTEGER,
            'RFCTYPE_INT8'      => IValue::TYPE_INTEGER,
            'RFCTYPE_BCD'       => IValue::TYPE_STRING,
            'RFCTYPE_FLOAT'     => IValue::TYPE_FLOAT,
            'RFCTYPE_CHAR'      => IValue::TYPE_STRING,
            'RFCTYPE_STRING'    => IValue::TYPE_STRING,
            'RFCTYPE_BYTE'      => IValue::TYPE_HEXBIN,
            'RFCTYPE_XSTRING'   => IValue::TYPE_HEXBIN,
            'RFCTYPE_STRUCTURE' => IStruct::TYPE_STRUCT,
            'RFCTYPE_TABLE'     => ITable::TYPE_TABLE
        ];
        if (!array_key_exists($type, $mapping)) {
            throw new SapLogicException(sprintf('Unknown SAP Netweaver RFC type \'%s\'!', $type));
        }
        return $mapping[$type];
    }

    /**
     * Convert SAP Netweaver RFC directions into PHP/SAP directions.
     * @param string $direction The remote function parameter direction.
     * @return string The PHP/SAP internal direction.
     * @throws SapLogicException
     */
    private function mapDirection(string $direction): string
    {
        $mapping = [
            'RFC_EXPORT'   => IApiElement::DIRECTION_OUTPUT,
            'RFC_IMPORT'   => IApiElement::DIRECTION_INPUT,
            'RFC_CHANGING' => IApiElement::DIRECTION_CHANGING,
            'RFC_TABLES'   => ITable::DIRECTION_TABLE
        ];
        if (!array_key_exists($direction, $mapping)) {
            throw new SapLogicException(sprintf('Unknown SAP Netweaver RFC direction \'%s\'!', $direction));
        }
        return $mapping[$direction];
    }
}
