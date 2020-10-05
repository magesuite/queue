<?php

namespace MageSuite\Queue\Api\Queue;

interface HandlerInterface
{
    /**
     * @param mixed $data
     * @return $this
     */
    public function execute($data);
}
