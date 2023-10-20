<?php

declare(strict_types=1);

namespace tests\phpsap\saprfc;

use phpsap\classes\Api\RemoteApi;
use phpsap\IntegrationTests\AbstractTestCase;
use phpsap\interfaces\exceptions\IConnectionFailedException;
use phpsap\interfaces\exceptions\IFunctionCallException;
use phpsap\interfaces\exceptions\IIncompleteConfigException;
use phpsap\interfaces\exceptions\IInvalidArgumentException;
use phpsap\interfaces\exceptions\IUnknownFunctionException;
use SAPNWRFC\ConnectionException;
use SAPNWRFC\FunctionCallException;
use SAPNWRFC\RemoteFunction;
use stdClass;
use tests\phpsap\saprfc\Traits\TestCaseTrait;

/**
 * Class OutputTableTest
 *
 * All strings and numbers in this example have been generated at random.org.
 *
 * @package tests\phpsap\saprfc
 */
class OutputTableTest extends AbstractTestCase
{
    use TestCaseTrait;

    public static array $apiRaw = [
        'ET_API_ANGEBOT_ADRESSE' => [
            'name' => 'ET_API_ANGEBOT_ADRESSE',
            'type' => 'RFCTYPE_TABLE',
            'direction' => 'RFC_EXPORT',
            'description' => '',
            'ucLength' => 1320,
            'nucLength' => 660,
            'decimals' => 0,
            'optional' => false,
            'default' => '',
            'typedef' => [
                'KUNNR' => [
                    'name' => 'KUNNR',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 20,
                    'ucOffset' => 0,
                    'nucLength' => 10,
                    'nucOffset' => 0,
                    'decimals' => 0,
                ],
                'LIFNR' => [
                    'name' => 'LIFNR',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 20,
                    'ucOffset' => 20,
                    'nucLength' => 10,
                    'nucOffset' => 10,
                    'decimals' => 0,
                ],
                'PARVW' => [
                    'name' => 'PARVW',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 4,
                    'ucOffset' => 40,
                    'nucLength' => 2,
                    'nucOffset' => 20,
                    'decimals' => 0,
                ],
                'ADRNR' => [
                    'name' => 'ADRNR',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 20,
                    'ucOffset' => 44,
                    'nucLength' => 10,
                    'nucOffset' => 22,
                    'decimals' => 0,
                ],
                'TITLE' => [
                    'name' => 'TITLE',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 8,
                    'ucOffset' => 64,
                    'nucLength' => 4,
                    'nucOffset' => 32,
                    'decimals' => 0,
                ],
                'NAME1' => [
                    'name' => 'NAME1',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 80,
                    'ucOffset' => 72,
                    'nucLength' => 40,
                    'nucOffset' => 36,
                    'decimals' => 0,
                ],
                'NAME2' => [
                    'name' => 'NAME2',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 80,
                    'ucOffset' => 152,
                    'nucLength' => 40,
                    'nucOffset' => 76,
                    'decimals' => 0,
                ],
                'NAME3' => [
                    'name' => 'NAME3',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 80,
                    'ucOffset' => 232,
                    'nucLength' => 40,
                    'nucOffset' => 116,
                    'decimals' => 0,
                ],
                'NAME4' => [
                    'name' => 'NAME4',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 80,
                    'ucOffset' => 312,
                    'nucLength' => 40,
                    'nucOffset' => 156,
                    'decimals' => 0,
                ],
                'STREET' => [
                    'name' => 'STREET',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 120,
                    'ucOffset' => 392,
                    'nucLength' => 60,
                    'nucOffset' => 196,
                    'decimals' => 0,
                ],
                'HOUSE_NUM1' => [
                    'name' => 'HOUSE_NUM1',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 20,
                    'ucOffset' => 512,
                    'nucLength' => 10,
                    'nucOffset' => 256,
                    'decimals' => 0,
                ],
                'CITY2' => [
                    'name' => 'CITY2',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 80,
                    'ucOffset' => 532,
                    'nucLength' => 40,
                    'nucOffset' => 266,
                    'decimals' => 0,
                ],
                'POST_CODE1' => [
                    'name' => 'POST_CODE1',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 20,
                    'ucOffset' => 612,
                    'nucLength' => 10,
                    'nucOffset' => 306,
                    'decimals' => 0,
                ],
                'CITY1' => [
                    'name' => 'CITY1',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 80,
                    'ucOffset' => 632,
                    'nucLength' => 40,
                    'nucOffset' => 316,
                    'decimals' => 0,
                ],
                'COUNTRY' => [
                    'name' => 'COUNTRY',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 6,
                    'ucOffset' => 712,
                    'nucLength' => 3,
                    'nucOffset' => 356,
                    'decimals' => 0,
                ],
                'TEL_NUMBER' => [
                    'name' => 'TEL_NUMBER',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 60,
                    'ucOffset' => 718,
                    'nucLength' => 30,
                    'nucOffset' => 359,
                    'decimals' => 0,
                ],
                'FAX_NUMBER' => [
                    'name' => 'FAX_NUMBER',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 60,
                    'ucOffset' => 778,
                    'nucLength' => 30,
                    'nucOffset' => 389,
                    'decimals' => 0,
                ],
                'SMTP_ADDR' => [
                    'name' => 'SMTP_ADDR',
                    'type' => 'RFCTYPE_CHAR',
                    'ucLength' => 482,
                    'ucOffset' => 838,
                    'nucLength' => 241,
                    'nucOffset' => 419,
                    'decimals' => 0,
                ],
            ],
        ]
    ];
    /**
     * @var string
     */
    public static $apiJson = [
        0 => [
            'type' => 'table',
            'name' => 'ET_API_ANGEBOT_ADRESSE',
            'direction' => 'output',
            'optional' => false,
            'members' => [
                0 => [
                    'type' => 'string',
                    'name' => 'KUNNR',
                ],
                1 => [
                    'type' => 'string',
                    'name' => 'LIFNR',
                ],
                2 => [
                    'type' => 'string',
                    'name' => 'PARVW',
                ],
                3 => [
                    'type' => 'string',
                    'name' => 'ADRNR',
                ],
                4 => [
                    'type' => 'string',
                    'name' => 'TITLE',
                ],
                5 => [
                    'type' => 'string',
                    'name' => 'NAME1',
                ],
                6 => [
                    'type' => 'string',
                    'name' => 'NAME2',
                ],
                7 => [
                    'type' => 'string',
                    'name' => 'NAME3',
                ],
                8 => [
                    'type' => 'string',
                    'name' => 'NAME4',
                ],
                9 => [
                    'type' => 'string',
                    'name' => 'STREET',
                ],
                10 => [
                    'type' => 'string',
                    'name' => 'HOUSE_NUM1',
                ],
                11 => [
                    'type' => 'string',
                    'name' => 'CITY2',
                ],
                12 => [
                    'type' => 'string',
                    'name' => 'POST_CODE1',
                ],
                13 => [
                    'type' => 'string',
                    'name' => 'CITY1',
                ],
                14 => [
                    'type' => 'string',
                    'name' => 'COUNTRY',
                ],
                15 => [
                    'type' => 'string',
                    'name' => 'TEL_NUMBER',
                ],
                16 => [
                    'type' => 'string',
                    'name' => 'FAX_NUMBER',
                ],
                17 => [
                    'type' => 'string',
                    'name' => 'SMTP_ADDR',
                ],
            ],
        ],
    ];

