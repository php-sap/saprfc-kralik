<?php

namespace tests\phpsap\saprfc;

use phpsap\IntegrationTests\AbstractSapRfcTestCase;
use tests\phpsap\saprfc\Traits\TestCaseTrait;

/**
 * Class tests\phpsap\saprfc\SapRfcIntegrationTest
 *
 * Implement methods of the integration tests to mock SAP remote function
 * calls without an actual SAP system for testing.
 *
 * @package tests\phpsap\saprfc
 * @author Gregor J.
 * @license MIT
 */
class SapRfcIntegrationTest extends AbstractSapRfcTestCase
{
    use TestCaseTrait;

    /**
     * @var array The raw API of the RFC walk through test as seen by the module.
     */
    public static $rfcWalkThruTestApi = [
        'TEST_OUT' => [
            'type' => 'RFCTYPE_STRUCTURE',
            'direction' => 'RFC_EXPORT',
            'description' => 'muss nach Funktionsaufruf TEST_IN entsprechen',
            'optional' => false,
            'defaultValue' => '',
        ],
        'TEST_IN' => [
            'type' => 'RFCTYPE_STRUCTURE',
            'direction' => 'RFC_IMPORT',
            'description' => 'Struktur mit allen unterstützten Datentypen',
            'optional' => false,
            'defaultValue' => '',
        ],
        'DESTINATIONS' => [
            'type' => 'RFCTYPE_TABLE',
            'direction' => 'RFC_TABLES',
            'description' => 'Liste der aufzurufenden Destinations',
            'optional' => false,
            'defaultValue' => '',
        ],
        'LOG' => [
            'type' => 'RFCTYPE_TABLE',
            'direction' => 'RFC_TABLES',
            'description' => 'Log-Tabelle (Enthaelt Fehlermeldungen od. \'o.k\')',
            'optional' => false,
            'defaultValue' => '',
        ],
    ];

    /**
     * @var array raw API of RFC read table
     */
    public static $rfcReadTableApi = [
        'DELIMITER' => [
            'type' => 'RFCTYPE_CHAR',
            'direction' => 'RFC_IMPORT',
            'description' => 'Zeichen für Markierung von Feldgrenzen in DATA',
            'optional' => true,
            'defaultValue' => 'SPACE',
        ],
        'NO_DATA' => [
            'type' => 'RFCTYPE_CHAR',
            'direction' => 'RFC_IMPORT',
            'description' => 'falls <> SPACE, wird nur FIELDS gefüllt',
            'optional' => true,
            'defaultValue' => 'SPACE',
        ],
        'QUERY_TABLE' => [
            'type' => 'RFCTYPE_CHAR',
            'direction' => 'RFC_IMPORT',
            'description' => 'Tabelle, aus der gelesen wird',
            'optional' => false,
            'defaultValue' => '',
        ],
        'ROWCOUNT' => [
            'type' => 'RFCTYPE_INT',
            'direction' => 'RFC_IMPORT',
            'description' => '',
            'optional' => true,
            'defaultValue' => '0',
        ],
        'ROWSKIPS' => [
            'type' => 'RFCTYPE_INT',
            'direction' => 'RFC_IMPORT',
            'description' => '',
            'optional' => true,
            'defaultValue' => '0',
        ],
        'DATA' => [
            'type' => 'RFCTYPE_TABLE',
            'direction' => 'RFC_TABLES',
            'description' => 'gelesene Daten (out)',
            'optional' => false,
            'defaultValue' => '',
        ],
        'FIELDS' => [
            'type' => 'RFCTYPE_TABLE',
            'direction' => 'RFC_TABLES',
            'description' => 'Namen (in) und Struktur (out) gelesener Felder',
            'optional' => false,
            'defaultValue' => '',
        ],
        'OPTIONS' => [
            'type' => 'RFCTYPE_TABLE',
            'direction' => 'RFC_TABLES',
            'description' => 'Selektionsangaben, "WHERE-Klauseln" (in)',
            'optional' => false,
            'defaultValue' => '',
        ]
    ];

    /**
     * @inheritDoc
     */
    protected function mockConnectionFailed()
    {
        static::mock('\SAPNWRFC\Connection::__construct', static function (array $config, array $options) {
            throw new \SAPNWRFC\ConnectionException('mock failed connection');
        });
    }

