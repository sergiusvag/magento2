<?php

namespace Skillaerea\Request\Controller\Valid;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use \Magento\Framework\App\Action\Action;

class Valid extends Action
{
    /**
     * @var Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    protected $resultJsonFactory;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        $name = $this->getRequest()->getParam('name');
        $email = $this->getRequest()->getParam('email');
        $message = $this->getRequest()->getParam('message');
        $age = $this->getRequest()->getParam('age');
        $targetDirectory = $this->getRequest()->getParam('target_directory');
//        $doc = $this->getRequest()->getParam('doc');
        $result = $this->resultJsonFactory->create();
        $resultPage = $this->resultPageFactory->create();

        $allValidMessage = "";
        $targetFileName = basename($_FILES["doc"]["name"]);
        $targetFile = $targetDirectory . $targetFileName;

        if ($targetFileName != "")
        {
            if (move_uploaded_file($_FILES["doc"]["tmp_name"], $targetFile))
            {
                $allValidMessage = "The file was uploaded successfully";
            } else {
                $allValidMessage = "Sorry, there was an error uploading your file.";
            }
        } else {
            $allValidMessage = "No file";
        }

        $doc = $allValidMessage;

        $block = $resultPage->getLayout()
            ->createBlock('Skillaerea\Request\Block\Index')
            ->setTemplate('Skillaerea_Request::form_message.phtml')
            ->setData('name',$name)
            ->setData('email',$email)
            ->setData('message',$message)
            ->setData('age',$age)
            ->setData('doc',$doc)
            ->toHtml();

        $result->setData(['output' => $block]);
        return $result;
    }
}
