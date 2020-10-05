<?php

namespace MageSuite\Queue\Service;

class Publisher
{
    const AMQP_CONSUMER_NAME = 'magesuite.consumer.amqp';
    const DATABASE_CONSUMER_NAME = 'magesuite.consumer.db';

    /**
     * @var \Magento\Framework\MessageQueue\PublisherInterface
     */
    protected $publisher;

    /**
     * @var \MageSuite\Queue\Api\ContainerInterface
     */
    protected $container;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * @var \Magento\Framework\App\DeploymentConfig
     */
    protected $deploymentConfig;

    public function __construct(
        \Magento\Framework\MessageQueue\PublisherInterface $publisher,
        \MageSuite\Queue\Api\ContainerInterface $container,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Framework\App\DeploymentConfig $deploymentConfig
    ) {
        $this->publisher = $publisher;
        $this->container = $container;
        $this->serializer = $serializer;
        $this->deploymentConfig = $deploymentConfig;
    }

    public function publish($handler, $data)
    {
        //We need to serialize date to ensure Magento will not change the data structure
        $data = $this->serializer->serialize($data);

        $this->container
            ->setHandler($handler)
            ->setData($data);

        $this->publisher->publish($this->getConsumerName(), $this->container);
    }

    protected function getConsumerName()
    {
        $queueConfig = $this->deploymentConfig->getConfigData(\Magento\Framework\Amqp\Config::QUEUE_CONFIG);

        if (empty($queueConfig)) {
            return self::DATABASE_CONSUMER_NAME;
        }

        return self::AMQP_CONSUMER_NAME;
    }
}
