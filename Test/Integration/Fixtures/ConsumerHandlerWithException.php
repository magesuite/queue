<?php

declare(strict_types=1);

namespace MageSuite\Queue\Test\Integration\Fixtures;

class ConsumerHandlerWithException implements \MageSuite\Queue\Api\Queue\HandlerInterface
{
    public const EXCEPTION_MESSAGE = 'Exception message';

    public function execute($data)
    {
        throw new \Magento\Framework\Exception\InvalidArgumentException(__(self::EXCEPTION_MESSAGE)); // phpcs:ignore
    }
}
