<?php
/**
 * File tests/SapRfcTestCaseTrait.php
 *
 * Implement methods of phpsap\IntegrationTests\AbstractTestCase
 *
 * @package saprfc-kralik
 * @author  Gregor J.
 * @license MIT
 */

namespace tests\phpsap\saprfc;

use phpsap\saprfc\SapRfcConfigA;
use phpsap\saprfc\SapRfcConnection;

/**
 * Trait tests\phpsap\saprfc\SapRfcTestCaseTrait
 *
 * Implement methods of phpsap\IntegrationTests\AbstractTestCase
 *
 * @package tests\phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
trait SapRfcTestCaseTrait
{
    /**
     * Get the name of the PHP module.
     * @return string
     */
    public function getModuleName()
    {
        return 'sapnwrfc';
    }

    /**
     * Get the path to the PHP/SAP configuration file.
     * @return string
     */
    public function getSapConfigFile()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'sap.json';
    }

    /**
     * Get the path to the filename containing the SAP RFC module mockups.
     * @return string
     */
    public function getModuleTemplateFile()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'helper' . DIRECTORY_SEPARATOR . 'SAPNWRFC.php';
    }

    /**
     * Get an array of valid SAP RFC module function or class method names.
     * @return array
     */
    public function getValidModuleFunctions()
    {
        return [
            'clearFunctionDescCache',
            '\SAPNWRFC\Connection::__construct',
            '\SAPNWRFC\Connection::getAttributes',
            '\SAPNWRFC\Connection::ping',
            '\SAPNWRFC\Connection::getFunction',
            '\SAPNWRFC\Connection::close',
            '\SAPNWRFC\Connection::setIniPath',
            '\SAPNWRFC\Connection::reloadIniFile',
            '\SAPNWRFC\Connection::setTraceDir',
            '\SAPNWRFC\Connection::setTraceLevel',
            '\SAPNWRFC\Connection::version',
            '\SAPNWRFC\Connection::rfcVersion',
            '\SAPNWRFC\RemoteFunction::__construct',
            '\SAPNWRFC\RemoteFunction::invoke',
            '\SAPNWRFC\RemoteFunction::setParameterActive',
            '\SAPNWRFC\RemoteFunction::isParameterActive'
        ];
    }

    /**
     * Create a new instance of a PHP/SAP connection class.
     * @param array|string|null $config The PHP/SAP configuration. Default: null
     * @return \phpsap\saprfc\SapRfcConnection
     */
    public function newConnection($config = null)
    {
        return new SapRfcConnection(new SapRfcConfigA($config));
    }

    /**
     * Clean up trace files after tests.
     */
    public function __destruct()
    {
        $traceFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'dev_rfc.trc';
        if (file_exists($traceFile)) {
            unlink($traceFile);
        }
    }
}
