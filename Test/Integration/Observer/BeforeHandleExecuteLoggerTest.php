<?php

declare(strict_types=1);

namespace MageSuite\Queue\Test\Integration\Observer;

class BeforeHandleExecuteLoggerTest extends \MageSuite\Queue\Test\Integration\Observer\AbstractLoggerTest
{
    protected const EVENT_TYPE = 'before';
    protected const LOGGER_METHOD = 'logHandleBeforeExecute';

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
     * @magentoConfigFixture current_store queues/general/log_types before_handle_execute
     * @throws \Exception
     */
    public function testInsertingLogMessageToDb()
    {
        $container = $this->getContainerWithSerializedData(
            \MageSuite\Queue\Test\Integration\Fixtures\ConsumerHandler::class
        );

        $this->queueHandler->execute($container);

        $this->isLogContained(
            \MageSuite\Queue\Service\Logger::EVENT_BEFORE_HANDLE_EXECUTE,
            'Before handle execute a message has been not added to queue log'
        );
    }

    /**
     * @return array[]
     */
    public function getTestCases()
    {
        return [
            [
                'event' => 'magesuite_queue_handler_execute_before',
                'container' => $this->getContainer(),
                'expects' => 1,
            ],
        ];
    }
}
