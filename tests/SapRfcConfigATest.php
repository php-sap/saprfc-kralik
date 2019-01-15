<?php
/**
 * File tests/SapRfcConfigATest.php
 *
 * Test config type A.
 *
 * @package saprfc-kralik
 * @author  Gregor J.
 * @license MIT
 */

namespace tests\phpsap\saprfc;

use phpsap\IntegrationTests\AbstractConfigATestCase;
use phpsap\saprfc\SapRfcConfigA;

/**
 * Class tests\phpsap\saprfc\SapRfcConfigATest
 *
 * Test config type A.
 *
 * @package tests\phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
class SapRfcConfigATest extends AbstractConfigATestCase
{
    /**
     * Return a new instance of a PHP/SAP config type A.
     * @param array|string|null $config PHP/SAP config JSON/array. Default: null
     * @return \phpsap\saprfc\SapRfcConfigA
     */
    public function newConfigA($config = null)
    {
        return new SapRfcConfigA($config);
    }

    /**
     * Assert the actual module configuration variable.
     * @param array $configSaprfc
     * @param string $ashost
     * @param string $sysnr
     * @param string $client
     * @param string $user
     * @param string $passwd
     * @param string $gwhost
     * @param string $gwserv
     * @param string $lang
     * @param string $trace
     */
    public function assertValidModuleConfig(
        $configSaprfc,
        $ashost,
        $sysnr,
        $client,
        $user,
        $passwd,
        $gwhost,
        $gwserv,
        $lang,
        $trace
    ) {
        static::assertInternalType('array', $configSaprfc);
        static::assertArrayHasKey('ashost', $configSaprfc);
        static::assertSame($ashost, $configSaprfc['ashost']);
        static::assertArrayHasKey('sysnr', $configSaprfc);
        static::assertSame($sysnr, $configSaprfc['sysnr']);
        static::assertArrayHasKey('client', $configSaprfc);
        static::assertSame($client, $configSaprfc['client']);
        static::assertArrayHasKey('user', $configSaprfc);
        static::assertSame($user, $configSaprfc['user']);
        static::assertArrayHasKey('passwd', $configSaprfc);
        static::assertSame($passwd, $configSaprfc['passwd']);
        static::assertArrayHasKey('gwhost', $configSaprfc);
        static::assertSame($gwhost, $configSaprfc['gwhost']);
        static::assertArrayHasKey('gwserv', $configSaprfc);
        static::assertSame($gwserv, $configSaprfc['gwserv']);
        static::assertArrayHasKey('lang', $configSaprfc);
        static::assertSame($lang, $configSaprfc['lang']);
        static::assertArrayHasKey('trace', $configSaprfc);
        static::assertSame($trace, $configSaprfc['trace']);
    }
}
