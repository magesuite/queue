<?php

declare(strict_types=1);

namespace MageSuite\Queue\Test\Integration\Observer;

class AfterHandleExecuteLoggerTest extends \MageSuite\Queue\Test\Integration\Observer\AbstractLoggerTestCase
{
    protected const EVENT_TYPE = 'after';
    protected const LOGGER_METHOD = 'logHandleAfterExecute';

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @dataProvider getTestCases
     */
    public function testLogHandleNotExecuteMethodCall(string $event, \MageSuite\Queue\Api\ContainerInterface $container): void
    {
        $this->setLoggerServiceStub();
        $this->callEvent($event, $container);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store queues/general/is_logger_enabled 1
     * @magentoConfigFixture current_store queues/general/log_types after_handle_execute
     */
    public function testInsertingLogMessageToDb(): void
    {
        $container = $this->getContainerWithSerializedData(
            \MageSuite\Queue\Test\Integration\Fixtures\ConsumerHandler::class
        );

        $this->queueHandler->execute($container);

        $this->isLogContained(
            \MageSuite\Queue\Service\Logger::EVENT_AFTER_HANDLE_EXECUTE,
            'After handle execute a message has been not added to queue log'
        );
    }

    public static function getTestCases(): array
    {
        return [
            [
                'event' => 'magesuite_queue_handler_execute_after',
                'container' => self::getContainer()
            ],
        ];
    }
}
