<?php
declare(strict_types=1);

namespace Skillaerea\LocaleManager\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Skillaerea\LocaleManager\Model\Module;
use Skillaerea\LocaleManager\Model\Locale;
use Skillaerea\LocaleManager\Model\Translate;

class Manager extends Template
{
    /**
     * @var string
     */
    const TRANSLATION_GOOD = 'good';

    /**
     * @var string
     */
    const TRANSLATION_BAD = 'bad';

    /**
     * @var string
     */
    const TRANSLATION_WARM = 'warm';

    /**
     * @var string
     */
    const TRANSLATION_WARM_PATTERN = '/##[a-z]+##/isU';

    /**
     * @var int
     */
    const TRANSLATION_WARM_LENGTH_LIMIT = 255;

    /**
     * @var Locale
     */
    public $locale;

    /**
     * @var Module
     */
    public $module;

    /**
     * applied filters state
     *
     * @var array
     */
    public $filters;

    /**
     * grid data
     *
     * @var array
     */
    public $grid = [];

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param Module $module
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Module $module,
        Locale $locale,
        array $data = []
    ) {
        $this->module = $module;
        $this->locale = $locale;
        parent::__construct($context, $data);
    }

    /**
     * Set Filters
     *
     * @param array $filters Key - value filters
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;
    }

    /**
     * Get filters
     *
     * @return array List of applied filters
     */
    public function getFilters()
    {
        $filters = [];
        foreach ([Module::PARAM_KEYWORD, Module::PARAM_DIRS, Module::PARAM_FILES] as $filterKey) {
            if ($this->getRequest()->getParam($filterKey)) {
                $filters[$filterKey] = $this->getRequest()->getParam($filterKey);
            }
        }
        return $filters;
    }

    /**
     * Get filtered files
     *
     * @return array
     */
    public function getFilterFiles()
    {
        return $this->getRequest()->getParam(Module::PARAM_FILES, []);
    }

    /**
     * Get filtered dirs
     *
     * @return array
     */
    public function getFilterDirs()
    {
        return $this->getRequest()->getParam(Module::PARAM_DIRS, []);
    }

    /**
     * Get keyword
     *
     * @return string
     */
    public function getKeyword()
    {
        return $this->getRequest()->getParam(Module::PARAM_KEYWORD);
    }

    /**
     * Get files
     *
     * @return array
     */
    public function getFiles()
    {
        return $this->module->getAllModules();
    }

    /**
     * Get directories - used in Form Dropdown
     *
     * @return array
     */
    public function getDirectories()
    {
        return $this->module->getDirectories();
    }

    /**
     * Get Grid Data
     *
     * @return array
     */
    public function getGrid()
    {
        if (!$this->grid) {
            if ($this->getFilters()) {
                $this->module->setFilters($this->getFilters());
                $this->grid = $this->module->combineTranslations();
            }
        }
        return $this->grid;
    }

    /**
     * Collect stats (total number)
     * of good, bad, warn translations
     * used only for rendering in grid on frontend
     *
     * @return array
     */
    public function getStats()
    {
        $totals = [];
        foreach ($this->getLocales() as $locale) {
            foreach ([self::TRANSLATION_GOOD, self::TRANSLATION_BAD, self::TRANSLATION_WARM] as $type) {
                $totals[$locale][$type] = 0;
            }
        }
        foreach ($this->getGrid() as $key => $row) {
            foreach ($this->getLocales() as $locale) {
                $translate = $this->getTranslated($row, $locale);
                $type = $this->getTranslationStatus($translate);
                $totals[$locale][$type]++;
            }
        }
        return $totals;
    }

    /**
     * Analyze translated text for stats & for frontend coloring
     * If translation exists - it's good
     * If translation not exists - bad
     * If it contain unexpected characters or length > 255 - warn notice
     *
     * @param $translate
     * @return string
     */
    public function getTranslationStatus($translate)
    {
        if ($translate) {
            $type = self::TRANSLATION_GOOD;
        } else {
            $type = self::TRANSLATION_BAD;
        }

        if (preg_match(self::TRANSLATION_WARM_PATTERN, $translate)) {
            $type = self::TRANSLATION_WARM;
        }
        if (mb_strlen($translate) > self::TRANSLATION_WARM_LENGTH_LIMIT) {
            $type = self::TRANSLATION_WARM;
        }

        return $type;
    }

    /**
     * Get locales
     *
     * @return array
     */
    public function getLocales()
    {
        return $this->locale->getLocales();
    }

    /**
     * Format sources row in table
     *
     *  [sources] => Array ( [0] => Magento_Directory [1] => Language Pack ) )
     *  Magento_Directory / Language Pack
     *
     * @param array $row
     * @return string
     */
    public function getSources($row)
    {
        return implode(" / ", $row[Translate::SOURCES]);
    }

    /**
     * Get translated string from
     * $row: Array ( [locales] => Array ( [en_US] => Environment emulation nesting is not allowed. [nl_NL] => Emulation nesting omgeving is niet toegestaan.
     * $locale - nl_NL
     *
     * @param array $row
     * @param string $locale
     *
     * @return string
     */
    public function getTranslated($row, $locale)
    {
        $result = '';
        if (isset($row[Translate::LOCALES][$locale])) {
            $result = $row[Translate::LOCALES][$locale];
            if (is_array($result)) {
                $result = $result[0];
            }
        }
        return $result;
    }

}
