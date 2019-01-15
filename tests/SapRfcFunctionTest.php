<?php
/**
 * File src/SapRfcFunctionTest.php
 *
 * Test function class.
 *
 * @package saprfc-kralik
 * @author  Gregor J.
 * @license MIT
 */

namespace tests\phpsap\saprfc;

use phpsap\IntegrationTests\AbstractFunctionTestCase;

/**
 * Class tests\phpsap\saprfc\SapRfcFunctionTest
 *
 * Test function class.
 *
 * @package tests\phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
class SapRfcFunctionTest extends AbstractFunctionTestCase
{
    /**
     * @var bool
     */
    protected $connection = false;

    /**
     * @var string function name
     */
    private $function;

    /**
     * Implement methods of phpsap\IntegrationTests\AbstractTestCase
     */
    use SapRfcTestCaseTrait;

    /**
     * Mock the SAP RFC module for a successful SAP remote function call.
     */
    protected function mockSuccessfulFunctionCall()
    {
        $this->connection = false;
        $this->function = null;
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
        static::mock('\SAPNWRFC\RemoteFunction::__construct', function ($name) use ($self) {
            if ($name !== 'RFC_PING') {
                throw new \SAPNWRFC\FunctionCallException('expected RFC_PING as mock function name!');
            }
            $self->function = $name;
        });
        static::mock('\SAPNWRFC\Connection::getFunction', function ($name) use ($self) {
            if ($self->connection !== true) {
                throw new \SAPNWRFC\FunctionCallException('mock connection not open!');
            }
            return new \SAPNWRFC\RemoteFunction($name);
        });
        static::mock('\SAPNWRFC\RemoteFunction::invoke', function ($params, $options) use ($self) {
            if ($self->function !== 'RFC_PING') {
                throw new \SAPNWRFC\FunctionCallException('function not correctly initialized');
            }
            return [];
        });
    }

    /**
     * Mock the SAP RFC module for an unknown function call exception.
     */
    protected function mockUnknownFunctionException()
    {
        $this->connection = false;
        $this->function = null;
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
        static::mock('\SAPNWRFC\Connection::getFunction', function ($name) {
            throw new \SAPNWRFC\FunctionCallException(sprintf('function %s not found', $name));
        });
    }

    /**
     * Mock the SAP RFC module for a successful SAP remote function call with
     * parameters and results.
     */
    protected function mockRemoteFunctionCallWithParametersAndResults()
    {
        $this->connection = false;
        $this->function = null;
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
        static::mock('\SAPNWRFC\RemoteFunction::__construct', function ($name) use ($self) {
            if ($name !== 'Z_MC_GET_DATE_TIME') {
                throw new \SAPNWRFC\FunctionCallException('expected Z_MC_GET_DATE_TIME as mock function name!');
            }
            $self->function = $name;
        });
        static::mock('\SAPNWRFC\Connection::getFunction', function ($name) use ($self) {
            if ($self->connection !== true) {
                throw new \SAPNWRFC\FunctionCallException('mock connection not open!');
            }
            return new \SAPNWRFC\RemoteFunction($name);
        });
        static::mock('\SAPNWRFC\RemoteFunction::invoke', function ($params, $options) use ($self) {
            if ($self->function !== 'Z_MC_GET_DATE_TIME') {
                throw new \SAPNWRFC\FunctionCallException('function not correctly initialized!');
            }
            if ($params !== ['IV_DATE' => '20181119'] || $options !== ['rtrim' => true]) {
                throw new \SAPNWRFC\FunctionCallException('unexpected parameters array!');
            }
            return [
                'EV_FRIDAY'         => '20181123',
                'EV_FRIDAY_LAST'    => '20181116',
                'EV_FRIDAY_NEXT'    => '20181130',
                'EV_FRITXT'         => 'Freitag',
                'EV_MONDAY'         => '20181119',
                'EV_MONDAY_LAST'    => '20181112',
                'EV_MONDAY_NEXT'    => '20181126',
                'EV_MONTH'          => '11',
                'EV_MONTH_LAST_DAY' => '20181130',
                'EV_MONTXT'         => 'Montag',
                'EV_TIMESTAMP'      => 'NOVALUE',
                'EV_WEEK'           => '201847',
                'EV_WEEK_LAST'      => '201846',
                'EV_WEEK_NEXT'      => '201848',
                'EV_YEAR'           => '2018'
            ];
        });
    }

    /**
     * Mock the SAP RFC module for a failed SAP remote function call with parameters.
     */
    protected function mockFailedRemoteFunctionCallWithParameters()
    {
        $this->connection = false;
        $this->function = null;
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
        static::mock('\SAPNWRFC\RemoteFunction::__construct', function ($name) use ($self) {
            if ($name !== 'Z_MC_GET_DATE_TIME') {
                throw new \SAPNWRFC\FunctionCallException('expected Z_MC_GET_DATE_TIME as mock function name!');
            }
            $self->function = $name;
        });
        static::mock('\SAPNWRFC\Connection::getFunction', function ($name) use ($self) {
            if ($self->connection !== true) {
                throw new \SAPNWRFC\FunctionCallException('mock connection not open!');
            }
            return new \SAPNWRFC\RemoteFunction($name);
        });
        static::mock('\SAPNWRFC\RemoteFunction::invoke', function ($params, $options) use ($self) {
            throw new \SAPNWRFC\FunctionCallException('mock function call exception!');
        });
    }
}
