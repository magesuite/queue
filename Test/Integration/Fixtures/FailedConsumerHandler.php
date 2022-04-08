<?php

declare(strict_types=1);

namespace MageSuite\Queue\Test\Integration\Fixtures;

class FailedConsumerHandler
{
    public function execute($data)
    {
        return true;
    }
}
