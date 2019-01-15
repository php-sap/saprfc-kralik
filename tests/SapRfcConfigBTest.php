<?php
/**
 * File tests/SapRfcConfigBTest.php
 *
 * Test config type B.
 *
 * @package saprfc-kralik
 * @author  Gregor J.
 * @license MIT
 */

namespace tests\phpsap\saprfc;

use phpsap\IntegrationTests\AbstractConfigBTestCase;
use phpsap\saprfc\SapRfcConfigB;

/**
 * Class tests\phpsap\saprfc\SapRfcConfigBTest
 *
 * Test config type B.
 *
 * @package tests\phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
class SapRfcConfigBTest extends AbstractConfigBTestCase
{
    /**
     * Return a new instance of a PHP/SAP config type B.
     * @param array|string|null $config PHP/SAP config JSON/array. Default: null
     * @return \phpsap\saprfc\SapRfcConfigB
     */
    public function newConfigB($config = null)
    {
        return new SapRfcConfigB($config);
    }

    /**
     * Assert the actual module configuration variable.
     * @param mixed $configSaprfc
     * @param string $client
     * @param string $user
     * @param string $passwd
     * @param string $mshost
     * @param string $r3name
     * @param string $group
     * @param string $lang
     * @param int $trace
     */
    public function assertValidModuleConfig(
        $configSaprfc,
        $client,
        $user,
        $passwd,
        $mshost,
        $r3name,
        $group,
        $lang,
        $trace
    ) {
        static::assertInternalType('array', $configSaprfc);
        static::assertArrayHasKey('client', $configSaprfc);
        static::assertSame($client, $configSaprfc['client']);
        static::assertArrayHasKey('user', $configSaprfc);
        static::assertSame($user, $configSaprfc['user']);
        static::assertArrayHasKey('passwd', $configSaprfc);
        static::assertSame($passwd, $configSaprfc['passwd']);
        static::assertArrayHasKey('mshost', $configSaprfc);
        static::assertSame($mshost, $configSaprfc['mshost']);
        static::assertArrayHasKey('r3name', $configSaprfc);
        static::assertSame($r3name, $configSaprfc['r3name']);
        static::assertArrayHasKey('group', $configSaprfc);
        static::assertSame($group, $configSaprfc['group']);
        static::assertArrayHasKey('lang', $configSaprfc);
        static::assertSame($lang, $configSaprfc['lang']);
        static::assertArrayHasKey('trace', $configSaprfc);
        static::assertSame($trace, $configSaprfc['trace']);
    }
}
