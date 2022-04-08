<?php

declare(strict_types=1);

namespace MageSuite\Queue\Observer;

class LogEventDataToDb implements \Magento\Framework\Event\ObserverInterface
{
    protected \MageSuite\Queue\Service\Logger $loggerService;
    protected \MageSuite\Queue\Model\Resolver\QueueLogTypeResolver $resolver;

    public function __construct(
        \MageSuite\Queue\Service\Logger $loggerService,
        \MageSuite\Queue\Model\Resolver\QueueLogTypeResolver $resolver
    ) {
        $this->loggerService = $loggerService;
        $this->resolver = $resolver;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $eventName = $observer->getEvent()->getName();
        $eventNameExploded = explode('_', $eventName);
        $eventType = end($eventNameExploded);
        $event = $observer->getEvent();

        $resolve = $this->resolver->getConfigurationByEventType($eventType);

        if (is_string($resolve)) {
            $this->loggerService->{$resolve}($event);
        } elseif (is_array($resolve) || $this->canBeCalledWithConfig($resolve)) {
            $this->callMethodWithParams($resolve, $event);
        }
    }

    /**
     * @param array $resolve
     * @return bool
     */
    protected function canBeCalledWithConfig(array $resolve): bool
    {
        return !empty($resolve['method_name'])
            && !empty($resolve['params'])
            && is_array($resolve['params'])
            && method_exists($this->loggerService, $resolve['method_name']);
    }

    /**
     * @param $resolve
     * @param \Magento\Framework\Event $event
     */
    protected function callMethodWithParams($resolve, \Magento\Framework\Event $event): void
    {
        $params = [];
        $eventName = $event->getName();

        foreach ($resolve['params'] as $param) {
            $params[] = ${$param} ?? null;
        }

        call_user_func_array( // phpcs:ignore
            [
                $this->loggerService,
                $resolve['method_name']
            ],
            $params
        );
    }
}
