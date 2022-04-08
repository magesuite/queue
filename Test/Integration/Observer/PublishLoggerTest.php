<?php

declare(strict_types=1);

namespace MageSuite\Queue\Test\Integration\Observer;

class PublishLoggerTest extends \MageSuite\Queue\Test\Integration\Observer\AbstractLoggerTest
{
    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @param string $event
     * @param $container
     * @dataProvider getTestCases
     */
    public function testLogMessageMethodCall(string $event, $container)
    {
        $this->setLoggerServiceStub();

        $config = $this->queueLogTypeResolver->getConfigurationByEventType('publish');
        $this->loggerServiceStub->expects($this->once())->method($config['method_name']);

        $this->eventManager->dispatch($event, ['container' => [$container]]);

        $this->assertEquals($config['method_name'], 'logMessage');
        $this->assertIsArray($config['params']);
        $this->assertArrayHasKey('eventName', $config['params']);
        $this->assertEquals('eventName', $config['params']['eventName']);
        $this->assertArrayHasKey('event', $config['params']);
        $this->assertEquals('event', $config['params']['event']);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @param string $event
     * @param $container
     * @dataProvider getTestCasesForDb
     * @magentoConfigFixture current_store queues/general/is_logger_enabled 1
     * @magentoConfigFixture current_store queues/general/log_types publish_message
     * @throws \Exception
     */
    public function testInsertingLogMessageToDb(string $event, $container)
    {
        $this->eventManager->dispatch($event, ['container' => [$container]]);

        $this->isLogContained(
            \MageSuite\Queue\Service\Logger::EVENT_PUBLISH_MESSAGE,
            'A Publish message has been not added to queue log'
        );
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store queues/general/is_logger_enabled 1
     * @magentoConfigFixture current_store queues/general/log_types publish_message
     * @throws \Exception
     */
    public function testInsertingPluginLogMessageToDb()
    {
        $publisher = $this->objectManager->create(\MageSuite\Queue\Service\Publisher::class);
        $publisher->publish(\MageSuite\Queue\Test\Integration\Fixtures\ConsumerHandler::class, 'Message');

        $this->isLogContained(
            'Plugin afterLogSavedMessage',
            'A Publish message has been not added to queue log',
            true
        );
    }

    /**
     * @return array[]
     */
    public function getTestCases()
    {
        $container = $this->getContainer();

        return [
            [
                'event' => 'magesuite_consumer_amqp_before_publish',
                'container' => $container,
            ],
            [
                'event' => 'magesuite_consumer_db_before_publish',
                'container' => $container,
            ],
            [
                'event' => 'magesuite_consumer_amqp_after_publish',
                'container' => $container,
            ],
            [
                'event' => 'magesuite_consumer_db_after_publish',
                'container' => $container,
            ],
        ];
    }

    /**
     * @return array[]
     */
    public function getTestCasesForDb()
    {
        $container = $this->getContainer();

        return [
            [
                'event' => 'magesuite_consumer_db_before_publish',
                'container' => $container,
            ],
            [
                'event' => 'magesuite_consumer_db_after_publish',
                'container' => $container,
            ],
        ];
    }
}
