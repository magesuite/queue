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
    
    public function afterSaveMessage(
        \Magento\MysqlMq\Model\ResourceModel\Queue $subject,
        string $messageId,
        string $messageTopic,
        string $messageBody
    ): ?string {
        if ($messageTopic == \MageSuite\Queue\Service\Publisher::CONSUMER_NAME && !empty($messageId)) {
            $this->loggerService->logSavedMessage((int) $messageId, $messageBody);
        }

        return $messageId;
    }
    
    public function afterSaveMessages(
        \Magento\MysqlMq\Model\ResourceModel\Queue $subject,
        array $messageIds,
        string $messageTopic,
        array $messageBody
    ): ?array {
        if ($messageTopic == \MageSuite\Queue\Service\Publisher::CONSUMER_NAME && !empty($messageIds)) {
            $this->loggerService->logSavedMessages($messageIds);
        }

        return $messageIds;
    }
}
