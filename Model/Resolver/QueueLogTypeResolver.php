<?php

namespace MageSuite\Queue\Model\Resolver;

class QueueLogTypeResolver
{
    protected array $configuration;

    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return array
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    /**
     * @param string $eventType
     * @return mixed
     * @throws \Magento\Framework\Exception\InvalidArgumentException
     */
    public function getConfigurationByEventType(string $eventType)
    {
        if (!isset($this->configuration[$eventType])) {
            throw new \Magento\Framework\Exception\InvalidArgumentException(__('Configuration does not exist for type %1', $eventType));
        }

        return $this->configuration[$eventType];
    }
}
