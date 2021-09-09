<?php
declare(strict_types=1);

namespace Skillaerea\LocaleManager\Model;

use Magento\Framework\Escaper;
use Magento\Framework\Model\AbstractModel;
use Skillaerea\LocaleManager\Model\ResourceModel\StringUtils;

class Translate extends AbstractModel
{
    /**
     * translate record db fields
     */
    const SOURCE = 'string';
    const TRANSLATED = 'translate';
    const LOCALE = 'locale';

    /**
     * Source name
     */
    const SOURCE_DB = 'Database';

    /**
     * Keys for translations array
     */
    const LOCALES = 'locales';
    const SOURCES = 'sources';

    /**
     * @var StringUtils
     */
    public $stringUtils;

    /**
     * @var StringUtilsCollection
     */
    public $stringUtilsCollection;

    /**
     * @var Escaper
     */
    public $escaper;

    /**
     * Constructor
     *
     * @param StringUtils $stringUtils
     * @param StringUtilsCollection $stringUtilsCollection
     * @param Escaper $escaper
     */
    public function __construct(
        StringUtils $stringUtils,
        StringUtilsCollection $stringUtilsCollection,
        Escaper $escaper
    ) {
        $this->stringUtils = $stringUtils;
        $this->stringUtilsCollection = $stringUtilsCollection;
        $this->escaper = $escaper;
    }

    /**
     * Save translated string
     *
     * @param string $string - source string
     * @param string $translate - translated string
     * @param string|null $locale - locale to translate
     */
    public function saveTranslate($string, $translate, $locale = null)
    {
        $this->stringUtils->saveTranslate($string, $translate, $locale);
    }

    /**
     * Get all translations from database using collection
     *
     * Format translations to structure
     *  key (en/us)
     *    locales
     *       - en_US : translated
     *    sources:
     *       - database
     *
     * @return array
     */
    public function getTranslations()
    {
        $translations = [];
        $this->stringUtilsCollection->addFieldToSelect([self::SOURCE, self::TRANSLATED, self::LOCALE]);
        $select = $this->stringUtilsCollection->getData();
        foreach ($select as $row) {
            $translations[$row[self::SOURCE]][self::LOCALES][$row[self::LOCALE]] = $this->escaper->escapeHtml($row[self::TRANSLATED]);
            $translations[$row[self::SOURCE]][self::SOURCES] = [self::SOURCE_DB];
        }
        return $translations;
    }
}
