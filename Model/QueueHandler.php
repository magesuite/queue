<?php

declare(strict_types=1);

namespace MageSuite\Queue\Model;

class QueueHandler implements \MageSuite\Queue\Api\QueueHandlerInterface
{
    protected \Magento\Framework\ObjectManagerInterface $objectManager;
    protected \Magento\Framework\Event\ManagerInterface $eventManager;
    protected \Magento\Framework\Serialize\SerializerInterface $serializer;

    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    ) {
        $this->eventManager = $eventManager;
        $this->objectManager = $objectManager;
        $this->serializer = $serializer;
    }

    public function execute(\MageSuite\Queue\Api\ContainerInterface $container)
    {
        $this->eventManager->dispatch('magesuite_queue_handler_execute_before', ['container' => $container]);

        $handler = $this->objectManager->get($container->getHandler());

        if (!$handler instanceof \MageSuite\Queue\Api\Queue\HandlerInterface) {
            $this->eventManager->dispatch('magesuite_queue_handler_execute_failed', ['container' => $container]);
            return;
        }

        $data = $this->serializer->unserialize($container->getData());

        try {
            $handler->execute($data);
        } catch (\Exception $e) {
            $this->eventManager->dispatch('magesuite_queue_handler_execute_exception', ['container' => $container, 'exception' => $e]);
            throw $e;
        }

        $this->eventManager->dispatch('magesuite_queue_handler_execute_after', ['container' => $container]);
    }
}
