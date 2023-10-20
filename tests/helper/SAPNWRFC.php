<?php
/**
 * phpcs:ignoreFile
 * @codingStandardsIgnoreFile
 * @noinspection ALL
 */
/**
 * THIS FILE HAS BLUNTLY BEEN COPIED FROM
 * https://github.com/gkralik/php7-sapnwrfc
 * WHERE IT HAS BEEN PUBLISHED UNDER THE MIT LICENSE.
 */

/**
 * This is a stub file of the extensions public interface to enable
 * code completion in IDEs.
 */
namespace SAPNWRFC;

/**
 * Either run tests using this mock of the sapnwrfc class or run the tests with the
 * actual module and an actual SAP system.
 */
if (extension_loaded('sapnwrfc')) {
    throw new \RuntimeException('PHP module sapnwrfc is loaded. Cannot run tests using mockups.');
}

/**
 * Clear the function desc cache for $functionName.
 *
 * Clears the default repository. If $repositoryId is passed, the cache
 * repository matching $repository is cleard as well.
 *
 * @since 1.3.0
 *
 * @param string $functionName Function name.
 * @param string $repositoryId The cache repository to use. Defaults to null.
 *                             If passed, the repository is cleared in addition
 *                             to the default repository.
 *
 * @return bool True if the call succeeded (this does not necessarily mean
 *              that a cache entry was found).
 *
 */
function clearFunctionDescCache(string $functionName, string $repositoryId = null): bool
{
    $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()
        ->get(__FUNCTION__);
    return $func($functionName, $repositoryId);
}

/**
 * Class Exception
 * @package SAPNWRFC
 */
class Exception extends \RuntimeException
{
    /**
     * Detailed RFC/ABAP error information.
     *
     * $errorInfo contains at least the "code", "key" and "message" keys from
     * the RFC library.
     * If the error is an ABAP error, the following additional keys are
     * available:
     *    "abapMsgClass", "abapMsgType", "abapMsgNumber", "abapMsgV1",
     *    "abapMsgV2", "abapMsgV3", "abapMsgV4"
     *
     * @var array
     */
    protected $errorInfo = [];

    /**
     * Getter for the errorInfo property.
     *
     * @returns array|null
     */
    public function getErrorInfo(): array
    {
        return $this->errorInfo;
    }
}

/**
 * Class ConnectionException
 * @package SAPNWRFC
 */
class ConnectionException extends Exception
{
}

/**
 * Class FunctionCallException
 * @package SAPNWRFC
 */
class FunctionCallException extends Exception
{
}

/**
 * Class Connection
 * @package SAPNWRFC
 */
class Connection
{
    /**
     * Disable SAP remote function call tracing.
     */
    public const TRACE_LEVEL_OFF = 0;
    /**
     * Brief tracing of SAP remote function calls.
     */
    public const TRACE_LEVEL_BRIEF = 1;
    /**
     * Verbose tracing of SAP remote function calls.
     */
    public const TRACE_LEVEL_VERBOSE = 2;
    /**
     * Debug-like tracing of SAP remote function calls.
     */
    public const TRACE_LEVEL_FULL = 3;

    /**
     * Connect to the system using the given parameters.
     *
     * @param array $parameters Connection parameters (see `sapnwrfc.ini` documentation for supported keys)
     * @param array $options Additional options {
     *      @var bool $use_function_desc_cache Use function desc cache (defaults to `true`)
     * }
     *
     * @throws ConnectionException if the connection fails.
     */
    public function __construct(array $parameters, array $options = [])
    {
        $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()
            ->get('\SAPNWRFC\Connection::' . __FUNCTION__);
        $func($parameters, $options);
    }

    /**
     * Get the connection attributes.
     *
     * @return array Array of connection attributes.
     *
     * @throws ConnectionException if the connection attributes could not be
     *                             fetched.
     */
    public function getAttributes(): array
    {
        $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()
            ->get('\SAPNWRFC\Connection::' . __FUNCTION__);
        return $func();
    }

    /**
     * @return bool True if ping successful.
     *
     * @throws ConnectionException if the ping failed.
     */
    public function ping(): bool
    {
        $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()
            ->get('\SAPNWRFC\Connection::' . __FUNCTION__);
        return $func();
    }

    /**
     * Lookup a RFC function and return a RemoteFunction object.
     *
     * @param string $functionnName Name of the function.
     *
     * @return RemoteFunction A RemoteFunction class for the RFC function.
     *
     * @throws FunctionCallException if the lookup fails or an error is
     *                               returned during parameter parsing.
     */
    public function getFunction(string $functionName): RemoteFunction
    {
        $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()
            ->get('\SAPNWRFC\Connection::' . __FUNCTION__);
        return $func($functionName);
    }