    /**
     * @inheritDoc
     */
    protected function mockSuccessfulRfcPing()
    {
        $flags = new \stdClass();
        $flags->conn = false;
        $flags->func = null;
        $expectedConfig = static::getSampleSapConfig();
        static::mock('\SAPNWRFC\Connection::__construct', static function (array $config, array $options) use ($flags, $expectedConfig) {
            if (
                !is_array($config)
                || !array_key_exists('ashost', $config)
                || !array_key_exists('sysnr', $config)
                || !array_key_exists('client', $config)
                || !array_key_exists('user', $config)
                || !array_key_exists('passwd', $config)
                || $config['ashost'] !== $expectedConfig->getAshost()
                || $config['sysnr'] !== $expectedConfig->getSysnr()
                || $config['client'] !== $expectedConfig->getClient()
                || $config['user'] !== $expectedConfig->getUser()
                || $config['passwd'] !== $expectedConfig->getPasswd()
            ) {
                throw new \SAPNWRFC\ConnectionException('mock received invalid config array!');
            }
            //set flag that a connection has been established
            $flags->conn = true;
        });
        static::mock('\SAPNWRFC\Connection::close', static function () use ($flags) {
            //calling \SAPNWRFC\Connection::close twice has to fail
            if ($flags->conn !== true) {
                throw new \SAPNWRFC\ConnectionException('mock connection already closed!');
            }
            $flags->conn = false;
            return true;
        });
        static::mock('\SAPNWRFC\Connection::getFunction', static function ($name) {
            return new \SAPNWRFC\RemoteFunction($name);
        });
        static::mock('\SAPNWRFC\RemoteFunction::__construct', static function ($name) use ($flags) {
            if ($flags->conn !== true) {
                throw new \SAPNWRFC\FunctionCallException('mock connection not open!');
            }
            if ($name !== 'RFC_PING') {
                throw new \SAPNWRFC\FunctionCallException('expected RFC_PING as mock function name!');
            }
            $flags->func = $name;
        });
        static::mock('\SAPNWRFC\RemoteFunction::invoke', static function (array $params, array $options) use ($flags) {
            if ($flags->conn !== true) {
                throw new \SAPNWRFC\FunctionCallException('mock connection not open!');
            }
            if ($flags->func !== 'RFC_PING') {
                throw new \SAPNWRFC\FunctionCallException('mock function not correctly constructed!');
            }
            if (!empty($params)) {
                throw new \SAPNWRFC\FunctionCallException('mock RFC_PING received parameters! ' . json_encode($params));
            }
            return [];
        });
    }

    /**
     * @inheritDoc
     */
    protected function mockUnknownFunctionException()
    {
        $flags = new \stdClass();
        $flags->conn = false;
        $expectedConfig = static::getSampleSapConfig();
        static::mock('\SAPNWRFC\Connection::__construct', static function (array $config, array $options) use ($flags, $expectedConfig) {
            if (
                !is_array($config)
                || !array_key_exists('ashost', $config)
                || !array_key_exists('sysnr', $config)
                || !array_key_exists('client', $config)
                || !array_key_exists('user', $config)
                || !array_key_exists('passwd', $config)
                || $config['ashost'] !== $expectedConfig->getAshost()
                || $config['sysnr'] !== $expectedConfig->getSysnr()
                || $config['client'] !== $expectedConfig->getClient()
                || $config['user'] !== $expectedConfig->getUser()
                || $config['passwd'] !== $expectedConfig->getPasswd()
            ) {
                throw new \SAPNWRFC\ConnectionException('mock received invalid config array!');
            }
            //set flag that a connection has been established
            $flags->conn = true;
        });
        static::mock('\SAPNWRFC\Connection::close', static function () use ($flags) {
            //calling \SAPNWRFC\Connection::close twice has to fail
            if ($flags->conn !== true) {
                throw new \SAPNWRFC\ConnectionException('mock connection already closed!');
            }
            $flags->conn = false;
            return true;
        });
        static::mock('\SAPNWRFC\Connection::getFunction', static function ($name) {
            throw new \SAPNWRFC\FunctionCallException(sprintf('function %s not found', $name));
        });
    }

