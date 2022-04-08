<?php

declare(strict_types=1);

namespace MageSuite\Queue\Helper;

class Configuration extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected const IS_LOGGER_ENABLED_XML_PATH = 'queues/general/is_logger_enabled';
    protected const LOG_TYPES_XML_PATH = 'queues/general/log_types';

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isLogEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::IS_LOGGER_ENABLED_XML_PATH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param string $type
     * @param int|null $storeId
     * @return bool
     */
    public function isEventEnabled(string $type, ?int $storeId = null): bool
    {
        if ($this->isLogEnabled($storeId) == false) {
            return false;
        }

        $logTypes = $this->scopeConfig->getValue(
            self::LOG_TYPES_XML_PATH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $logTypes = explode(',', $logTypes);

        return in_array($type, $logTypes);
    }
}
