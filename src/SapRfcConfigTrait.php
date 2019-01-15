<?php
/**
 * File src/SapRfcConfigTrait.php
 *
 * Common code for connection configuration.
 *
 * @package saprfc-kralik
 * @author  Gregor J.
 * @license MIT
 */

namespace phpsap\saprfc;

use phpsap\exceptions\IncompleteConfigException;

/**
 * Trait phpsap\saprfc\SapRfcConfigTrait
 *
 * Common code for connection configuration. Implements methods of
 * phpsap\classes\AbstractConfigContainer.
 *
 * @package phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
trait SapRfcConfigTrait
{
    /**
     * Generate the type of configuration needed by the PHP module in order to
     * establish a connection to SAP.
     * @return mixed
     * @throws \phpsap\exceptions\IncompleteConfigException
     */
    public function generateConfig()
    {
        $config = [];
        foreach (static::$conParamAvail as $key => $mandatory) {
            if ($this->has($key)) {
                $method = sprintf('get%s', ucfirst($key));
                $config[$key] = $this->{$method}();
            } elseif ($mandatory === true) {
                throw new IncompleteConfigException(sprintf(
                    'Missing mandatory key %s.',
                    $key
                ));
            }
        }
        return $config;
    }
}
