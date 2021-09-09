<?php
declare(strict_types=1);

namespace Skillaerea\LocaleManager\Model;

use Magento\Framework\Module\FullModuleList;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\Module\Dir;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Escaper;
use Skillaerea\LocaleManager\Model\SkillaereaTranslate;
use Skillaerea\LocaleManager\Model\Translate;
use Skillaerea\LocaleManager\Model\LanguagePack;

class Module extends AbstractModel
{
    /**
     * @var Translate
     */
    public $translate;

    /**
     * @var FullModuleList
     */
    public $fullModuleList;

    /**
     * @var Dir
     */
    public $dir;

    /**
     * @var ReadFactory
     */
    public $read;

    /**
     * @var FileDriver
     */
    public $fileDriver;

    /**
     * @var Csv
     */
    public $csvParser;

    /**
     * @var Escaper
     */
    public $escaper;

    /**
     * @var array
     */
    public $merged = [];

    /**
     * Filters
     */
    public $filterKeyword;
    public $filterFiles = [];
    public $filterDirs = [];


    const PARAM_KEYWORD = 'keyword';
    const PARAM_FILES = 'files';
    const PARAM_DIRS = 'dirs';


    /**
     * Language Pack source name
     */
    const SOURCE_LP = 'Language Pack';

    /**
     * Constructor
     *
     * @param FullModuleList $fullModuleList
     * @param ReadFactory $read
     * @param Dir $dir
     * @param Translate $translate
     * @param Csv $csvParser
     * @param File $fileDriver
     * @param Escaper $escaper
     * @param LanguagePack $languagePack
     */
    public function __construct(
        FullModuleList $fullModuleList,
        ReadFactory $read,
        Dir $dir,
        Translate $translate,
        Csv $csvParser,
        File $fileDriver,
        Escaper $escaper,
        LanguagePack $languagePack
    ) {
        $this->translate = $translate;
        $this->fullModuleList = $fullModuleList;
        $this->dir = $dir;
        $this->read = $read;
        $this->fileDriver = $fileDriver;
        $this->csvParser = $csvParser;
        $this->escaper = $escaper;
        $this->languagePack = $languagePack;
    }

    /**
     * Get all translation files from modules in folowing format
     *
     * 'Magento_Sales' =>
     *   ['path/to/Magento/Sales/i18n' => ['en_US.csv','uk_GB.csv']]
     *
     * @return array
     */
    public function getModuleTranslations()
    {
        $this->directories = [];
        foreach ($this->getDirectories() as $module => $path) {
            try {
                $files = $this->read->create($path);
                $data = $files->read();
                $this->directories[$module] = [$path => $data];
            } catch (FileSystemException $e) {
                // no i18n folder in module dir - not something critical
            }
        }
        return $this->directories;
    }

    /**
     * Parse files and prepare module translations in format
     *
     * Hello
     *   - locales
     *       - en_US => 'Hello'
     *       - es_ES => 'Hola'
     *   - sources
     *      - Language Pack
     *
     * @return array
     */
    public function getTranslations()
    {
        $this->moduleTranslations = [];
        foreach ($this->getModuleTranslations() as $module => $moduleFiles) {
            if ($this->filterFiles) {
                if (!in_array($module, $this->filterFiles)) {
                    continue;
                }
            }
            foreach ($moduleFiles as $path => $files) {
                if ($this->filterDirs) {
                    if (!in_array($path, $this->filterDirs)) {
                        continue;
                    }
                }
                foreach ($files as $file) {
                    $translation = $this->parseCSV(sprintf("%s/%s", $path, $file));
                    $locale = $this->fromCsvToLocale($file);
                    foreach ($translation as $key => $string) {
                        $key = $this->escaper->escapeHtml($key);
                        $this->moduleTranslations[$key][Translate::LOCALES][$locale] = $this->escaper->escapeHtml($string);
                        $this->moduleTranslations[$key][Translate::SOURCES] = [$module];
                    }
                }
            }
        }
        return $this->moduleTranslations;
    }

    /**
     * Merge all translations from different sources
     * - First order - db
     * - Second order module translations
     * - Third order - language packs
     *
     * @return array
     */
    public function combineTranslations()
    {
        $module = $this->getTranslations();

        $theme = [];
        if ($this->isPackTranslationsNeeded()) {
            $theme = $this->languagePack->getTranslations();
        }

        $db = $this->translate->getTranslations();
        $this->merged = array_merge_recursive($db, $module, $theme);
        return $this->applyFilters();
    }

    /**
     * Apply filters action
     *
     * @return array
     */
    public function applyFilters()
    {
        if ($this->filterKeyword) {
            $this->filterByKeyword();
        }
        return $this->merged;
    }

    /**
     * Filters data by Keyword
     *
     * Finding matching keys and prepare new results set
     *
     */
    public function filterByKeyWord()
    {
        $result = [];
        foreach ($this->merged as $key => $array) {
            if (stristr((string)$key, $this->filterKeyword)) {
                $result[$key] = $array;
            }
        }
        $this->merged = $result;
    }

    /**
     * Retrieve data from file
     *
     * @param string $file
     * @return array
     */
    public function parseCSV($file)
    {
        $data = [];
        if ($this->fileDriver->isExists($file)) {
            $this->csvParser->setDelimiter(',');
            $data = $this->csvParser->getDataPairs($file);
        }
        return $data;
    }

    /**
     * Set filters for translations
     *
     * @param array $filters
     */
    public function setFilters(array $filters)
    {
        if (isset($filters[self::PARAM_DIRS])) {
            $this->filterDirs = $filters[self::PARAM_DIRS];
        }
        if (isset($filters[self::PARAM_KEYWORD])) {
            $this->filterKeyword = $filters[self::PARAM_KEYWORD];
        }
        if (isset($filters[self::PARAM_FILES])) {
            $this->filterFiles = $filters[self::PARAM_FILES];
        }
    }

    /**
     * Pack translations needed to load only if
     * there's filter dirs / files applied without keyword
     *
     * @return bool
     */
    public function isPackTranslationsNeeded()
    {
        $result = true;
        if ($this->filterFiles || $this->filterDirs) {
            $result = false;
        }
        return $result;
    }

    /**
     * Get array of all directories for filters
     * Sample output:
     * [['Magento_Sales' => 'path/to/magento/sales'],  ... ]
     *
     * @return array
     */
    public function getDirectories()
    {
        $directories = [];
        foreach ($this->getAllModules() as $module) {
            $path = $this->dir->getDir($module['name'], Dir::MODULE_I18N_DIR);
            try {
                $files = $this->read->create($path);
                $directories[$module['name']] = $path;
            } catch (FileSystemException $e) {
                // no i18n folder in module dir - not something critical
            }
        }
        return $directories;
    }

    /**
     * Helper method to convert from en_US.csv -> en_US
     *
     * @param string $fileName
     * @return string
     */
    public function fromCsvToLocale($fileName)
    {
        $result = $fileName;
        $parts = explode(".csv", $fileName);
        if (isset($parts[0])) {
            $result = $parts[0];
        }
        return $result;
    }

    /**
     * Get list of all modules
     *
     * @return array
     */
    public function getAllModules()
    {
        return $this->fullModuleList->getAll();
    }
}
