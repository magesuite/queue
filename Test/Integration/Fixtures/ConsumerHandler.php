<?php

declare(strict_types=1);

namespace MageSuite\Queue\Test\Integration\Fixtures;

class ConsumerHandler implements \MageSuite\Queue\Api\Queue\HandlerInterface
{
    public function execute($data)
    {
        return true;
    }
}