    /**
     * Close the connection.
     *
     * @return bool True if the connection was closed, false if the connection
     *              is closed already.
     *
     * @throws ConnectionException if the connection could not be closed.
     */
    public function close(): bool
    {
        $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()
            ->get('\SAPNWRFC\Connection::' . __FUNCTION__);
        return $func();
    }

    /**
     * Sets the path to the sapnwrfc.ini file.
     *
     * By default, the INI file is searched for in the current directory.
     *
     * @param string $path Path to the sapnwrfc.ini file.
     *
     * @return bool True if path was set.
     *
     * @throws ConnectionException if path could not be set.
     */
    public static function setIniPath(string $path): bool
    {
        $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()
            ->get('\SAPNWRFC\Connection::' . __FUNCTION__);
        return $func($path);
    }

    /**
     * Reload the INI file.
     *
     * Searches for the INI file either in the path set by
     * Connection::setIniFile() or in the current directory.
     *
     * @return bool True if INI file was reloaded.
     *
     * @throws ConnectionException if the INI file could not be reloaded.
     */
    public static function reloadIniFile(): bool
    {
        $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()
            ->get('\SAPNWRFC\Connection::' . __FUNCTION__);
        return $func();
    }

    /**
     * Set trace directory.
     *
     * @param string $path Path to trace directory (must exist).
     *
     * @return bool True if path was set.
     *
     * @throws ConnectionException if path could not be set.
     */
    public static function setTraceDir(string $path): bool
    {
        $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()
            ->get('\SAPNWRFC\Connection::' . __FUNCTION__);
        return $func($path);
    }

    /**
     * Set trace level.
     *
     * @param int $level Trace level.
     *
     * @return bool True if level was set.
     *
     * @throws ConnectionException if level could not be set.
     */
    public static function setTraceLevel(int $level): bool
    {
        $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()
            ->get('\SAPNWRFC\Connection::' . __FUNCTION__);
        return $func($level);
    }

    /**
     * Get the extension version.
     *
     * @return string The extension version.
     */
    public static function version(): string
    {
        return 'SAPNWRFC MOCKUP 1.0';
    }

    /**
     * Get the RFC SDK version.
     *
     * @return string The RFC SDK version.
     */
    public static function rfcVersion(): string
    {
        return 'SAP Netweaver RFC SDK MOCKUP 1.0';
    }
}

/**
 * Class RemoteFunction
 * @package SAPNWRFC
 */
class RemoteFunction
{
    /**
     * RemoteFunction constructor.
     * @param string $name function name
     */
    public function __construct($name)
    {
        $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()
            ->get('\SAPNWRFC\RemoteFunction::' . __FUNCTION__);
        $func($name);
    }

    /**
     * Invoke the RFC function.
     *
     * @param array $parameters Function parameters.
     * @param array $options Additional invoke options {
     *      @var bool $rtrim Right trim CHAR field values.
     * }
     * @return array Return value from the backend.
     *
     * @throws FunctionCallException if any error occurs during execution.
     */
    public function invoke(array $parameters = [], array $options = []): array
    {
        $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()
            ->get('\SAPNWRFC\RemoteFunction::' . __FUNCTION__);
        return $func($parameters, $options);
    }

    /**
     * Make a parameter active or inactive.
     *
     * @param string $parameterName The parameter to modify.
     * @param bool   $isActive      True to activate the parameter, false to deactivate.
     *
     * @throws FunctionCallException if the parameter status could not be set.
     */
    public function setParameterActive(string $parameterName, bool $isActive)
    {
        $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()
            ->get('\SAPNWRFC\RemoteFunction::' . __FUNCTION__);
        $func($parameterName, $isActive);
    }

    /**
     * Check if a parameter is active or inactive.
     *
     * @param string $parameterName The parameter to check.
     *
     * @return bool True if parameter is active, false if not.
     */
    public function isParameterActive(string $parameterName): bool
    {
        $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()
            ->get('\SAPNWRFC\RemoteFunction::' . __FUNCTION__);
        return $func($parameterName);
    }

    /**
     * Return the SAP remote function API description as array.
     *
     * @return array
     */
    public function getFunctionDescription(): array
    {
        $func = \phpsap\IntegrationTests\SapRfcModuleMocks::singleton()
            ->get('\SAPNWRFC\RemoteFunction::' . __FUNCTION__);
        return $func();
    }
}
