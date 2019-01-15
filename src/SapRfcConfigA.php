<?php
/**
 * File src/SapRfcConfigA.php
 *
 * Type A configuration.
 *
 * @package saprfc-kralik
 * @author  Gregor J.
 * @license MIT
 */

namespace phpsap\saprfc;

use phpsap\classes\AbstractConfigA;

/**
 * Class phpsap\saprfc\SapRfcConfigA
 *
 * Configure connection parameters for SAP remote function calls using a specific
 * SAP application server (type A).
 *
 * @package phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
class SapRfcConfigA extends AbstractConfigA
{
    /**
     * @var array list all connection parameters available
     */
    protected static $conParamAvail = [
        'ashost'    => true,
        'sysnr'     => true,
        'client'    => true,
        'user'      => true,
        'passwd'    => true,
        'gwhost'    => false,
        'gwserv'    => false,
        'lang'      => false,
        'trace'     => false
    ];

    /**
     * Common code for connection configuration. Implements methods of
     * phpsap\classes\AbstractConfigContainer.
     */
    use SapRfcConfigTrait;
}
