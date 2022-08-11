<?php

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Loader,
    Bitrix\Main\Application,
    Bitrix\Main\Entity\Base,
    Bitrix\Main\Config\Option,
    Bitrix\Main\ModuleManager,
    Cookiesstudio\Currency\CurrencyTable;

/**
 * Модуль курса валют для CMS Bitrix
 *
 * Class cookiesstudio_currency
 */
class cookiesstudio_currency extends CModule
{
    var $MODULE_ID = "cookiesstudio.currency";

    /**
     * cookiesstudio_currency constructor
     */
    public function __construct()
    {
        $arModuleVersion = array();

        include(__DIR__ . '/version.php');

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->MODULE_NAME = Loc::getMessage("COOKIESSTUDIO_CURRENCY_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("COOKIESSTUDIO_CURRENCY_MODULE_DESCRIPTION");
        $this->PARTNER_NAME = Loc::getMessage("COOKIESSTUDIO_CURRENCY_PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage("COOKIESSTUDIO_CURRENCY_PARTNER_URI");
    }

    /**
     * Добавление данных модуля в БД
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectException
     * @throws \Bitrix\Main\SystemException
     */
    function InstallDB()
    {
        Loader::includeModule($this->MODULE_ID);

        if (!Application::getConnection()->isTableExists(
            Base::getInstance("\Cookiesstudio\Currency\CurrencyTable")->getDBTableName()
        ))
        {
            Base::getInstance("\Cookiesstudio\Currency\CurrencyTable")->createDbTable();
        }
        (new Cookiesstudio\Currency\loadCurrency)->executeComponent();
    }

    /**
     * Удаление данных модуля из БД
     *
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\DB\SqlQueryException
     * @throws \Bitrix\Main\SystemException
     */
    function UnInstallDB()
    {
        Loader::includeModule($this->MODULE_ID);
        Application::getConnection(CurrencyTable::getConnectionName())->
        queryExecute("drop table if exists ".Base::getInstance("\Cookiesstudio\Currency\CurrencyTable")->getDBTableName());
        Option::delete($this->MODULE_ID);
    }

    /**
     * Добавления файлов модуля в структуру сайта
     *
     * @return bool
     */
    function InstallFiles(): bool
    {
        CopyDirFiles($this->GetPath()."/install/components", $_SERVER["DOCUMENT_ROOT"]."/local/components/", true, true);
        return true;
    }

    /**
     * Удаление файлов модуля в структуру сайта
     *
     * @return bool
     */
    function UnIntstallFiles(): bool
    {
        \Bitrix\Main\IO\Directory::deleteDirectory($_SERVER["DOCUMENT_ROOT"]."/local/components/cookiesstudio/currency");
        return true;
    }

    /**
     * Добавление агента модуля
     *
     * @return void
     */
    function InstallAgent()
    {
        CAgent::AddAgent( "\\Cookiesstudio\Currency\loadCurrency::executeComponent();", "cookiesstudio.currency");
    }

    /**
     * Удаление агента модуля
     *
     * @return void
     */
    function UnInstallAgent()
    {
        CAgent::RemoveModuleAgents("cookiesstudio.currency");
    }

    /**
     * Проверка поддержки функционала D7
     *
     * @return bool
     */
    function isVersionD7(): bool
    {
        return CheckVersion(ModuleManager::getVersion("main"), "14.00.00");
    }

    /**
     * Получения пути к модулю
     *
     * @param $notDocumentRoot
     * @return array|string|string[]
     */
    public function GetPath($notDocumentRoot=false)
    {
        if ($notDocumentRoot)
        {
            return str_ireplace(Application::getDocumentRoot(),'',dirname(__DIR__));
        }
        else
        {
            return dirname(__DIR__);
        }
    }

    /**
     * Установка модуля
     *
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectException
     * @throws \Bitrix\Main\SystemException
     */
    function DoInstall()
    {
        global $APPLICATION;

        if ($this->isVersionD7())
        {
            ModuleManager::registerModule($this->MODULE_ID);
            $this->InstallDB();
            $this->InstallFiles();
            $this->InstallAgent();
        }
        else
        {
            $APPLICATION->ThrowException(Loc::getMessage("COOKIESSTUDIO_CURRENCY_INSTALL_ERROR_VERSION"));
        }

        $APPLICATION->IncludeAdminFile(Loc::getMessage("COOKIESSTUDIO_CURRENCY_INSTALL_TITLE"), $this->GetPath()."/install/step1.php");
    }

    /**
     * Удаление модуля в 2 шага, с возможностью сохранения данных в БД
     *
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\DB\SqlQueryException
     * @throws \Bitrix\Main\SystemException
     */
    function DoUninstall()
    {
        global $APPLICATION;

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        if ($request["step"] < 2)
        {
            $APPLICATION->IncludeAdminFile(Loc::getMessage("COOKIESSTUDIO_CURRENCY_UNINSTALL_TITLE"), $this->GetPath()."/install/unstep1.php");
        }
        elseif ($request["step"] == 2)
        {
            $this->UnIntstallFiles();
            $this->UnInstallAgent();

            if ($request["savedata"] != "Y")
            {
                $this->UnInstallDB();
            }

            ModuleManager::unRegisterModule($this->MODULE_ID);
            $APPLICATION->IncludeAdminFile(Loc::getMessage("COOKIESSTUDIO_CURRENCY_UNINSTALL_TITLE"), $this->GetPath()."/install/unstep2.php");
        }
    }
}