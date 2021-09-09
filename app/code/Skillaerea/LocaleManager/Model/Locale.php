<?php
declare(strict_types=1);

namespace Skillaerea\LocaleManager\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class Locale
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Locale constructor.
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get list of Locale for all stores
     * @return array
     */
    public function getLocales()
    {
        //Locale code
        $locale = [];
        $stores = $this->storeManager->getStores($withDefault = false);
        //Try to get list of locale for all stores;
        foreach($stores as $store) {
            $locale[] = $this->scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getStoreId());
        }
        return $locale;
    }
}
