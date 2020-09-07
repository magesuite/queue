<?php

namespace MageSuite\Queue\Api;

interface ContainerInterface
{
    /**
     * @param string $handler
     * @return $this
     */
    public function setHandler($handler);

    /**
     * @return string
     */
    public function getHandler();

    /**
     * @param mixed $data
     * @return $this
     */
    public function setData($data);

    /**
     * @return mixed
     */
    public function getData();
}
