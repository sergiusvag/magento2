<?php
declare(strict_types=1);

namespace Skillaerea\LocaleManager\Model;

use Magento\Framework\View\DesignInterface;
use Magento\Framework\Cache\FrontendInterface;
use Magento\Framework\View\FileSystem as ViewFileSystem;
use Magento\Framework\Module\ModuleList;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\Translate\ResourceInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\App\State;
use Magento\Framework\Filesystem;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\File\Csv;
use Magento\Framework\App\Language\Dictionary;
use Magento\Framework\Translate;

class SkillaereaTranslate extends Translate
{
    /**
     * Constructor
     *
     * @param DesignInterface $viewDesign
     * @param FrontendInterface $cache
     * @param ViewFileSystem $viewFileSystem
     * @param ModuleList $moduleList
     * @param Reader $modulesReader
     * @param ScopeResolverInterface $scopeResolver
     * @param ResourceInterface $translate
     * @param ResolverInterface $locale
     * @param State $appState
     * @param Filesystem $filesystem
     * @param RequestInterface $request
     * @param Csv $csvParser
     * @param Dictionary $packDictionary
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        DesignInterface $viewDesign,
        FrontendInterface $cache,
        ViewFileSystem $viewFileSystem,
        ModuleList $moduleList,
        Reader $modulesReader,
        ScopeResolverInterface $scopeResolver,
        ResourceInterface $translate,
        ResolverInterface $locale,
        State $appState,
        Filesystem $filesystem,
        RequestInterface $request,
        Csv $csvParser,
        Dictionary $packDictionary
    ) {
        $this->_viewDesign = $viewDesign;
        $this->_cache = $cache;
        $this->_viewFileSystem = $viewFileSystem;
        $this->_moduleList = $moduleList;
        $this->_modulesReader = $modulesReader;
        $this->_scopeResolver = $scopeResolver;
        $this->_translateResource = $translate;
        $this->_locale = $locale;
        $this->_appState = $appState;
        $this->request = $request;
        $this->_csvParser = $csvParser;
        $this->packDictionary = $packDictionary;

        $this->_config = [
            self::CONFIG_AREA_KEY => null,
            self::CONFIG_LOCALE_KEY => null,
            self::CONFIG_SCOPE_KEY => null,
            self::CONFIG_THEME_KEY => null,
            self::CONFIG_MODULE_KEY => null,
        ];
    }

    /**
     * Access to Pack Translations
     *
     * @param string $localeCode
     * @return array
     */
    public function getPackTranslation($localeCode)
    {
        return $this->packDictionary->getDictionary($localeCode);
    }

    /**
     * Get parent themes for the current theme in fallback order
     *
     * @return array
     */
    public function getParentThemesList(): array
    {
        $themes = [];

        $parentTheme = $this->_viewDesign->getDesignTheme()->getParentTheme();
        while ($parentTheme) {
            $themes[] = $parentTheme;
            $parentTheme = $parentTheme->getParentTheme();
        }
        $themes = array_reverse($themes);

        return $themes;
    }

    /**
     * Retrieve translation files for themes according to fallback
     *
     * @param string $locale
     *
     * @return array
     */
    public function getThemeTranslationFilesList($locale): array
    {
        $translationFiles = [];

        /** @var \Magento\Framework\View\Design\ThemeInterface $theme */
        foreach ($this->getParentThemesList() as $theme) {
            $config = $this->_config;
            $config['theme'] = $theme->getCode();
            $translationFiles[] = $this->getThemeTranslationFileName($locale, $config);
        }

        $translationFiles[] = $this->getThemeTranslationFileName($locale, $this->_config);

        return $translationFiles;
    }

    /**
     * Get theme translation locale file name
     *
     * @param string|null $locale
     * @param array $config
     * @return string|null
     */
    public function getThemeTranslationFileName(?string $locale, array $config): ?string
    {
        $fileName = $this->_viewFileSystem->getLocaleFileName(
            'i18n' . '/' . $locale . '.csv',
            $config
        );

        return $fileName ? $fileName : null;
    }
}
