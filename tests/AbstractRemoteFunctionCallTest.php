<?php
/**
 * File src/AbstractRemoteFunctionCallTest.php
 *
 * Test the abstract remote function call.
 *
 * @package saprfc-harding
 * @author  Gregor J.
 * @license MIT
 */

namespace tests\phpsap\saprfc;

use phpsap\saprfc\SapRfcConfigA;
use phpsap\saprfc\SapRfcConnection;
use PHPUnit\Framework\TestCase;
use tests\phpsap\saprfc\helper\RemoteFunctionCall;

/**
 * Class tests\phpsap\saprfc\AbstractRemoteFunctionCallTest
 *
 * Test the abstract remote function call.
 *
 * @package tests\phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
class AbstractRemoteFunctionCallTest extends TestCase
{
    /**
     * Test test creating a package specific connection instance.
     */
    public function testCreateConnectionInstance()
    {
        $config = new SapRfcConfigA([
            'ashost' => 'sap.example.com',
            'sysnr' => '001',
            'client' => '002',
            'user' => 'username',
            'passwd' => 'password'
        ]);
        $rfc = new RemoteFunctionCall($config);
        $connection = $rfc->createConnectionInstance($config);
        static::assertInstanceOf(SapRfcConnection::class, $connection);
    }

    /**
     * Test getting an empty return typecast.
     */
    public function testGetReturnTypecast()
    {
        $config = new SapRfcConfigA([
            'ashost' => 'sap.example.com',
            'sysnr' => '001',
            'client' => '002',
            'user' => 'username',
            'passwd' => 'password'
        ]);
        $rfc = new RemoteFunctionCall($config);
        static::assertNull($rfc->getReturnTypecast());
    }
}
