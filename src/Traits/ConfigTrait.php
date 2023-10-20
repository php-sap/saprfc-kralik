<?php

declare(strict_types=1);

namespace phpsap\saprfc\Traits;

use phpsap\interfaces\Config\IConfigCommon;
use phpsap\interfaces\Config\IConfigTypeA;
use phpsap\interfaces\Config\IConfigTypeB;
use phpsap\interfaces\Config\IConfiguration;
use phpsap\interfaces\exceptions\IIncompleteConfigException;
use function array_merge;

/**
 * Trait ConfigTrait
 * @package phpsap\saprfc
 * @author  Gregor J.
 * @license MIT
 */
trait ConfigTrait
{
    /**
     * Get the module specific connection configuration.
     * @param IConfiguration $config
     * @return array
     * @throws IIncompleteConfigException
     */
    protected function getModuleConfig(IConfiguration $config): array
    {
        return array_merge(
            $this->getCommonConfig($config),
            $this->getSpecificConfig($config)
        );
    }

    /**
     * Only type A and B configurations are supported by this module,
     * its common classes and its interface. Therefore, we do not
     * expect any other types here.
     * @param IConfiguration $config
     * @return array
     * @throws IIncompleteConfigException
     */
    private function getSpecificConfig(IConfiguration $config): array
    {
        if ($config instanceof IConfigTypeA) {
            return $this->getTypeAConfig($config);
        }
        return $this->getTypeBConfig($config);
    }

    /**
     * Get the common configuration for the saprfc module.
     *
     * I chose a "stupid" (and repetitive) way because it is more readable
     * and thus better maintainable for others than an "intelligent" way.
     *
     * @param IConfigCommon $config
     * @return array
     * @throws IIncompleteConfigException
     */
    private function getCommonConfig(IConfigCommon $config): array
    {
        $common = [];
        if ($config->getLang() !== null) {
            $common['lang'] = $config->getLang();
        }
        $common['client'] = $config->getClient();
        $common['user'] = $config->getUser();
        $common['passwd'] = $config->getPasswd();
        return $common;
    }

    /**
     * Get the connection type A configuration for the saprfc module.
     *
     * I chose a "stupid" (and repetitive) way because it is more readable
     * and thus better maintainable for others than an "intelligent" way.
     *
     * @param IConfigTypeA $config
     * @return array
     * @throws IIncompleteConfigException
     */
    private function getTypeAConfig(IConfigTypeA $config): array
    {
        $typeA = [];
        if ($config->getGwhost() !== null) {
            $typeA['gwhost'] = $config->getGwhost();
        }
        if ($config->getGwserv() !== null) {
            $typeA['gwserv'] = $config->getGwserv();
        }
        $typeA['ashost'] = $config->getAshost();
        $typeA['sysnr']  = $config->getSysnr();
        return $typeA;
    }

    /**
     * Get the connection type B configuration for the saprfc module.
     *
     * I chose a "stupid" (and repetitive) way because it is more readable
     * and thus better maintainable for others than an "intelligent" way.
     *
     * @param IConfigTypeB $config
     * @return array
     * @throws IIncompleteConfigException
     */
    private function getTypeBConfig(IConfigTypeB $config): array
    {
        $typeB = [];
        if ($config->getR3name() !== null) {
            $typeB['r3name'] = $config->getR3name();
        }
        if ($config->getGroup() !== null) {
            $typeB['group'] = $config->getGroup();
        }
        $typeB['mshost'] = $config->getMshost();
        return $typeB;
    }
}
