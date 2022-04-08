<?php

declare(strict_types=1);

namespace MageSuite\Queue\Service;

class Logger
{
    public const EVENT_PUBLISH_MESSAGE = 'publish_message';
    public const EVENT_BEFORE_HANDLE_EXECUTE = 'before_handle_execute';
    public const EVENT_FAILED_HANDLE_EXECUTE = 'failed_handle_execute';
    public const EVENT_AFTER_HANDLE_EXECUTE = 'after_handle_execute';
    public const EVENT_EXCEPTION = 'exception';

    public const EVENTS = [
        self::EVENT_PUBLISH_MESSAGE => 'Publish message',
        self::EVENT_BEFORE_HANDLE_EXECUTE => 'Before handle execute',
        self::EVENT_FAILED_HANDLE_EXECUTE => 'Failed handle execute',
        self::EVENT_AFTER_HANDLE_EXECUTE => 'After handle execute',
        self::EVENT_EXCEPTION => 'Exceptions',
    ];

    protected \MageSuite\Queue\Helper\Configuration $configuration;
    protected \Magento\Framework\MessageQueue\MessageEncoder $messageSerializer;
    protected \MageSuite\Queue\Model\QueueLogRepository $queueLogRepository;
    protected ?\Magento\Framework\Serialize\SerializerInterface $serializer;

    public function __construct(
        \MageSuite\Queue\Helper\Configuration $configuration,
        \Magento\Framework\MessageQueue\MessageEncoder $messageSerializer,
        \MageSuite\Queue\Model\QueueLogRepository $queueLogRepository,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    ) {
        $this->configuration = $configuration;
        $this->messageSerializer = $messageSerializer;
        $this->queueLogRepository = $queueLogRepository;
        $this->serializer = $serializer;
    }

    /**
     * @param \Magento\Framework\Event $event
     * @return void
     */
    public function logException(\Magento\Framework\Event $event): void
    {
        if ($this->configuration->isEventEnabled(self::EVENT_EXCEPTION) === false) {
            return;
        }

        $message = $this->getEncodedContainer($event);
        $exceptionMessage = $event->getException()->getMessage();
        $exceptionTrace = $event->getException()->getTraceAsString();

        $data = [
            'type' => self::EVENT_EXCEPTION,
            'log' => sprintf("%s \n %s \ %s", $message, $exceptionMessage, $exceptionTrace),
        ];

        $this->queueLogRepository->createNewQueueLog($data);
    }

    /**
     * @param string $eventName
     * @param \Magento\Framework\Event $event
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function logMessage(string $eventName, \Magento\Framework\Event $event): void
    {
        if ($this->configuration->isEventEnabled(self::EVENT_PUBLISH_MESSAGE) === false) {
            return;
        }

        $message = $this->getEncodedContainer($event);

        $data = [
            'type' => self::EVENT_PUBLISH_MESSAGE,
            'log' => sprintf("%s \n %s", $eventName, $message),
        ];

        $this->queueLogRepository->createNewQueueLog($data);
    }

    /**
     * @param int $messageId
     * @param string $message
     */
    public function logSavedMessage(int $messageId, string $message): void
    {
        if ($this->configuration->isEventEnabled(self::EVENT_PUBLISH_MESSAGE) === false) {
            return;
        }

        $logData = sprintf('Plugin %s | %s', 'afterLogSavedMessage', $message);
        $data = [
            'type' => self::EVENT_PUBLISH_MESSAGE,
            'message_id' => $messageId,
            'log' => $logData,
        ];

        $this->queueLogRepository->createNewQueueLog($data);
    }

    /**
     * @param array $messagesIds
     */
    public function logSavedMessages(array $messagesIds): void
    {
        if ($this->configuration->isEventEnabled(self::EVENT_PUBLISH_MESSAGE) === false) {
            return;
        }

        $messages = $this->queueLogRepository->getMessagesFromDb($messagesIds);

        foreach ($messages as $message) {
            $logData = sprintf('Plugin %s | %s', 'afterLogSavedMessages', $message['body']);

            $data = [
                'type' => self::EVENT_PUBLISH_MESSAGE,
                'message_id' => $message['id'],
                'log' => $logData,
            ];

            $this->queueLogRepository->createNewQueueLog($data);
        }
    }

    /**
     * @param \Magento\Framework\Event $event
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function logHandleNotExecute(\Magento\Framework\Event $event): void
    {
        if ($this->configuration->isEventEnabled(self::EVENT_FAILED_HANDLE_EXECUTE) === false) {
            return;
        }

        $message = $this->getEncodedContainer($event);

        $data = [
            'type' => self::EVENT_FAILED_HANDLE_EXECUTE,
            'log' => $message,
        ];

        $this->queueLogRepository->createNewQueueLog($data);
    }

    /**
     * @param \Magento\Framework\Event $event
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function logHandleBeforeExecute(\Magento\Framework\Event $event): void
    {
        if ($this->configuration->isEventEnabled(self::EVENT_BEFORE_HANDLE_EXECUTE) === false) {
            return;
        }

        $message = $this->getEncodedContainer($event);

        $data = [
            'type' => self::EVENT_BEFORE_HANDLE_EXECUTE,
            'log' => $message,
        ];

        $this->queueLogRepository->createNewQueueLog($data);
    }

    /**
     * @param \Magento\Framework\Event $event
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function logHandleAfterExecute(\Magento\Framework\Event $event): void
    {
        if ($this->configuration->isEventEnabled(self::EVENT_AFTER_HANDLE_EXECUTE) === false) {
            return;
        }

        $message = $this->getEncodedContainer($event);

        $data = [
            'type' => self::EVENT_AFTER_HANDLE_EXECUTE,
            'log' => $message,
        ];

        $this->queueLogRepository->createNewQueueLog($data);
    }

    /**
     * @param \Magento\Framework\Event $event
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getEncodedContainer(\Magento\Framework\Event $event, $eventType = null): string
    {
        $container = $event->getContainer();

        if (!$eventType) {
            $eventType = $this->getEventTypeFromEventName($event);
        }

        if (is_array($container)) {
            foreach ($container as &$containerItem) {
                $containerItem = $this->serializeLogData($containerItem);
            }
            return sprintf(
                'EventType: %s | %s',
                $eventType,
                $this->serializer->serialize($container)
            );
        } else {
            return sprintf(
                'EventType: %s | %s',
                $eventType,
                $this->serializeLogData($container)
            );
        }
    }

    /**
     * @param \MageSuite\Queue\Api\ContainerInterface $container
     * @return bool|string
     */
    protected function serializeLogData(\MageSuite\Queue\Api\ContainerInterface $container): string
    {
        $containerData = $container->getData();

        if (!is_string($containerData)) {
            $containerData = $this->serializer->unserialize($containerData);
        }

        $data = [
            'handler' => $container->getHandler(),
            'data' => $containerData,
        ];

        return $this->serializer->serialize($data);
    }

    /**
     * @param \Magento\Framework\Event $event
     * @return string
     */
    protected function getEventTypeFromEventName(\Magento\Framework\Event $event): string
    {
        $eventName = $event->getName();
        $eventNameExploded = explode('_', $eventName);
        return end($eventNameExploded);
    }
}
