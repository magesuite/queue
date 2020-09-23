<?php

namespace MageSuite\Queue\Model;

class QueueHandler implements \MageSuite\Queue\Api\QueueHandlerInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    ) {
        $this->objectManager = $objectManager;
        $this->serializer = $serializer;
    }

    public function execute(\MageSuite\Queue\Api\ContainerInterface $container)
    {
        $handler = $this->objectManager->get($container->getHandler());

        if (!$handler instanceof \MageSuite\Queue\Api\Queue\HandlerInterface) {
            return;
        }

        $data = $this->serializer->unserialize($container->getData());
        $handler->execute($data);
    }
}
