<?php
/**
 * File tests/SapRfcConnectionTest.php
 *
 * Test connection class.
 *
 * @package saprfc-kralik
 * @author  Gregor J.
 * @license MIT
 */

namespace tests\phpsap\saprfc;

use phpsap\IntegrationTests\AbstractConnectionTestCase;

/**
 * Class tests\phpsap\saprfc\SapRfcConnectionTest
 *
 * Test connection class.
 *
 * @package tests\phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
class SapRfcConnectionTest extends AbstractConnectionTestCase
{
    /**
     * @var bool
     */
    protected $connection = false;

    /**
     * Implement methods of phpsap\IntegrationTests\AbstractTestCase
     */
    use SapRfcTestCaseTrait;

    /**
     * Mock the SAP RFC module for a successful connection attempt.
     */
    protected function mockSuccessfulConnect()
    {
        $this->connection = false;
        $self = $this;
        static::mock('\SAPNWRFC\Connection::__construct', function (
            array $parameters,
            array $options = []
        ) use ($self) {
            if ($parameters !== $self->getSampleSapConfig() || $options !== []) {
                throw new \SAPNWRFC\ConnectionException('mock received invalid config array!');
            }
            //set flag that a connection has been established
            $self->connection = true;
        });
        static::mock('\SAPNWRFC\Connection::close', function () use ($self) {
            //calling \SAPNWRFC\Connection::close twice has to fail
            if ($self->connection !== true) {
                throw new \SAPNWRFC\ConnectionException('mock connection already closed!');
            }
            $self->connection = false;
            return true;
        });
    }

    /**
     * Mock the SAP RFC module for a failed connection attempt.
     */
    protected function mockFailedConnect()
    {
        $this->connection = false;
        static::mock('\SAPNWRFC\Connection::__construct', function (
            array $parameters,
            array $options = []
        ) {
            throw new \SAPNWRFC\ConnectionException('mock failed connection');
        });
    }

    /**
     * Mock the SAP RFC module for a successful attempt to ping a connection.
     */
    protected function mockSuccessfulPing()
    {
        $this->connection = false;
        $self = $this;
        static::mock('\SAPNWRFC\Connection::__construct', function (
            array $parameters,
            array $options = []
        ) use ($self) {
            if ($parameters !== $self->getSampleSapConfig() || $options !== []) {
                throw new \SAPNWRFC\ConnectionException('mock received invalid config array!');
            }
            //set flag that a connection has been established
            $self->connection = true;
        });
        static::mock('\SAPNWRFC\Connection::close', function () use ($self) {
            //calling \SAPNWRFC\Connection::close twice has to fail
            if ($self->connection !== true) {
                throw new \SAPNWRFC\ConnectionException('mock connection already closed!');
            }
            $self->connection = false;
            return true;
        });
        static::mock('\SAPNWRFC\Connection::ping', function () use ($self) {
            //return true only in case a connection has been established.
            return $self->connection;
        });
    }

    /**
     * Mock the SAP RFC module for a failed attempt to ping a connection.
     */
    protected function mockFailedPing()
    {
        $this->connection = false;
        $self = $this;
        static::mock('\SAPNWRFC\Connection::__construct', function (
            array $parameters,
            array $options = []
        ) use ($self) {
            if ($parameters !== $self->getSampleSapConfig() || $options !== []) {
                throw new \SAPNWRFC\ConnectionException('mock received invalid config array!');
            }
            //set flag that a connection has been established
            $self->connection = true;
        });
        static::mock('\SAPNWRFC\Connection::close', function () use ($self) {
            //calling sapnwrfc::close twice has to fail
            if ($self->connection !== true) {
                throw new \SAPNWRFC\ConnectionException('mock connection already closed!');
            }
            $self->connection = false;
            return true;
        });
        static::mock('\SAPNWRFC\Connection::ping', function () {
            return false;
        });
    }
}
