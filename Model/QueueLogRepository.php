<?php

declare(strict_types=1);

namespace MageSuite\Queue\Model;

class QueueLogRepository
{
    protected $queueLogFactory;
    protected $queueLogResourceModel;

    public function __construct(
        \MageSuite\Queue\Model\QueueLogFactory $queueLogFactory,
        \MageSuite\Queue\Model\ResourceModel\QueueLog $queueLogResourceModel
    ) {
        $this->queueLogFactory = $queueLogFactory;
        $this->queueLogResourceModel = $queueLogResourceModel;
    }

    /**
     * @return \MageSuite\Queue\Model\QueueLog
     */
    public function createNewQueueLog(array $data, bool $save = true): \MageSuite\Queue\Model\QueueLog
    {
        $queueLog = $this->queueLogFactory->create();
        $queueLog->setType($data['type'] ?? 'n/a');
        $queueLog->setLog($data['log'] ?? 'n/a');

        if (!empty($data['message_id'])) {
            $queueLog->setMessageId($data['message_id']);
        }

        if ($save === false) {
            return $queueLog;
        }

        return $this->save($queueLog);
    }

    /**
     * @param array $messageIds
     * @return array
     */
    public function getMessagesserializeLogDataFromDb(array $messageIds): array
    {
        $adapter = $this->queueLogResourceModel->getConnection();

        $select = $adapter->select()->from($adapter->getTableName('queue_message'))
            ->where('id in ?', $messageIds);

        return $adapter->fetchAll($select);
    }

    /**
     * @param QueueLog $queueLog
     * @return QueueLog
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\MageSuite\Queue\Model\QueueLog $queueLog): \MageSuite\Queue\Model\QueueLog
    {
        try {
            $this->queueLogResourceModel->save($queueLog);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __('Could not save the queue log: %1', $exception->getMessage()),
                $exception
            );
        }

        return $queueLog;
    }
}
