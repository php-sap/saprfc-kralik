<?php

declare(strict_types=1);

namespace phpsap\saprfc\Traits;

use phpsap\classes\Api\Element;
use phpsap\classes\Api\Struct;
use phpsap\classes\Api\Table;
use phpsap\classes\Api\Value;
use phpsap\exceptions\SapLogicException;
use phpsap\interfaces\Api\IElement;
use phpsap\interfaces\Api\IStruct;
use phpsap\interfaces\Api\ITable;
use phpsap\interfaces\Api\IValue;
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
     * @param string $name      The name of the parameter or return value.
     * @param string $type      The type of the parameter or return value.
     * @param string $direction The direction indicating whether it's a parameter or
     *                          return value.
     * @param array $def       The complete API value defintion from the module.
     * @return Value|Struct|Table
     */
    private function createApiValue(string $name, string $type, string $direction, array $def)
    {
        $optional = $def['optional'];
        if ($type === ITable::TYPE_TABLE) {
            return new Table($name, $direction, $optional, $this->createMembers($def));
        }
        if ($type === IStruct::TYPE_STRUCT) {
            return new Struct($name, $direction, $optional, $this->createMembers($def));
        }
        return new Value($type, $name, $direction, $optional);
    }

    /**
     * Create either struct or table members from the def array of the remote function API.
     * @param array $def The complete API value defintion.
     * @return Element[] An array of IElement compatible objects.
     * @throws SapLogicException In case a datatype is missing in the mappings array.
     */
    private function createMembers(array $def): array
    {
        $result = [];
        if (array_key_exists('typedef', $def) && is_array($def['typedef'])) {
            foreach ($def['typedef'] as $name => $member) {
                $result[] = new Element($this->mapType($member['type']), $name);
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
            'RFCTYPE_DATE'      => IElement::TYPE_DATE,
            'RFCTYPE_TIME'      => IElement::TYPE_TIME,
            'RFCTYPE_INT'       => IElement::TYPE_INTEGER,
            'RFCTYPE_NUM'       => IElement::TYPE_INTEGER,
            'RFCTYPE_INT1'      => IElement::TYPE_INTEGER,
            'RFCTYPE_INT2'      => IElement::TYPE_INTEGER,
            'RFCTYPE_BCD'       => IElement::TYPE_FLOAT,
            'RFCTYPE_FLOAT'     => IElement::TYPE_FLOAT,
            'RFCTYPE_CHAR'      => IElement::TYPE_STRING,
            'RFCTYPE_STRING'    => IElement::TYPE_STRING,
            'RFCTYPE_BYTE'      => IElement::TYPE_HEXBIN,
            'RFCTYPE_XSTRING'   => IElement::TYPE_HEXBIN,
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
            'RFC_EXPORT' => IValue::DIRECTION_OUTPUT,
            'RFC_IMPORT' => IValue::DIRECTION_INPUT,
            'RFC_TABLES' => ITable::DIRECTION_TABLE
        ];
        if (!array_key_exists($direction, $mapping)) {
            throw new SapLogicException(sprintf('Unknown SAP Netweaver RFC direction \'%s\'!', $direction));
        }
        return $mapping[$direction];
    }
}
