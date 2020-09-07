<?php

namespace MageSuite\Queue\Service;

class Publisher
{
    /**
     * @var \Magento\Framework\MessageQueue\PublisherInterface
     */
    protected $publisher;

    /**
     * @var \MageSuite\Queue\Api\ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $consumerName;

    public function __construct(
        \Magento\Framework\MessageQueue\PublisherInterface $publisher,
        \MageSuite\Queue\Api\ContainerInterface $container,
        $consumerName
    ) {
        $this->publisher = $publisher;
        $this->container = $container;
        $this->consumerName = $consumerName;
    }

    public function publish($handler, $data)
    {
        $this->container
            ->setHandler($handler)
            ->setData($data);

        $this->publisher->publish($this->consumerName, $this->container);
    }
}
