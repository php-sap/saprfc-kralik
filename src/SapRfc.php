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
use phpsap\interfaces\Config\IConfiguration;
use phpsap\interfaces\exceptions\IIncompleteConfigException;
use phpsap\interfaces\exceptions\IInvalidArgumentException;
use phpsap\saprfc\Traits\ApiTrait;
use phpsap\saprfc\Traits\ConfigTrait;
use phpsap\saprfc\Traits\ParamTrait;
use SAPNWRFC\Connection;
use SAPNWRFC\ConnectionException as ModuleConnectionException;
use SAPNWRFC\FunctionCallException as ModuleFunctionCallException;
use SAPNWRFC\RemoteFunction;
use TypeError;

use function array_merge;
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
     * @var Connection|null
     */
    private ?Connection $connection = null;

    /**
     * @var RemoteFunction|null
     */
    private ?RemoteFunction $function = null;

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
                    $exception->getCode()
                );
            }
            /**
             * Create a new connection resource.
             */
            try {
                if ($config->getTrace() !== null) {
                    /**
                     * SAPNWRFC introduced TRACE_DETAILED (3) in v2.1.0 which
                     * is not available via the interface.
                     */
                    $trace = match ($config->getTrace()) {
                        IConfiguration::TRACE_FULL => 4,
                        IConfiguration::TRACE_VERBOSE => 2,
                        IConfiguration::TRACE_BRIEF => 1,
                        default => 0,
                    };
                    Connection::setTraceLevel($trace);
                }
                $this->connection = new Connection($moduleConfig);
            } catch (TypeError | ModuleConnectionException $exception) {
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
        $api = new RemoteApi();
        foreach ($this->saprfcFunctionInterface() as $name => $element) {
            try {
                $api->add($this->createApiElement(
                    strtoupper($name),
                    $this->mapType($element['type']),
                    $this->mapDirection($element['direction']),
                    $element
                ));
            } catch (IInvalidArgumentException | SapLogicException $exception) {
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
        return $this->getFunction()->getFunctionDescription();
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
                array_merge(
                    $this->getApi()->getInputElements(),
                    $this->getApi()->getChangingElements()
                ),
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
        } catch (TypeError | ModuleFunctionCallException $exception) {
            throw new FunctionCallException(sprintf(
                'Function call %s failed: %s',
                $this->getName(),
                $exception->getMessage()
            ), 0, $exception);
        }
        /**
         * Typecast the return values.
         */
        return $this->castOutput(array_merge(
            $this->getApi()->getOutputElements(),
            $this->getApi()->getChangingElements(),
            $this->getApi()->getTables()
        ), $result);
    }
}
