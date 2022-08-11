<?php
use Bitrix\Main,
    Bitrix\Main\Localization\Loc,
    Cookiesstudio\Currency\CurrencyTable,
    Bitrix\Main\UI\PageNavigation;

/**
 * Class currencyGetListExpression
 */
class currencyGetListExpression extends CBitrixComponent
{
    /**
     * Проверка подключение необходимых модулей
     *
     * @throws \Bitrix\Main\LoaderException
     */
    protected function checkModules()
    {
        if (!Main\Loader::IncludeModule("cookiesstudio.currency"))
            throw new Main\LoaderException(Loc::getMessage("COOKIESSTUDIO_CURRENCY_MODULE_NOT_INSTALLED"));
    }

    /**
     * Добавление валют за сегодня
     *
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function executeComponent()
    {
        $arParams = $this->arParams;
        $this->checkModules();

        $nav = new PageNavigation("nav");
        $nav->allowAllRecords(true)
            ->setPageSize($arParams["COUNT"])
            ->initFromUri();

        $result = CurrencyTable::getList(array(
            "select" => array("ID", "CODE", "DATE", "COURSE"), //Имена полей, которые нужно получить
            "filter" => array(), //Фильтр для выборки
            "order" => array($arParams["SORT_BY"] => $arParams["SORT_ORDER"]), //Параметры сортировки
            "count_total" => true,
            "offset" => $nav->getOffset(), //смещение для limit
            "limit" => $nav->getLimit(), //колличество записей
        ));

        $this->arResult["NAV"] = $nav->setRecordCount($result->getCount());
        $this->arResult[] = $result->fetchAll();
        $this->includeComponentTemplate();
    }
}