<?php
declare(strict_types=1);

namespace Skillaerea\Request\Controller\Page;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 */
class View implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param PageFactory $pageFactory
     * @param RequestInterface $request
     */
    public function __construct(PageFactory $pageFactory, RequestInterface $request)
    {
        $this->pageFactory = $pageFactory;
        $this->request = $request;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        // Get the params that were passed from our Router
        $firstParam = $this->request->getParam('first_param', null);
        $secondParam = $this->request->getParam('second_param', null);
        $this->request->setParams(['message' => 'Hello World!']);

        return $this->pageFactory->create();
    }
}
