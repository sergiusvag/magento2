<?php
declare(strict_types=1);

namespace Skillaerea\LocaleManager\Model;

use Magento\Framework\Escaper;

class LanguagePack
{
    /**
     * Language Pack source name
     */
    const SOURCE_LP = 'Language Pack';

    /**
     * @var array
     */
    public $translations = [];

    /**
     * @var SkillaereaTranslate
     */
    public $translate;

    /**
     * @var Escaper
     */
    public $escaper;

    /**
     * @var Locale
     */
    public $locale;

    /**
     * Constructor
     *
     */
    public function __construct(
        SkillaereaTranslate $translate,
        Locale $locale,
        Escaper $escaper
    ) {
        $this->translate = $translate;
        $this->escaper = $escaper;
        $this->locale = $locale;
    }

    /**
     * Load language pack translations
     *
     * @return array
     */
    public function loadPack()
    {
        if (!$this->translations) {
            foreach ($this->locale->getLocales() as $locale) {
                $this->translations[$locale] = $this->translate->getPackTranslation($locale);
            }
        }
        return $this->translations;
    }

    /**
     * Prepare translations - convert to
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
        $formatted = [];
        foreach ($this->loadPack() as $locale => $data) {
            if ($data) {
                foreach ($data as $key => $value) {
                    $key = $this->escaper->escapeHtml($key);
                    $formatted[$key][Translate::LOCALES][$locale] = $this->escaper->escapeHtml($value);
                    $formatted[$key][Translate::SOURCES] = [self::SOURCE_LP];
                }
            }
        }
        return $formatted;
    }

}
