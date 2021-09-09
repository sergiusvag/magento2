<?php
declare(strict_types=1);

namespace Skillaerea\LocaleManager\Controller\Adminhtml\Index;

use Magento\Backend\Model\Session;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Skillaerea\LocaleManager\Model\Translate;

class Save extends Action
{
    /**
     * @var JsonFactory
     */
    public $jsonFactory;

    /**
     * @var Translate
     */
    public $translate;

    /**
     * Query params for saving entity
     */
    const PARAM_STRING = 'string';
    const PARAM_TRANSLATE = 'translate';
    const PARAM_LOCALE = 'locale';
    const DEFAULT_LOCALE_CODE = 'en_US';
    const LOCALE_SESSION_KEY = 'session_locale';

    /**
     * @var Session
     */
    private $backendSession;

    /**
     * Save constructor.
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param Translate $translate
     * @param Session $backendSession
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        Translate $translate,
        Session $backendSession
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->translate = $translate;
        $this->backendSession = $backendSession;
    }

    /**
     * Save / edit action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->jsonFactory->create();

        $string = $this->getRequest()->getParam(self::PARAM_STRING);
        $translate = $this->getRequest()->getParam(self::PARAM_TRANSLATE);
        $locale = $this->getRequest()->getParam(self::PARAM_LOCALE, null);

        if ($string) {
            try {
                $this->translate->saveTranslate($string, $translate, $locale);
                //set default locale code to avoid changing locale
                $this->backendSession->setData(self::LOCALE_SESSION_KEY, self::DEFAULT_LOCALE_CODE);
                $data = ['error' => 0];
            } catch (Exception $e) {
                $data = ['error' => 1, 'message' => $e->getMessage()];
            }
        } else {
            $data = ['error' => 2, 'message' => 'Missing string to translate'];
        }
        $resultPage->setData($data);

        return $resultPage;
    }
}
