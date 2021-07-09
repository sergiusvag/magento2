<?php
namespace Skillaerea\Request\Block;

use \Magento\Framework\View\Element\Template;
use \Magento\Framework\View\Element\Template\Context;

class Index extends Template
{
    public function __construct(Context $context,
                                \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    public function getNameData()
    {
        return $this->getName();
    }

    public function getEmailData()
    {
        return $this->getEmail();
    }

    public function getMessageData()
    {
        return $this->getMessage();
    }

    public function getAgeData()
    {
        return $this->getAge();
    }

    public function getDocData()
    {
        return $this->getDoc();
    }
}
