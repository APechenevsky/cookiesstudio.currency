<?php

namespace Cookiesstudio\Currency;

use Bitrix\Main\ORM\Data\DataManager,
    Bitrix\Main\Entity;

/**
 * Class CurrencyTable
 *
 * @package Cookiesstudio\Currency
 */
class CurrencyTable extends DataManager
{
    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return "cookiesstudio_currency";
    }

    /**
     * @return string
     */
    public static function getUfid(): string
    {
        return "COOKIESSTUDIO_CURRENCY";
    }

    /**
     * @return array
     * @throws \Bitrix\Main\SystemException
     */
    public static function getMap(): array
    {
        return array(
            //ID
            new Entity\IntegerField("ID", array(
                "primary" => true,
                "autocomplete" => true
            )),
            //Код валюты
            new Entity\StringField("CODE", array(
                "required" => true
            )),
            //Дата
            new Entity\DatetimeField("DATE", array(
                "required" => true
            )),
            //Курс
            new Entity\FloatField("COURSE", array(
                "required" => true
            ))
        );
    }
}