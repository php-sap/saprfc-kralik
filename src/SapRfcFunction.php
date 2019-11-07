<?php
/**
 * File src/SapRfcFunction.php
 *
 * PHP/SAP remote function calls using Gregor Kraliks sapnwrfc module.
 *
 * @package saprfc-kralik
 * @author  Gregor J.
 * @license MIT
 */

namespace phpsap\saprfc;

use phpsap\classes\AbstractFunction;
use phpsap\classes\Api\Struct;
use phpsap\classes\Api\Table;
use phpsap\classes\Api\Value;
use phpsap\classes\RemoteApi;
use phpsap\exceptions\FunctionCallException;
use phpsap\exceptions\UnknownFunctionException;
use phpsap\interfaces\Api\IArray;
use phpsap\interfaces\Api\IElement;
use phpsap\interfaces\Api\IValue;

/**
 * Class phpsap\saprfc\SapRfcFunction
 *
 * PHP/SAP remote function class abstracting remote function call related functions
 * using Gregor Kraliks sapnwrfc module.
 *
 * @package phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
class SapRfcFunction extends AbstractFunction
{
    /**
     * @var \SAPNWRFC\Connection
     */
    protected $connection;

    /**
     * @var \SAPNWRFC\RemoteFunction
     */
    protected $function;

    /**
     * @var array Which options to use for invoke() method of the module.
     */
    private static $invokeOptions = [
        'rtrim' => true
    ];

    /**
     * Clear remote function call.
     */
    public function __destruct()
    {
        if ($this->function !== null) {
            $this->function = null;
        }
    }

    /**
     * Execute the prepared function call.
     * @return array
     * @throws \phpsap\exceptions\ConnectionFailedException
     * @throws \phpsap\exceptions\FunctionCallException
     */
    public function invoke():array
    {
        try {
            $result = $this->function->invoke($this->params, static::$invokeOptions);
        } catch (\Exception $exception) {
            throw new FunctionCallException(sprintf(
                'Function call %s failed: %s',
                $this->getName(),
                $exception->getMessage()
            ), 0, $exception);
        }
        //typecast the result...
        return $this->cast($result, array_merge(
            $this->getApi()->getOutputValues(),
            $this->getApi()->getTables()
        ));
    }

    /**
     * Typecast the results array.
     * @param array    $results
     * @param IValue[] $apiValues
     * @return array
     */
    private function cast(array $results, array $apiValues):array
    {
        foreach ($apiValues as $apiValue) {
            $name = $apiValue->getName();
            if (array_key_exists($name, $results)) {
                $results[$name] = $apiValue->cast($results[$name]);
            }
        }
        return $results;
    }

    /**
     * Lookup SAP remote function and return an module class instance of it.
     * @return \SAPNWRFC\RemoteFunction
     * @throws \phpsap\exceptions\UnknownFunctionException
     */
    protected function getFunction()
    {
        try {
            return $this->connection->getFunction($this->getName());
        } catch (\Exception $exception) {
            throw new UnknownFunctionException(sprintf(
                'Unknown function %s: %s',
                $this->getName(),
                $exception->getMessage()
            ), 0, $exception);
        }
    }

    /**
     * Extract the remote function API and return an API description class.
     * @return RemoteApi
     */
    public function extractApi():RemoteApi
    {
        $api = new RemoteApi();
        foreach ($this->saprfcFunctionInterface() as $name => $element) {
            $api->add($this->createApiValue(
                strtoupper($name),
                $this->mapType($element['type']),
                $this->mapDirection($element['direction']),
                $element['optional']
            ));
        }
        return $api;
    }

    /**
     * Extract the remote function API from the function object and remove unwanted variables.
     * @return array
     */
    public function saprfcFunctionInterface():array
    {
        $result = get_object_vars($this->function);
        unset($result['name']);
        return $result;
    }

    /**
     * Create either Value, Struct or Table from a given remote function parameter or return value.
     * @param string $name The name of the parameter or return value.
     * @param string $type The type of the parameter or return value.
     * @param string $direction The direction indicating whether it's a parameter or return value.
     * @param bool $optional The flag, whether this parameter or return value is required.
     * @return Value|Struct|Table
     */
    private function createApiValue($name, $type, $direction, $optional):IValue
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
     * @throws \LogicException In case the given SAP Netweaver RFC type is missing in the mapping table.
     */
    private function mapType($type):string
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
            throw new LogicException(sprintf('Unknown SAP Netweaver RFC type \'%s\'!', $type));
        }
        return $mapping[$type];
    }

    /**
     * Convert SAP Netweaver RFC directions into PHP/SAP directions.
     * @param string $direction The remote function parameter direction.
     * @return string The PHP/SAP internal direction.
     * @throws \LogicException In case the given SAP Netweaver RFC direction is missing in the mapping table.
     */
    private function mapDirection($direction):string
    {
        $mapping = [
            'RFC_EXPORT' => IValue::DIRECTION_OUTPUT,
            'RFC_IMPORT' => IValue::DIRECTION_INPUT,
            'RFC_TABLES' => IArray::DIRECTION_TABLE
        ];
        if (!array_key_exists($direction, $mapping)) {
            throw new LogicException(sprintf('Unknown SAP Netweaver RFC direction \'%s\'!', $direction));
        }
        return $mapping[$direction];
    }
}
