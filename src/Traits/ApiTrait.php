<?php

namespace phpsap\saprfc\Traits;

use phpsap\classes\Api\Struct;
use phpsap\classes\Api\Table;
use phpsap\classes\Api\Value;
use phpsap\exceptions\SapLogicException;
use phpsap\interfaces\Api\IArray;
use phpsap\interfaces\Api\IElement;
use phpsap\interfaces\Api\IValue;

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
     * @param bool   $optional  The flag, whether this parameter or return value is
     *                          required.
     * @return Value|Struct|Table
     * @throws \phpsap\exceptions\SapLogicException
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    private function createApiValue($name, $type, $direction, $optional)
    {
        if ($direction === IArray::DIRECTION_TABLE) {
            /**
             * The members array is empty because there is no information about it
             * from the sapnwrfc module class.
             * @todo Write to Gregor Kralik.
             */
            return new Table($name, $optional, []);
        }
        if ($type === IArray::TYPE_ARRAY) {
            /**
             * The members array is empty because there is no information about it
             * from the sapnwrfc module class.
             * @todo Write to Gregor Kralik.
             */
            return new Struct($name, $direction, $optional, []);
        }
        return new Value($type, $name, $direction, $optional);
    }

    /**
     * Convert SAP Netweaver RFC types into PHP/SAP types.
     * @param string $type The remote function parameter type.
     * @return string The PHP/SAP internal data type.
     * @throws \phpsap\exceptions\SapLogicException
     */
    private function mapType($type): string
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
            'RFCTYPE_STRUCTURE' => IArray::TYPE_ARRAY,
            'RFCTYPE_TABLE'     => IArray::TYPE_ARRAY
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
     * @throws \phpsap\exceptions\SapLogicException
     */
    private function mapDirection($direction): string
    {
        $mapping = [
            'RFC_EXPORT' => IValue::DIRECTION_OUTPUT,
            'RFC_IMPORT' => IValue::DIRECTION_INPUT,
            'RFC_TABLES' => IArray::DIRECTION_TABLE
        ];
        if (!array_key_exists($direction, $mapping)) {
            throw new SapLogicException(sprintf('Unknown SAP Netweaver RFC direction \'%s\'!', $direction));
        }
        return $mapping[$direction];
    }
}