    /**
     * @inheritDoc
     */
    protected function mockRemoteFunctionCallWithParametersAndResults()
    {
        //Use an object for connection flag and function name.
        $flags = new \stdClass();
        $flags->conn = false;
        $flags->func = null;
        $flags->api = static::$rfcWalkThruTestApi;
        $expectedConfig = static::getSampleSapConfig();
        static::mock('\SAPNWRFC\Connection::__construct', static function (array $config, array $options) use ($flags, $expectedConfig) {
            if (
                !is_array($config)
                || !array_key_exists('ashost', $config)
                || !array_key_exists('sysnr', $config)
                || !array_key_exists('client', $config)
                || !array_key_exists('user', $config)
                || !array_key_exists('passwd', $config)
                || $config['ashost'] !== $expectedConfig->getAshost()
                || $config['sysnr'] !== $expectedConfig->getSysnr()
                || $config['client'] !== $expectedConfig->getClient()
                || $config['user'] !== $expectedConfig->getUser()
                || $config['passwd'] !== $expectedConfig->getPasswd()
            ) {
                throw new \SAPNWRFC\ConnectionException('mock received invalid config array!');
            }
            //set flag that a connection has been established
            $flags->conn = true;
        });
        static::mock('\SAPNWRFC\Connection::close', static function () use ($flags) {
            //calling \SAPNWRFC\Connection::close twice has to fail
            if ($flags->conn !== true) {
                throw new \SAPNWRFC\ConnectionException('mock connection already closed!');
            }
            $flags->conn = false;
            return true;
        });
        static::mock('\SAPNWRFC\RemoteFunction::__construct', static function ($name) use ($flags) {
            if ($flags->conn !== true) {
                throw new \SAPNWRFC\FunctionCallException('mock connection not open!');
            }
            if ($name !== 'RFC_WALK_THRU_TEST') {
                throw new \SAPNWRFC\FunctionCallException('expected RFC_WALK_THRU_TEST as mock function name!');
            }
            $flags->func = $name;
        });
        static::mock('\SAPNWRFC\Connection::getFunction', static function ($name) use ($flags) {
            if ($flags->conn !== true) {
                throw new \SAPNWRFC\FunctionCallException('mock connection not open!');
            }
            if ($name !== 'RFC_WALK_THRU_TEST') {
                throw new \SAPNWRFC\FunctionCallException('expected RFC_WALK_THRU_TEST as mock function name!');
            }
            $func = new \SAPNWRFC\RemoteFunction($name);
            //Assigning all the API values that are later gathered by get_object_vars().
            foreach ($flags->api as $key => $value) {
                $func->$key = $value;
            }
            return $func;
        });
        static::mock('\SAPNWRFC\RemoteFunction::invoke', static function (array $params, array $options) use ($flags) {
            if ($flags->conn !== true) {
                throw new \SAPNWRFC\FunctionCallException('mock connection not open!');
            }
            if ($flags->func !== 'RFC_WALK_THRU_TEST') {
                throw new \SAPNWRFC\FunctionCallException('function not correctly initialized!');
            }
            return [
                'TEST_OUT' => [
                    'RFCFLOAT' => 70.109999999999999,
                    'RFCCHAR1' => 'A',
                    'RFCINT2' => 4095,
                    'RFCINT1' => 163,
                    'RFCCHAR4' => 'QqMh',
                    'RFCINT4' => 416639,
                    'RFCHEX3' => '53' . "\0" . '',
                    'RFCCHAR2' => 'XC',
                    'RFCTIME' => '102030',
                    'RFCDATE' => '20191030',
                    'RFCDATA1' => 'qKWjmNfad32rfS9Z                                  ',
                    'RFCDATA2' => 'xi82ph2zJ8BCVtlR                                  '
                ],
                'DESTINATIONS' => [],
                'LOG' => [
                    [
                        'RFCDEST' => 'AOP3                            ',
                        'RFCWHOAMI' => 'pzjti000                        ',
                        'RFCLOG' => 'FAP-RytEHBsRYKX AOP3 eumqvMJD ZLqovj.                                 '
                    ]
                ]
            ];
        });
    }

    /**
     * @inheritDoc
     */
    protected function mockFailedRemoteFunctionCallWithParameters()
    {
        //Use an object for connection flag and function name.
        $flags = new \stdClass();
        $flags->conn = false;
        $flags->func = null;
        $flags->api = static::$rfcReadTableApi;
        $expectedConfig = static::getSampleSapConfig();
        static::mock('\SAPNWRFC\Connection::__construct', static function (array $config, array $options) use ($flags, $expectedConfig) {
            if (
                !is_array($config)
                || !array_key_exists('ashost', $config)
                || !array_key_exists('sysnr', $config)
                || !array_key_exists('client', $config)
                || !array_key_exists('user', $config)
                || !array_key_exists('passwd', $config)
                || $config['ashost'] !== $expectedConfig->getAshost()
                || $config['sysnr'] !== $expectedConfig->getSysnr()
                || $config['client'] !== $expectedConfig->getClient()
                || $config['user'] !== $expectedConfig->getUser()
                || $config['passwd'] !== $expectedConfig->getPasswd()
            ) {
                throw new \SAPNWRFC\ConnectionException('mock received invalid config array!');
            }
            //set flag that a connection has been established
            $flags->conn = true;
        });
        static::mock('\SAPNWRFC\Connection::close', static function () use ($flags) {
            //calling \SAPNWRFC\Connection::close twice has to fail
            if ($flags->conn !== true) {
                throw new \SAPNWRFC\ConnectionException('mock connection already closed!');
            }
            $flags->conn = false;
            return true;
        });
        static::mock('\SAPNWRFC\RemoteFunction::__construct', static function ($name) use ($flags) {
            if ($flags->conn !== true) {
                throw new \SAPNWRFC\FunctionCallException('mock connection not open!');
            }
            if ($name !== 'RFC_READ_TABLE') {
                throw new \SAPNWRFC\FunctionCallException('expected RFC_READ_TABLE as mock function name!');
            }
            $flags->func = $name;
        });
        static::mock('\SAPNWRFC\Connection::getFunction', static function ($name) use ($flags) {
            if ($flags->conn !== true) {
                throw new \SAPNWRFC\FunctionCallException('mock connection not open!');
            }
            if ($name !== 'RFC_READ_TABLE') {
                throw new \SAPNWRFC\FunctionCallException('expected RFC_READ_TABLE as mock function name!');
            }
            $func = new \SAPNWRFC\RemoteFunction($name);
            //Assigning all the API values that are later gathered by get_object_vars().
            foreach ($flags->api as $key => $value) {
                $func->$key = $value;
            }
            return $func;
        });
        static::mock('\SAPNWRFC\RemoteFunction::invoke', static function (array $params, array $options) use ($flags) {
            throw new \SAPNWRFC\FunctionCallException('mock function call exception!');
        });
    }
}
