<?php

namespace tests\phpsap\saprfc\Traits;

use phpsap\saprfc\SapRfc;

/**
 * Trait TestCaseTrait
 *
 * Collect methods common to all test cases extending the integration tests.
 *
 * @package tests\phpsap\saprfc
 * @author Gregor J.
 * @license MIT
 */
trait TestCaseTrait
{
    /**
     * Return the name of the class, used for testing.
     * @return string
     */
    public static function getClassName(): string
    {
        return SapRfc::class;
    }

    /**
     * Get the name of the PHP module.
     * @return string
     */
    public static function getModuleName(): string
    {
        return 'sapnwrfc';
    }

    /**
     * Get the path to the PHP/SAP configuration file.
     * @return string
     */
    public static function getSapConfigFile(): string
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'sap.json';
    }

    /**
     * Get the path to the filename containing the SAP RFC module mockups.
     * @return string
     */
    public static function getModuleTemplateFile(): string
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'helper' . DIRECTORY_SEPARATOR . 'SAPNWRFC.php';
    }

    /**
     * Get an array of valid SAP RFC module function or class method names.
     * @return array
     */
    public static function getValidModuleFunctions(): array
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
            '\SAPNWRFC\RemoteFunction::isParameterActive',
            '\SAPNWRFC\RemoteFunction::getFunctionDescription'
        ];
    }

    /**
     * Remove sapnwrfc trace file.
     */
    protected function tearDown(): void
    {
        $traceFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'dev_rfc.trc';
        if (file_exists($traceFile)) {
            unlink($traceFile);
        }
        parent::tearDown();
    }
}
