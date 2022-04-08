<?php

declare(strict_types=1);

namespace MageSuite\Queue\Model\Config\Source;

class LogTypes implements \Magento\Framework\Data\OptionSourceInterface
{
    protected $options = [];

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [];

        foreach ($this->options as $optionId => $option) {
            $options[] = [
                'label' => __($option),
                'value' => $optionId
            ];
        }

        return $options;
    }
}
