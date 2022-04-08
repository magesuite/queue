<?php

declare(strict_types=1);

namespace MageSuite\Queue\Model;

class QueueLog extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    public const CACHE_TAG = 'magesuite_queue_log';
    // phpcs:disable
    protected $_cacheTag = 'magesuite_queue_log';
    protected $_eventPrefix = 'magesuite_queue_log';
    protected $_eventObject = 'magesuite_queue_log';
    // phpcs:enable

    protected function _construct()
    {
        $this->_init(\MageSuite\Queue\Model\ResourceModel\QueueLog::class);
    }

    /**
     * @return string[]
     */
    public function getIdentities(): array
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