    /**
     * Mocked function call response array.
     * @var array
     */
    public static array $responseRaw = [
        'ET_API_ANGEBOT_ADRESSE' => [
            0 => [
                'KUNNR' => 'efhNQr886li8Zn5RhfMp',
                'LIFNR' => '',
                'PARVW' => 'RGSm',
                'ADRNR' => 'tgGvMqf0rs97rgN8M61a',
                'TITLE' => '0003',
                'NAME1' => 'Wmeunryaqm',
                'NAME2' => 'Xeay',
                'NAME3' => '',
                'NAME4' => '',
                'STREET' => 'Hrrvvcifuv',
                'HOUSE_NUM1' => '206',
                'CITY2' => '',
                'POST_CODE1' => '77861',
                'CITY1' => 'Bywemkvpro',
                'COUNTRY' => 'DE',
                'TEL_NUMBER' => '78086303',
                'FAX_NUMBER' => '45407202',
                'SMTP_ADDR' => '9mgus@ja9w.k',
            ],
            1 => [
                'KUNNR' => 'GP6gqa1vxYOog7JaB9Hu',
                'LIFNR' => '',
                'PARVW' => 'oiqQ',
                'ADRNR' => 'eJpxxxLfYeX9bdWnJ5dZ',
                'TITLE' => '0003',
                'NAME1' => 'Vprhwjtivt',
                'NAME2' => 'Tzoogd',
                'NAME3' => '',
                'NAME4' => '',
                'STREET' => 'Kbuhjnshdn',
                'HOUSE_NUM1' => '74',
                'CITY2' => '',
                'POST_CODE1' => '00745',
                'CITY1' => 'Atcfejtglr',
                'COUNTRY' => 'DE',
                'TEL_NUMBER' => '85407607',
                'FAX_NUMBER' => '55191501',
                'SMTP_ADDR' => 'ut@npwup9.lr',
            ],
        ]
    ];

