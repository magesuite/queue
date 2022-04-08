<?php // phpcs:ignoreFile

declare(strict_types=1);

namespace MageSuite\Queue\Model\ResourceModel;

class QueueLog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected $_idFieldName = 'log_id';
    protected $_mainTable = 'queue_logs';

    protected function _construct()
    {
        $this->_init($this->_mainTable, $this->_idFieldName);
    }
}
