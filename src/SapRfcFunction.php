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
use phpsap\exceptions\FunctionCallException;
use phpsap\exceptions\UnknownFunctionException;

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
    protected function execute()
    {
        try {
            return $this->function->invoke($this->params, static::$invokeOptions);
        } catch (\Exception $exception) {
            throw new FunctionCallException(sprintf(
                'Function call %s failed: %s',
                $this->getName(),
                $exception->getMessage()
            ), 0, $exception);
        }
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
}
