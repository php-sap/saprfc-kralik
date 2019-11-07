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
            if ($name !== 'RFC_WALK_THRU_TEST') {
                throw new \SAPNWRFC\FunctionCallException('expected RFC_WALK_THRU_TEST as mock function name!');
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
            if ($self->function !== 'RFC_WALK_THRU_TEST') {
                throw new \SAPNWRFC\FunctionCallException('function not correctly initialized!');
            }
            return [
                'TEST_OUT' => [
                    'RFCFLOAT' => 70.109999999999999,
                    'RFCCHAR1' => 'A',
                    'RFCINT2' => 5920,
                    'RFCINT1' => 163,
                    'RFCCHAR4' => 'QqMh',
                    'RFCINT4' => 416639,
                    'RFCHEX3' => '53' . "\0",
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
                        'RFCLOG' => 'FAP-RytEHBsRYKX AOP3 eumqvMJD ZLqovj.' //just some random characters around AOP3
                    ]
                ]
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
            if ($name !== 'RFC_READ_TABLE') {
                throw new \SAPNWRFC\FunctionCallException('expected RFC_READ_TABLE as mock function name!');
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
