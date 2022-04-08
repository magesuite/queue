<?php

declare(strict_types=1);

namespace MageSuite\Queue\Test\Integration\Observer;

class FailedHandleExecuteLoggerTest extends \MageSuite\Queue\Test\Integration\Observer\AbstractLoggerTest
{
    protected const EVENT_TYPE = 'failed';
    protected const LOGGER_METHOD = 'logHandleNotExecute';

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @param string $event
     * @param $container
     * @dataProvider getTestCases
     */
    public function testLogHandleNotExecuteMethodCall(string $event, $container)
    {
        $this->setLoggerServiceStub();
        $this->callEvent($event, $container);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store queues/general/is_logger_enabled 1
     * @magentoConfigFixture current_store queues/general/log_types failed_handle_execute
     * @throws \Exception
     */
    public function testInsertingLogMessageToDb()
    {
        $container = $this->getContainerWithSerializedData(
            \MageSuite\Queue\Test\Integration\Fixtures\FailedConsumerHandler::class
        );

        $this->queueHandler->execute($container);

        $this->isLogContained(
            \MageSuite\Queue\Service\Logger::EVENT_FAILED_HANDLE_EXECUTE,
            'Failed handle execute a message has been not added to queue log'
        );
    }

    /**
     * @return array[]
     */
    public function getTestCases()
    {
        return [
            [
                'event' => 'magesuite_queue_handler_execute_failed',
                'container' => $this->getContainer(),
                'expects' => 1,
            ],
        ];
    }
}
