<?php
declare(strict_types=1);

namespace Skillaerea\LocaleManager\Model\ResourceModel;

use Magento\Translation\Model\ResourceModel\StringUtils as TranslationStringUtils;

/**
 * String translation utilities
 */
class StringUtils extends TranslationStringUtils
{
    /**
     * Save translation
     *
     * @param String $string
     * @param String $translate
     * @param String $locale
     * @param int|null $storeId
     * @return $this
     */
    public function saveTranslate($string, $translate, $locale = null, $storeId = null)
    {
        $string = htmlspecialchars_decode($string);
        $connection = $this->getConnection();
        $table = $this->getMainTable();
        $translate =  htmlspecialchars($translate, ENT_COMPAT | ENT_SUBSTITUTE, 'UTF-8', false);

        if ($locale === null) {
            $locale = $this->_localeResolver->getLocale();
        }

        if ($storeId === null) {
            $storeId = $this->getStoreId();
        }

        $select = $connection->select()->from(
            $table,
            ['key_id', 'translate']
        )->where(
            'store_id = :store_id'
        )->where(
            'locale = :locale'
        )->where(
            'string = :string'
        )->where(
            'crc_string = :crc_string'
        );
        $bind = [
            'store_id' => $storeId,
            'locale' => $locale,
            'string' => $string,
            'crc_string' => crc32($string),
        ];

        if ($row = $connection->fetchRow($select, $bind)) {
            $original = $string;
            if (strpos($original, '::') !== false) {
                list(, $original) = explode('::', $original);
            }
            if ($original == $translate) {
                $connection->delete($table, ['key_id=?' => $row['key_id']]);
            } elseif ($row['translate'] != $translate) {
                $connection->update($table, ['translate' => $translate], ['key_id=?' => $row['key_id']]);
            }
        } else {
            $connection->insert(
                $table,
                [
                    'store_id' => $storeId,
                    'locale' => $locale,
                    'string' => $string,
                    'translate' => $translate,
                    'crc_string' => crc32($string)
                ]
            );
        }

        return $this;
    }
}
