<?php

declare(strict_types=1);

namespace MageSuite\Queue\Test\Integration\Observer;

abstract class AbstractLoggerTestCase extends \PHPUnit\Framework\TestCase
{
    protected ?\Magento\Framework\Event\ManagerInterface $eventManager;
    protected ?\Magento\Framework\ObjectManagerInterface $objectManager;
    protected ?\MageSuite\Queue\Model\QueueHandler $queueHandler;
    protected ?\MageSuite\Queue\Model\Resolver\QueueLogTypeResolver $queueLogTypeResolver;
    protected ?\Magento\Framework\App\ResourceConnection $resourceConnection;
    protected ?\Magento\Framework\Serialize\SerializerInterface $serializer;
    protected ?\MageSuite\Queue\Service\Logger $loggerServiceStub = null;

    protected function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->eventManager = $this->objectManager->create(\Magento\Framework\Event\ManagerInterface::class);
        $this->queueHandler = $this->objectManager->create(\MageSuite\Queue\Model\QueueHandler::class);
        $this->queueLogTypeResolver = $this->objectManager->create(\MageSuite\Queue\Model\Resolver\QueueLogTypeResolver::class);
        $this->resourceConnection = $this->objectManager->get(\Magento\Framework\App\ResourceConnection::class);
        $this->serializer = $this->objectManager->get(\Magento\Framework\Serialize\SerializerInterface::class);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testLogTypeResolverHasConfiguration(): void
    {
        $this->assertIsArray($this->queueLogTypeResolver->getConfiguration());
    }

    protected function getContainer(): \MageSuite\Queue\Api\ContainerInterface
    {
        $container = $this->objectManager->get(\MageSuite\Queue\Api\ContainerInterface::class);
        $container
            ->setHandler(\MageSuite\Queue\Test\Integration\Fixtures\ConsumerHandler::class)
            ->setData('Test Data');

        return $container;
    }

    protected function getContainerWithSerializedData(string $handler, string $data = 'Test Data'): \MageSuite\Queue\Api\ContainerInterface
    {
        $container = $this->objectManager->get(\MageSuite\Queue\Api\ContainerInterface::class);

        $container->setHandler($handler);
        $serializedData = $this->serializer->serialize($data);
        $container->setData($serializedData);

        return $container;
    }

    protected function setLoggerServiceStub(): void
    {
        $this->loggerServiceStub = $this->getMockBuilder(\MageSuite\Queue\Service\Logger::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->objectManager->addSharedInstance(
            $this->loggerServiceStub,
            \MageSuite\Queue\Service\Logger::class
        );
    }

    protected function callEvent(string $event, \MageSuite\Queue\Api\ContainerInterface $container): void
    {
        $methodName = $this->queueLogTypeResolver->getConfigurationByEventType(static::EVENT_TYPE);
        $this->loggerServiceStub->expects($this->once())->method($methodName);

        $this->eventManager->dispatch($event, ['container' => [$container]]);

        $message = sprintf('Not assert equals %s and %s', $methodName, static::LOGGER_METHOD);

        $this->assertEquals($methodName, static::LOGGER_METHOD, $message);
    }

    protected function isLogContained(string $string, string $message, ?bool $messageId = null): void
    {
        $adapter = $this->resourceConnection->getConnection();

        $select = $adapter->select()->from($adapter->getTableName('queue_logs'))
            ->order('log_id desc')
            ->limit(1);

        if ($messageId) {
            $select->where('message_id is not null');
        }

        $row = $adapter->fetchRow($select);
        $this->assertIsArray($row, $message);

        $this->assertTrue(
            str_contains($row['log'], $string) || str_contains($row['type'], $string),
            $message
        );
    }
}
