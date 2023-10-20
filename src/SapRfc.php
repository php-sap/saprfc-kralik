<?php

declare(strict_types=1);

namespace phpsap\saprfc;

use phpsap\classes\AbstractFunction;
use phpsap\classes\Api\RemoteApi;
use phpsap\exceptions\ConnectionFailedException;
use phpsap\exceptions\FunctionCallException;
use phpsap\exceptions\IncompleteConfigException;
use phpsap\exceptions\SapLogicException;
use phpsap\exceptions\UnknownFunctionException;
use phpsap\interfaces\exceptions\IIncompleteConfigException;
use phpsap\interfaces\exceptions\IInvalidArgumentException;
use phpsap\saprfc\Traits\ApiTrait;
use phpsap\saprfc\Traits\ConfigTrait;
use phpsap\saprfc\Traits\ParamTrait;
use SAPNWRFC\Connection;
use SAPNWRFC\ConnectionException as ModuleConnectionException;
use SAPNWRFC\FunctionCallException as ModuleFunctionCallException;
use SAPNWRFC\RemoteFunction;
use function array_merge;
use function get_object_vars;
use function method_exists;
use function sprintf;

/**
 * Class SapRfc
 * @package phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
class SapRfc extends AbstractFunction
{
    use ApiTrait;
    use ConfigTrait;
    use ParamTrait;

    /**
     * @var Connection
     */
    private Connection $connection;

    /**
     * @var RemoteFunction
     */
    private RemoteFunction $function;

    /**
     * @var array Which options to use for invoke() method of the module.
     */
    private static array $invokeOptions = [
        'rtrim' => true
    ];

    /**
     * Cleanup method.
     */
    public function __destruct()
    {
        if ($this->function !== null) {
            $this->function = null;
        }
        if ($this->connection !== null) {
            $this->connection->close();
            $this->connection = null;
        }
    }

    /**
     * Create a remote function call resource.
     * @return RemoteFunction
     * @throws ConnectionFailedException
     * @throws IncompleteConfigException
     * @throws UnknownFunctionException
     */
    protected function getFunction(): RemoteFunction
    {
        if ($this->function === null) {
            /**
             * Create a new function resource.
             */
            try {
                $this->function = $this
                    ->getConnection()
                    ->getFunction($this->getName());
            } catch (ModuleFunctionCallException $exception) {
                throw new UnknownFunctionException(sprintf(
                    'Unknown function %s: %s',
                    $this->getName(),
                    $exception->getMessage()
                ), 0, $exception);
            }
        }
        return $this->function;
    }

    /**
     * Open a connection in case it hasn't been done yet and return the
     * connection resource.
     * @return Connection
     * @throws ConnectionFailedException
     * @throws IncompleteConfigException
     */
    protected function getConnection(): Connection
    {
        if ($this->connection === null) {
            /**
             * In case the is no configuration, throw an exception.
             */
            if (($config = $this->getConfiguration()) === null) {
                throw new IncompleteConfigException(
                    'Configuration is missing!'
                );
            }
            /**
             * Catch generic IIncompleteConfigException interface and throw the
             * actual exception class of this repository.
             */
            try {
                $moduleConfig = $this->getModuleConfig($config);
            } catch (IIncompleteConfigException $exception) {
                throw new IncompleteConfigException(
                    $exception->getMessage(),
                    $exception->getCode(),
                    $exception
                );
            }
            /**
             * Create a new connection resource.
             */
            try {
                if ($config->getTrace() !== null) {
                    /**
                     * \SAPNWRFC\Connection::TRACE_LEVEL_* uses the same values as
                     * \phpsap\interfaces\Config\IConfigCommon::TRACE_*. Therefore
                     * no mapping is necessary.
                     */
                    Connection::setTraceLevel($config->getTrace());
                }
                $this->connection = new Connection($moduleConfig);
            } catch (ModuleConnectionException $exception) {
                throw new ConnectionFailedException(sprintf(
                    'Connection creation failed: %s',
                    $exception->getMessage()
                ), 0, $exception);
            }
        }
        return $this->connection;
    }

    /**
     * @inheritDoc
     */
    public function extractApi(): RemoteApi
    {
        /**
         * InvalidArgumentException is never thrown, because no parameter is given.
         */
        $api = new RemoteApi();
        foreach ($this->saprfcFunctionInterface() as $name => $element) {
            try {
                $api->add($this->createApiValue(
                    strtoupper($name),
                    $this->mapType($element['type']),
                    $this->mapDirection($element['direction']),
                    $element
                ));
            } catch (SapLogicException $exception) {
                /**
                 * InvalidArgumentException is a child of SapLogicException and will
                 * be caught too.
                 */
                throw new ConnectionFailedException(
                    'The API behaved unexpectedly: ' . $exception->getMessage(),
                    $exception->getCode(),
                    $exception
                );
            }
        }
        return $api;
    }

    /**
     * Extract the remote function API from the function object and remove
     * unwanted variables.
     * @return array
     * @throws ConnectionFailedException
     * @throws IncompleteConfigException
     * @throws UnknownFunctionException
     */
    public function saprfcFunctionInterface(): array
    {
        $function = $this->getFunction();
        if (method_exists($function, 'getFunctionDescription')) {
            return $function->getFunctionDescription();
        }
        $result = get_object_vars($function);
        unset($result['name']);
        return $result;
    }

    /**
     * @inheritDoc
     * @throws IInvalidArgumentException
     */
    public function invoke(): array
    {
        /**
         * Merge value and table parameters into one parameter array.
         */
        $params = array_merge(
            $this->getInputParams(
                $this->getApi()->getInputValues(),
                $this->getParams()
            ),
            $this->getTableParams(
                $this->getApi()->getTables(),
                $this->getParams()
            )
        );
        /**
         * Invoke SAP remote function call.
         */
        try {
            $result = $this
                ->getFunction()
                ->invoke($params, self::$invokeOptions);
        } catch (ModuleFunctionCallException $exception) {
            throw new FunctionCallException(sprintf(
                'Function call %s failed: %s',
                $this->getName(),
                $exception->getMessage()
            ), 0, $exception);
        }
        /**
         * Typecast the return values.
         */
        return $this->castOutputValues(array_merge(
            $this->getApi()->getOutputValues(),
            $this->getApi()->getTables()
        ), $result);
    }
}
