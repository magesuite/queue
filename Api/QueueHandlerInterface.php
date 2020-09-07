<?php

namespace MageSuite\Queue\Api;

interface QueueHandlerInterface
{
    /**
     * @return void
     */
    public function execute(\MageSuite\Queue\Api\ContainerInterface $container);
}
