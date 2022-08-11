<?php

namespace Cookiesstudio\Currency;

use Bitrix\Main,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\Type,
    CBitrixComponent,
    Exception;

/**
 * Class loadCurrency
 *
 * @package Cookiesstudio\Currency
 */
class loadCurrency extends CBitrixComponent
{

    /**
     * Проверка подключения необходимых модулей
     *
     * @throws Main\LoaderException
     */
    protected function checkModules()
    {
        if (!Main\Loader::IncludeModule("cookiesstudio.currency"))
        {
            throw new Main\LoaderException(Loc::getMessage("COOKIESSTUDIO_CURRENCY_MODULE_NOT_INSTALLED"));
        }
    }

    /**
     * Добавление валют за сегодня
     *
     * @throws Main\ObjectException
     * @throws Exception
     */
    function var1()
    {
        $xml = simplexml_load_file("https://www.cbr.ru/scripts/XML_daily.asp?date_req=" .date('d/m/Y'));
        $date = new Type\DateTime(strval($xml["Date"]));

        foreach($xml as $val)
        {
            $result = CurrencyTable::add(array(
                "CODE" => strval($val->CharCode),
                "DATE" => $date,
                "COURSE" => $val->Value/$val->Nominal,
            ));
        }

        return $result;
    }

    /**
     * @throws Main\ObjectException
     * @throws Main\LoaderException
     */
    public function executeComponent()
    {
        $load = new loadCurrency();
        $load->includeComponentLang("loadCurrency.php");
        $load->checkModules();

        $result = $load->var1();

        if ($result->isSuccess())
        {
            $id = $result->getId();
            $load->arResult = "Запись добавлена с id: ".$id;
        }
        else
        {
            $error = $result->getErrorMessages();
            $load->arResult = "Произошла ошибка при добавлении: <pre>".var_export($error, true)."</pre>";
        }

        $load->includeComponentTemplate();
    }

}