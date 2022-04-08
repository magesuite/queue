<?php

declare(strict_types=1);

namespace MageSuite\Queue\Plugin\MysqlMq\Model\ResourceModel\Queue;

class LogPublishedMessageId
{
    protected \MageSuite\Queue\Service\Logger $loggerService;

    public function __construct(\MageSuite\Queue\Service\Logger $loggerService)
    {
        $this->loggerService = $loggerService;
    }

    /**
     * @param \Magento\MysqlMq\Model\ResourceModel\Queue $subject
     * @param string $result
     * @param string $messageTopic
     * @param string $messageBody
     * @return string
     */
    public function afterSaveMessage(
        \Magento\MysqlMq\Model\ResourceModel\Queue $subject,
        string $messageId,
        string $messageTopic,
        string $messageBody
    ): ?string {
        if ($messageTopic == 'magesuite.consumer.db' && !empty($messageId)) {
            $this->loggerService->logSavedMessage((int) $messageId, $messageBody);
        }

        return $messageId;
    }

    /**
     * @param \Magento\MysqlMq\Model\ResourceModel\Queue $subject
     * @param string $result
     * @param string $messageTopic
     * @param string $messageBody
     * @return array
     */
    public function afterSaveMessages(
        \Magento\MysqlMq\Model\ResourceModel\Queue $subject,
        array $messageIds,
        string $messageTopic,
        array $messageBody
    ): ?array {
        if ($messageTopic == 'magesuite.consumer.db' && !empty($messageIds)) {
            $this->loggerService->logSavedMessages($messageIds);
        }

        return $messageIds;
    }
}
