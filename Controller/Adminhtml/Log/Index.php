<?php

declare(strict_types=1);

namespace MageSuite\Queue\Controller\Adminhtml\Log;

class Index extends \Magento\Backend\App\Action
{
    public const ADMIN_RESOURCE = 'MageSuite_Queue::config_queue';

    protected \Magento\Framework\View\Result\PageFactory $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('MageSuite Queue Processing Logs')));

        return $resultPage;
    }
}
