<?php

namespace MageSuite\Queue\Model;

class QueueHandler implements \MageSuite\Queue\Api\QueueHandlerInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function execute(\MageSuite\Queue\Api\ContainerInterface $container)
    {
        $handler = $this->objectManager->get($container->getHandler());

        if (!$handler instanceof \MageSuite\Queue\Api\Queue\HandlerInterface) {
            return;
        }

        $handler->execute($container->getData());
    }
}
