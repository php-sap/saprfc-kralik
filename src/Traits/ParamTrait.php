<?php

declare(strict_types=1);

namespace phpsap\saprfc\Traits;

use phpsap\exceptions\FunctionCallException;
use phpsap\interfaces\Api\IElement;
use phpsap\interfaces\Api\IStruct;
use phpsap\interfaces\Api\ITable;
use phpsap\interfaces\Api\IValue;
use phpsap\interfaces\exceptions\IInvalidArgumentException;

use function array_key_exists;
use function count;
use function is_array;
use function is_string;
use function sprintf;

/**
 * Trait ParamTrait
 * @package phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
trait ParamTrait
{
    /**
     * Generate a function call parameter array from a list of known input values
     * and the previously set parameters.
     * @param IValue[] $inputs API input values.
     * @param array                           $params Parameters
     * @return array
     * @throws FunctionCallException
     */
    private function getInputParams(array $inputs, array $params): array
    {
        $result = [];
        foreach ($inputs as $input) {
            $key = $input->getName();
            if (array_key_exists($key, $params)) {
                $result[$key] = $params[$key];
            } elseif (!$input->isOptional()) {
                throw new FunctionCallException(sprintf(
                    'Missing parameter \'%s\' for function call \'%s\'!',
                    $key,
                    $this->getName()
                ));
            }
        }
        return $result;
    }

    /**
     * Generate a function call parameter array from a list of known tables and the
     * previously set parameters.
     * @param ITable[] $tables
     * @param array                           $params
     * @return array
     */
    private function getTableParams(array $tables, array $params): array
    {
        $result = [];
        foreach ($tables as $table) {
            $key = $table->getName();
            if (
                array_key_exists($key, $params)
                && is_array($params[$key])
                && count($params[$key]) > 0
            ) {
                $result[$key] = $params[$key];
            }
        }
        return $result;
    }

    /**
     * @param IValue[] $outputs
     * @param array                           $result
     * @return array
     * @throws IInvalidArgumentException
     */
    private function castOutputValues(array $outputs, array $result): array
    {
        $return = [];
        foreach ($outputs as $output) {
            $key = $output->getName();
            if (array_key_exists($key, $result)) {
                $return[$key] = $output->cast($result[$key]);
            } elseif (!$output->isOptional()) {
                throw new FunctionCallException(sprintf(
                    'Missing result value \'%s\' for function call \'%s\'!',
                    $key,
                    $this->getName()
                ));
            }
        }
        return $return;
    }
}
