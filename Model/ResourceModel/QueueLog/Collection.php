<?php // phpcs:ignoreFile

declare(strict_types=1);

namespace MageSuite\Queue\Model\ResourceModel\QueueLog;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_eventObject = 'magesuite_queue_log_collection';
    protected $_eventPrefix = 'magesuite_queue_log_collection';
    protected $_idFieldName = 'log_id';
    protected $_mainTable = 'queue_logs';

    protected function _construct()
    {
        $this->_init(\MageSuite\Queue\Model\QueueLog::class, \MageSuite\Queue\Model\ResourceModel\QueueLog::class);
    }
}
