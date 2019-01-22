<?php
/**
 * File tests/helper/RemoteFunctionCall.php
 *
 * Helper class extending the abstract remote function class for testing.
 *
 * @package common
 * @author  Gregor J.
 * @license MIT
 */

namespace tests\phpsap\saprfc\helper;

use phpsap\interfaces\IConfig;
use phpsap\saprfc\AbstractRemoteFunctionCall;

/**
 * Class tests\phpsap\saprfc\helper\RemoteFunctionCall
 *
 * Helper class extending the abstract remote function class for testing.
 *
 * @package tests\phpsap\saprfc\helper
 * @author  Gregor J.
 * @license MIT
 */
class RemoteFunctionCall extends AbstractRemoteFunctionCall
{
    /**
     * @var string function name
     */
    public $returnName = 'cketfemo';

    /**
     * The SAP remote function name.
     * @return string
     */
    public function getName()
    {
        return $this->returnName;
    }

    /**
     * Make protected function public for testing.
     * Create a connection instance using the given config.
     * @param \phpsap\interfaces\IConfig $config
     * @return \phpsap\interfaces\IConnection|\phpsap\saprfc\SapRfcConnection
     * @throws \phpsap\interfaces\exceptions\IIncompleteConfigException
     */
    public function createConnectionInstance(IConfig $config)
    {
        return parent::createConnectionInstance($config);
    }
}
