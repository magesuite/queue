<?php

namespace MageSuite\Queue\Model;

class Container implements \MageSuite\Queue\Api\ContainerInterface
{
    /**
     * @var string
     */
    protected $handler;

    /**
     * @var mixed
     */
    protected $data;

    public function setHandler($handler)
    {
        $this->handler = $handler;

        return $this;
    }

    public function getHandler()
    {
        return $this->handler;
    }

    public function setData($data)
    {
        $this->data = json_encode($data);

        return $this;
    }

    public function getData()
    {
        return json_decode($this->data, true);
    }
}
