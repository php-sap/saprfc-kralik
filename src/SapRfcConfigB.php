<?php
/**
 * File src/SapRfcConfigB.php
 *
 * Type B configuration.
 *
 * @package saprfc-kralik
 * @author  Gregor J.
 * @license MIT
 */

namespace phpsap\saprfc;

use phpsap\classes\AbstractConfigB;

/**
 * Class phpsap\saprfc\SapRfcConfigB
 *
 * Configure connection parameters for SAP remote function calls using load
 * balancing (type B).
 *
 * @package phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
class SapRfcConfigB extends AbstractConfigB
{
    /**
     * @var array list all connection parameters available
     */
    protected static $conParamAvail = [
        'client'    => true,
        'user'      => true,
        'passwd'    => true,
        'mshost'    => true,
        'r3name'    => false,
        'group'     => false,
        'lang'      => false,
        'trace'     => false
    ];

    /**
     * Common code for connection configuration. Implements methods of
     * phpsap\classes\AbstractConfigContainer.
     */
    use SapRfcConfigTrait;
}