    /**
     * Mock the RFC_OUTPUT_TABLE function.
     */
    public function mockRfcOutputTable()
    {
        //Use an object for connection flag and function name.
        $flags = new stdClass();
        $flags->conn = false;
        $flags->name = 'RFC_OUTPUT_TABLE';
        $flags->func = null;
        $flags->api = self::$apiRaw;
        $flags->response = self::$responseRaw;
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
            if ($name !== $flags->name) {
                throw new FunctionCallException('expected ' . $flags->name . ' as mock function name!');
            }
            $flags->func = $name;
        });
        static::mock('\SAPNWRFC\Connection::getFunction', static function ($name) use ($flags) {
            if ($flags->conn !== true) {
                throw new FunctionCallException('mock connection not open!');
            }
            if ($name !== $flags->name) {
                throw new FunctionCallException('expected ' . $flags->name . ' as mock function name!');
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
            if ($flags->func !== $flags->name) {
                throw new FunctionCallException('function not correctly initialized!');
            }
            return $flags->response;
        });
    }

    /**
     * Test the API description and the response from an RFC output table.
     * @throws IConnectionFailedException
     * @throws IFunctionCallException
     * @throws IIncompleteConfigException
     * @throws IUnknownFunctionException
     * @throws IInvalidArgumentException
     */
    public function testRfcOutputTable()
    {
        //Mock the behavior of the module
        $this->mockRfcOutputTable();
        //init with a bogus config
        $saprfc = static::newSapRfc('RFC_OUTPUT_TABLE')
            ->setConfiguration(static::getSampleSapConfig());
        //get the remote API
        $api = $saprfc->getApi();
        //Assert that the API meets the configured expectations.
        static::assertInstanceOf(RemoteApi::class, $api);
        static::assertJsonStringEqualsJsonString(
            json_encode(self::$apiJson),
            json_encode($api)
        );
        //remote function call
        $response = $saprfc->invoke();
        static::assertIsArray($response);
        static::assertCount(1, $response);
        static::assertArrayHasKey('ET_API_ANGEBOT_ADRESSE', $response);
        static::assertCount(2, $response['ET_API_ANGEBOT_ADRESSE']);
        static::assertArrayHasKey(0, $response['ET_API_ANGEBOT_ADRESSE']);
        static::assertArrayHasKey(1, $response['ET_API_ANGEBOT_ADRESSE']);
        static::assertArrayHasKey('KUNNR', $response['ET_API_ANGEBOT_ADRESSE'][0]);
        static::assertArrayHasKey('KUNNR', $response['ET_API_ANGEBOT_ADRESSE'][1]);
        static::assertSame('efhNQr886li8Zn5RhfMp', $response['ET_API_ANGEBOT_ADRESSE'][0]['KUNNR']);
        static::assertSame('GP6gqa1vxYOog7JaB9Hu', $response['ET_API_ANGEBOT_ADRESSE'][1]['KUNNR']);
    }
}
