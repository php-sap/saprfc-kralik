<?php
/**
 * File src/AbstractRemoteFunctionCall.php
 *
 * PHP/SAP proxy class for SAP remote function calls.
 *
 * @package saprfc-harding
 * @author  Gregor J.
 * @license MIT
 */

namespace phpsap\saprfc;

use phpsap\interfaces\IConfig;

/**
 * Class phpsap\saprfc\AbstractRemoteFunctionCall
 *
 * Abstract class handling a PHP/SAP connection and remote function.
 *
 * @package phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
abstract class AbstractRemoteFunctionCall extends \phpsap\classes\AbstractRemoteFunctionCall
{
    /**
     * Create a connection instance using the given config.
     * @param \phpsap\interfaces\IConfig $config
     * @return \phpsap\interfaces\IConnection|\phpsap\saprfc\SapRfcConnection
     * @throws \phpsap\interfaces\exceptions\IIncompleteConfigException
     */
    protected function createConnectionInstance(IConfig $config)
    {
        return new SapRfcConnection($config);
    }
}
