<?php

declare(strict_types=1);

namespace MageSuite\Queue\Test\Integration\Observer;

class ExceptionLoggerTest extends \MageSuite\Queue\Test\Integration\Observer\AbstractLoggerTestCase
{
    protected const EVENT_TYPE = 'exception';
    protected const LOGGER_METHOD = 'logException';

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @dataProvider getTestCases
     */
    public function testLogExceptionMethodCall(string $event, \MageSuite\Queue\Api\ContainerInterface $container): void
    {
        $this->setLoggerServiceStub();
        $this->callEvent($event, $container);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store queues/general/is_logger_enabled 1
     * @magentoConfigFixture current_store queues/general/log_types exception
     */
    public function testInsertingLogMessageToDb(): void
    {
        $container = $this->getContainerWithSerializedData(
            \MageSuite\Queue\Test\Integration\Fixtures\ConsumerHandlerWithException::class
        );

        try {
            $this->queueHandler->execute($container);
        } catch (\Exception $e) {

            $this->assertInstanceOf(\Magento\Framework\Exception\InvalidArgumentException::class, $e);
            $this->assertEquals(
                \MageSuite\Queue\Test\Integration\Fixtures\ConsumerHandlerWithException::EXCEPTION_MESSAGE,
                $e->getMessage()
            );

            $this->isLogContained(
                \MageSuite\Queue\Test\Integration\Fixtures\ConsumerHandlerWithException::EXCEPTION_MESSAGE,
                'Exception message has been not added to queue log'
            );
        }
    }

    public static function getTestCases(): array
    {
        return [
            [
                'event' => 'magesuite_queue_handler_execute_exception',
                'container' => self::getContainer()
            ],
        ];
    }
}
