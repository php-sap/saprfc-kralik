<?php

declare(strict_types=1);

namespace tests\phpsap\saprfc;

use phpsap\IntegrationTests\AbstractSapRfcTestCase;
use SAPNWRFC\ConnectionException;
use SAPNWRFC\FunctionCallException;
use SAPNWRFC\RemoteFunction;
use stdClass;
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
    public static array $rfcWalkThruTestApi = [
        'TEST_OUT' => [
            'name' => 'TEST_OUT',
            'type' => 'RFCTYPE_STRUCTURE',
            'direction' => 'RFC_EXPORT',
            'description' => '',
            'ucLength' => 264,
            'nucLength' => 144,
            'decimals' => 0,
            'optional' => false,
            'default' => '',
            'typedef' => [
                'RFCFLOAT' => [
                    'name' => 'RFCFLOAT',
                    'type' => 'RFCTYPE_FLOAT',
                    'ucLength' => 8,
                    'ucOffset' => 0,
                    'nucLength' => 8,
                    'nucOffset' => 0,
                    'decimals' => 16,
                ],
                'RFCCHAR1' => [
                    'name' => 'RFCCHAR1',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 2,
                    'ucOffset' => 8,
                    'nucLength' => 1,
                    'nucOffset' => 8,
                    'decimals' => 0,
                ],
                'RFCINT2' => [
                    'name' => 'RFCINT2',
                    'type' => 'RFCTYPE_INT2',
                    'ucLength' => 2,
                    'ucOffset' => 10,
                    'nucLength' => 2,
                    'nucOffset' => 10,
                    'decimals' => 0,
                ],
                'RFCINT1' => [
                    'name' => 'RFCINT1',
                    'type' => 'RFCTYPE_INT1',
                    'ucLength' => 1,
                    'ucOffset' => 12,
                    'nucLength' => 1,
                    'nucOffset' => 12,
                    'decimals' => 0,
                ],
                'RFCCHAR4' => [
                    'name' => 'RFCCHAR4',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 8,
                    'ucOffset' => 14,
                    'nucLength' => 4,
                    'nucOffset' => 13,
                    'decimals' => 0,
                ],
                'RFCINT4' => [
                    'name' => 'RFCINT4',
                    'type' => 'RFCTYPE_INT',
                    'ucLength' => 4,
                    'ucOffset' => 24,
                    'nucLength' => 4,
                    'nucOffset' => 20,
                    'decimals' => 0,
                ],
                'RFCHEX3' => [
                    'name' => 'RFCHEX3',
                    'type' => 'RFCTYPE_BYTE',
                    'ucLength' => 3,
                    'ucOffset' => 28,
                    'nucLength' => 3,
                    'nucOffset' => 24,
                    'decimals' => 0,
                ],
                'RFCCHAR2' => [
                    'name' => 'RFCCHAR2',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 4,
                    'ucOffset' => 32,
                    'nucLength' => 2,
                    'nucOffset' => 27,
                    'decimals' => 0,
                ],
                'RFCTIME' => [
                    'name' => 'RFCTIME',
                    'type' => 'RFCTYPE_TIME',
                    'ucLength' => 12,
                    'ucOffset' => 36,
                    'nucLength' => 6,
                    'nucOffset' => 29,
                    'decimals' => 0,
                ],
                'RFCDATE' => [
                    'name' => 'RFCDATE',
                    'type' => 'RFCTYPE_DATE',
                    'ucLength' => 16,
                    'ucOffset' => 48,
                    'nucLength' => 8,
                    'nucOffset' => 35,
                    'decimals' => 0,
                ],
                'RFCDATA1' => [
                    'name' => 'RFCDATA1',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 100,
                    'ucOffset' => 64,
                    'nucLength' => 50,
                    'nucOffset' => 43,
                    'decimals' => 0,
                ],
                'RFCDATA2' => [
                    'name' => 'RFCDATA2',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 100,
                    'ucOffset' => 164,
                    'nucLength' => 50,
                    'nucOffset' => 93,
                    'decimals' => 0,
                ]
            ]
        ],
        'TEST_IN' => [
            'name' => 'TEST_IN',
            'type' => 'RFCTYPE_STRUCTURE',
            'direction' => 'RFC_IMPORT',
            'description' => '',
            'ucLength' => 264,
            'nucLength' => 144,
            'decimals' => 0,
            'optional' => false,
            'default' => '',
            'typedef' => [
                'RFCFLOAT' => [
                    'name' => 'RFCFLOAT',
                    'type' => 'RFCTYPE_FLOAT',
                    'ucLength' => 8,
                    'ucOffset' => 0,
                    'nucLength' => 8,
                    'nucOffset' => 0,
                    'decimals' => 16,
                ],
                'RFCCHAR1' => [
                    'name' => 'RFCCHAR1',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 2,
                    'ucOffset' => 8,
                    'nucLength' => 1,
                    'nucOffset' => 8,
                    'decimals' => 0,
                ],
                'RFCINT2' => [
                    'name' => 'RFCINT2',
                    'type' => 'RFCTYPE_INT2',
                    'ucLength' => 2,
                    'ucOffset' => 10,
                    'nucLength' => 2,
                    'nucOffset' => 10,
                    'decimals' => 0,
                ],
                'RFCINT1' => [
                    'name' => 'RFCINT1',
                    'type' => 'RFCTYPE_INT1',
                    'ucLength' => 1,
                    'ucOffset' => 12,
                    'nucLength' => 1,
                    'nucOffset' => 12,
                    'decimals' => 0,
                ],
                'RFCCHAR4' => [
                    'name' => 'RFCCHAR4',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 8,
                    'ucOffset' => 14,
                    'nucLength' => 4,
                    'nucOffset' => 13,
                    'decimals' => 0,
                ],
                'RFCINT4' => [
                    'name' => 'RFCINT4',
                    'type' => 'RFCTYPE_INT',
                    'ucLength' => 4,
                    'ucOffset' => 24,
                    'nucLength' => 4,
                    'nucOffset' => 20,
                    'decimals' => 0,
                ],
                'RFCHEX3' => [
                    'name' => 'RFCHEX3',
                    'type' => 'RFCTYPE_BYTE',
                    'ucLength' => 3,
                    'ucOffset' => 28,
                    'nucLength' => 3,
                    'nucOffset' => 24,
                    'decimals' => 0,
                ],
                'RFCCHAR2' => [
                    'name' => 'RFCCHAR2',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 4,
                    'ucOffset' => 32,
                    'nucLength' => 2,
                    'nucOffset' => 27,
                    'decimals' => 0,
                ],
                'RFCTIME' => [
                    'name' => 'RFCTIME',
                    'type' => 'RFCTYPE_TIME',
                    'ucLength' => 12,
                    'ucOffset' => 36,
                    'nucLength' => 6,
                    'nucOffset' => 29,
                    'decimals' => 0,
                ],
                'RFCDATE' => [
                    'name' => 'RFCDATE',
                    'type' => 'RFCTYPE_DATE',
                    'ucLength' => 16,
                    'ucOffset' => 48,
                    'nucLength' => 8,
                    'nucOffset' => 35,
                    'decimals' => 0,
                ],
                'RFCDATA1' => [
                    'name' => 'RFCDATA1',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 100,
                    'ucOffset' => 64,
                    'nucLength' => 50,
                    'nucOffset' => 43,
                    'decimals' => 0,
                ],
                'RFCDATA2' => [
                    'name' => 'RFCDATA2',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 100,
                    'ucOffset' => 164,
                    'nucLength' => 50,
                    'nucOffset' => 93,
                    'decimals' => 0,
                ]
            ]
        ],
        'DESTINATIONS' => [
            'name' => 'DESTINATIONS',
            'type' => 'RFCTYPE_TABLE',
            'direction' => 'RFC_TABLES',
            'description' => '',
            'ucLength' => 64,
            'nucLength' => 32,
            'decimals' => 0,
            'optional' => false,
            'default' => '',
            'typedef' => [
                'RFCDEST' => [
                    'name' => 'RFCDEST',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 64,
                    'ucOffset' => 0,
                    'nucLength' => 32,
                    'nucOffset' => 0,
                    'decimals' => 0,
                ]
            ]
        ],
        'LOG' => [
            'name' => 'LOG',
            'type' => 'RFCTYPE_TABLE',
            'direction' => 'RFC_TABLES',
            'description' => '',
            'ucLength' => 268,
            'nucLength' => 134,
            'decimals' => 0,
            'optional' => false,
            'default' => '',
            'typedef' => [
                'RFCDEST' => [
                    'name' => 'RFCDEST',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 64,
                    'ucOffset' => 0,
                    'nucLength' => 32,
                    'nucOffset' => 0,
                    'decimals' => 0,
                ],
                'RFCWHOAMI' => [
                    'name' => 'RFCWHOAMI',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 64,
                    'ucOffset' => 64,
                    'nucLength' => 32,
                    'nucOffset' => 32,
                    'decimals' => 0,
                ],
                'RFCLOG' => [
                    'name' => 'RFCLOG',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 140,
                    'ucOffset' => 128,
                    'nucLength' => 70,
                    'nucOffset' => 64,
                    'decimals' => 0,
                ]
            ]
        ]
    ];

    /**
     * @var array raw API of RFC read table
     */
    public static array $rfcReadTableApi = [
        'DELIMITER' => [
            'name' => 'DELIMITER',
            'type' => 'RFCTYPE_CHAR',
            'direction' => 'RFC_IMPORT',
            'description' => '',
            'ucLength' => 2,
            'nucLength' => 1,
            'decimals' => 0,
            'optional' => true,
            'default' => 'SPACE',
        ],
        'NO_DATA' => [
            'name' => 'NO_DATA',
            'type' => 'RFCTYPE_CHAR',
            'direction' => 'RFC_IMPORT',
            'description' => '',
            'ucLength' => 2,
            'nucLength' => 1,
            'decimals' => 0,
            'optional' => true,
            'default' => 'SPACE',
        ],
        'QUERY_TABLE' => [
            'name' => 'QUERY_TABLE',
            'type' => 'RFCTYPE_CHAR',
            'direction' => 'RFC_IMPORT',
            'description' => '',
            'ucLength' => 60,
            'nucLength' => 30,
            'decimals' => 0,
            'optional' => false,
            'default' => '',
        ],
        'ROWCOUNT' => [
            'name' => 'ROWCOUNT',
            'type' => 'RFCTYPE_INT',
            'direction' => 'RFC_IMPORT',
            'description' => '',
            'ucLength' => 4,
            'nucLength' => 4,
            'decimals' => 0,
            'optional' => true,
            'default' => '0',
        ],
        'ROWSKIPS' => [
            'name' => 'ROWSKIPS',
            'type' => 'RFCTYPE_INT',
            'direction' => 'RFC_IMPORT',
            'description' => '',
            'ucLength' => 4,
            'nucLength' => 4,
            'decimals' => 0,
            'optional' => true,
            'default' => '0',
        ],
        'DATA' => [
            'name' => 'DATA',
            'type' => 'RFCTYPE_TABLE',
            'direction' => 'RFC_TABLES',
            'description' => '',
            'ucLength' => 1024,
            'nucLength' => 512,
            'decimals' => 0,
            'optional' => false,
            'default' => '',
            'typedef' => [
                'WA' => [
                    'name' => 'WA',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 1024,
                    'ucOffset' => 0,
                    'nucLength' => 512,
                    'nucOffset' => 0,
                    'decimals' => 0,
                ]
            ]
        ],
        'FIELDS' => [
            'name' => 'FIELDS',
            'type' => 'RFCTYPE_TABLE',
            'direction' => 'RFC_TABLES',
            'description' => '',
            'ucLength' => 206,
            'nucLength' => 103,
            'decimals' => 0,
            'optional' => false,
            'default' => '',
            'typedef' => [
                'FIELDNAME' => [
                    'name' => 'FIELDNAME',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 60,
                    'ucOffset' => 0,
                    'nucLength' => 30,
                    'nucOffset' => 0,
                    'decimals' => 0,
                ],
                'OFFSET' => [
                    'name' => 'OFFSET',
                    'type' => 'RFCTYPE_NUM',
                    'ucLength' => 12,
                    'ucOffset' => 60,
                    'nucLength' => 6,
                    'nucOffset' => 30,
                    'decimals' => 0,
                ],
                'LENGTH' => [
                    'name' => 'LENGTH',
                    'type' => 'RFCTYPE_NUM',
                    'ucLength' => 12,
                    'ucOffset' => 72,
                    'nucLength' => 6,
                    'nucOffset' => 36,
                    'decimals' => 0,
                ],
                'TYPE' => [
                    'name' => 'TYPE',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 2,
                    'ucOffset' => 84,
                    'nucLength' => 1,
                    'nucOffset' => 42,
                    'decimals' => 0,
                ],
                'FIELDTEXT' => [
                    'name' => 'FIELDTEXT',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 120,
                    'ucOffset' => 86,
                    'nucLength' => 60,
                    'nucOffset' => 43,
                    'decimals' => 0,
                ]
            ]
        ],
        'OPTIONS' => [
            'name' => 'OPTIONS',
            'type' => 'RFCTYPE_TABLE',
            'direction' => 'RFC_TABLES',
            'description' => '',
            'ucLength' => 144,
            'nucLength' => 72,
            'decimals' => 0,
            'optional' => false,
            'default' => '',
            'typedef' => [
                'TEXT' => [
                    'name' => 'TEXT',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 144,
                    'ucOffset' => 0,
                    'nucLength' => 72,
                    'nucOffset' => 0,
                    'decimals' => 0,
                ]
            ]
        ]
    ];

    /**
     * Clean up after tests.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $devRfcTrc = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'dev_rfc.trc';
        if (file_exists($devRfcTrc)) {
            unlink($devRfcTrc);
        }
    }

    /**
     * @inheritDoc
     */
    protected function mockConnectionFailed()
    {
        static::mock('\SAPNWRFC\Connection::__construct', static function (array $config, array $options) {
            throw new ConnectionException('mock failed connection');
        });
    }

    /**
     * @inheritDoc
     */
    protected function mockSuccessfulRfcPing()
    {
        $flags = new stdClass();
        $flags->conn = false;
        $flags->func = null;
        $expectedConfig = static::getSampleSapConfig();
        static::mock('\SAPNWRFC\Connection::__construct', static function (array $config, array $options) use ($flags, $expectedConfig) {
            if (!is_array($config)
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
                throw new ConnectionException('mock received invalid config array!');
            }
            //set flag that a connection has been established
            $flags->conn = true;
        });
        static::mock('\SAPNWRFC\Connection::close', static function () use ($flags) {
            //calling \SAPNWRFC\Connection::close twice has to fail
            if ($flags->conn !== true) {
                throw new ConnectionException('mock connection already closed!');
            }
            $flags->conn = false;
            return true;
        });
        static::mock('\SAPNWRFC\Connection::getFunction', static function ($name) {
            return new RemoteFunction($name);
        });
        static::mock('\SAPNWRFC\RemoteFunction::__construct', static function ($name) use ($flags) {
            if ($flags->conn !== true) {
                throw new FunctionCallException('mock connection not open!');
            }
            if ($name !== 'RFC_PING') {
                throw new FunctionCallException('expected RFC_PING as mock function name!');
            }
            $flags->func = $name;
        });
        static::mock('\SAPNWRFC\RemoteFunction::getFunctionDescription', static function () use ($flags) {
            if ($flags->conn !== true) {
                throw new FunctionCallException('mock connection not open!');
            }
            return [];
        });
        static::mock('\SAPNWRFC\RemoteFunction::invoke', static function (array $params, array $options) use ($flags) {
            if ($flags->conn !== true) {
                throw new FunctionCallException('mock connection not open!');
            }
            if ($flags->func !== 'RFC_PING') {
                throw new FunctionCallException('mock function not correctly constructed!');
            }
            if (!empty($params)) {
                throw new FunctionCallException('mock RFC_PING received parameters! ' . json_encode($params));
            }
            return [];
        });
    }

    /**
     * @inheritDoc
     */
    protected function mockUnknownFunctionException()
    {
        $flags = new stdClass();
        $flags->conn = false;
        $expectedConfig = static::getSampleSapConfig();
        static::mock('\SAPNWRFC\Connection::__construct', static function (array $config, array $options) use ($flags, $expectedConfig) {
            if (!is_array($config)
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
                throw new ConnectionException('mock received invalid config array!');
            }
            //set flag that a connection has been established
            $flags->conn = true;
        });
        static::mock('\SAPNWRFC\Connection::close', static function () use ($flags) {
            //calling \SAPNWRFC\Connection::close twice has to fail
            if ($flags->conn !== true) {
                throw new ConnectionException('mock connection already closed!');
            }
            $flags->conn = false;
            return true;
        });
        static::mock('\SAPNWRFC\Connection::getFunction', static function ($name) {
            throw new FunctionCallException(sprintf('function %s not found', $name));
        });
    }

    /**
     * @inheritDoc
     */
    protected function mockRemoteFunctionCallWithParametersAndResults()
    {
        //Use an object for connection flag and function name.
        $flags = new stdClass();
        $flags->conn = false;
        $flags->func = null;
        $flags->api = static::$rfcWalkThruTestApi;
        $expectedConfig = static::getSampleSapConfig();
        static::mock('\SAPNWRFC\Connection::__construct', static function (array $config, array $options) use ($flags, $expectedConfig) {
            if (!is_array($config)
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
                throw new ConnectionException('mock received invalid config array!');
            }
            //set flag that a connection has been established
            $flags->conn = true;
        });
        static::mock('\SAPNWRFC\Connection::close', static function () use ($flags) {
            //calling \SAPNWRFC\Connection::close twice has to fail
            if ($flags->conn !== true) {
                throw new ConnectionException('mock connection already closed!');
            }
            $flags->conn = false;
            return true;
        });
        static::mock('\SAPNWRFC\RemoteFunction::__construct', static function ($name) use ($flags) {
            if ($flags->conn !== true) {
                throw new FunctionCallException('mock connection not open!');
            }
            if ($name !== 'RFC_WALK_THRU_TEST') {
                throw new FunctionCallException('expected RFC_WALK_THRU_TEST as mock function name!');
            }
            $flags->func = $name;
        });
        static::mock('\SAPNWRFC\Connection::getFunction', static function ($name) use ($flags) {
            if ($flags->conn !== true) {
                throw new FunctionCallException('mock connection not open!');
            }
            if ($name !== 'RFC_WALK_THRU_TEST') {
                throw new FunctionCallException('expected RFC_WALK_THRU_TEST as mock function name!');
            }
            return new RemoteFunction($name);
        });
        static::mock('\SAPNWRFC\RemoteFunction::getFunctionDescription', static function () use ($flags) {
            if ($flags->conn !== true) {
                throw new FunctionCallException('mock connection not open!');
            }
            return $flags->api;
        });
        static::mock('\SAPNWRFC\RemoteFunction::invoke', static function (array $params, array $options) use ($flags) {
            if ($flags->conn !== true) {
                throw new FunctionCallException('mock connection not open!');
            }
            if ($flags->func !== 'RFC_WALK_THRU_TEST') {
                throw new FunctionCallException('function not correctly initialized!');
            }
            return [
                'TEST_OUT' => [
                    'RFCFLOAT' => 70.11,
                    'RFCCHAR1' => 'A',
                    'RFCINT2' => 4095,
                    'RFCINT1' => 163,
                    'RFCCHAR4' => 'QqMh',
                    'RFCINT4' => 416639,
                    'RFCHEX3' => '53' . "\0" . '',
                    'RFCCHAR2' => 'XC',
                    'RFCTIME' => '102030',
                    'RFCDATE' => '20191030',
                    'RFCDATA1' => 'qKWjmNfad32rfS9Z',
                    'RFCDATA2' => 'xi82ph2zJ8BCVtlR'
                ],
                'DESTINATIONS' => [],
                'LOG' => [
                    [
                        'RFCDEST' => 'AOP3',
                        'RFCWHOAMI' => 'pzjti000',
                        'RFCLOG' => 'FAP-RytEHBsRYKX AOP3 eumqvMJD ZLqovj.'
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
        $flags = new stdClass();
        $flags->conn = false;
        $flags->func = null;
        $flags->api = static::$rfcReadTableApi;
        $expectedConfig = static::getSampleSapConfig();
        static::mock('\SAPNWRFC\Connection::__construct', static function (array $config, array $options) use ($flags, $expectedConfig) {
            if (!is_array($config)
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
                throw new ConnectionException('mock received invalid config array!');
            }
            //set flag that a connection has been established
            $flags->conn = true;
        });
        static::mock('\SAPNWRFC\Connection::close', static function () use ($flags) {
            //calling \SAPNWRFC\Connection::close twice has to fail
            if ($flags->conn !== true) {
                throw new ConnectionException('mock connection already closed!');
            }
            $flags->conn = false;
            return true;
        });
        static::mock('\SAPNWRFC\RemoteFunction::__construct', static function ($name) use ($flags) {
            if ($flags->conn !== true) {
                throw new FunctionCallException('mock connection not open!');
            }
            if ($name !== 'RFC_READ_TABLE') {
                throw new FunctionCallException('expected RFC_READ_TABLE as mock function name!');
            }
            $flags->func = $name;
        });
        static::mock('\SAPNWRFC\RemoteFunction::getFunctionDescription', static function () use ($flags) {
            if ($flags->conn !== true) {
                throw new FunctionCallException('mock connection not open!');
            }
            return $flags->api;
        });
        static::mock('\SAPNWRFC\Connection::getFunction', static function ($name) use ($flags) {
            if ($flags->conn !== true) {
                throw new FunctionCallException('mock connection not open!');
            }
            if ($name !== 'RFC_READ_TABLE') {
                throw new FunctionCallException('expected RFC_READ_TABLE as mock function name!');
            }
            return new RemoteFunction($name);
        });
        static::mock('\SAPNWRFC\RemoteFunction::invoke', static function (array $params, array $options) use ($flags) {
            throw new FunctionCallException('mock function call exception!');
        });
    }
}
