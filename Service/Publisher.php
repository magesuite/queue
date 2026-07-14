<?php

declare(strict_types=1);

namespace MageSuite\Queue\Service;

class Publisher
{
    public const CONSUMER_NAME = 'magesuite.consumer';

    protected \MageSuite\Queue\Api\ContainerInterface $container;
    protected \Magento\Framework\App\DeploymentConfig $deploymentConfig;
    protected \Magento\Framework\Event\ManagerInterface $eventManager;
    protected \Magento\Framework\MessageQueue\PublisherInterface $publisher;
    protected \Magento\Framework\Serialize\SerializerInterface $serializer;

    public function __construct(
        \MageSuite\Queue\Api\ContainerInterface $container,
        \Magento\Framework\App\DeploymentConfig $deploymentConfig,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\MessageQueue\PublisherInterface $publisher,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    ) {
        $this->container = $container;
        $this->deploymentConfig = $deploymentConfig;
        $this->eventManager = $eventManager;
        $this->publisher = $publisher;
        $this->serializer = $serializer;
    }

    /**
     * @param string $handler
     * @param mixed $data
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\RuntimeException
     */
    public function publish(string $handler, $data)
    {
        //We need to serialize date to ensure Magento will not change the data structure
        $data = $this->serializer->serialize($data);

        $this->container
            ->setHandler($handler)
            ->setData($data);

        $eventConsumerName = str_replace('.', '_', $this->getConsumerName());
        $eventName = sprintf('%s_before_publish', $eventConsumerName);
        $this->eventManager->dispatch($eventName, ['container' => $this->container]);

        $this->publisher->publish($this->getConsumerName(), $this->container);

        $eventName = sprintf('%s_after_publish', $eventConsumerName);
        $this->eventManager->dispatch($eventName, ['container' => $this->container]);
    }

    protected function getConsumerName(): string
    {
        return self::CONSUMER_NAME;
    }
}
