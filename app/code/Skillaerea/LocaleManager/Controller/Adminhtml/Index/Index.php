<?php
declare(strict_types=1);

namespace Skillaerea\LocaleManager\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\Page;

class Index extends Action implements HttpPostActionInterface, HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * Constructor
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Render
     *
     * @return Page
     */
    public function execute()
    {
        return $resultPage = $this->resultPageFactory->create();
    }
}
