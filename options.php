<?

use Bitrix\Main\Application,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\UI\PageNavigation,
    Bitrix\Main\Type,
    Cookiesstudio\Currency\CurrencyTable;

/**
 * Страница настроек модуля
 */

$module_id = "cookiesstudio.currency";
$currencyPerms = $APPLICATION->GetGroupRight($module_id); // Уровень доступа к модулю

// Проверка уровня доступа к модулю (Чтение)
if ($currencyPerms>="R") :

    // Подулючение модуля
    CModule::IncludeModule($module_id);

    // Проверка уровня доступа к модулю (Полный доступ) и на POST
    if ($REQUEST_METHOD == "POST" && $currencyPerms == "W" && check_bitrix_sessid())
    {
        // Получение $request
        $context = Application::getInstance()->getContext();
        $request = $context->getRequest()->getPostList()->getValues();

        if ($Update) // Обновление значения в таблице БД
        {
            CurrencyTable::update($request["Update"], array("COURSE" => $request[$request["Update"]]));
        }
        elseif ($Delete) // Удаление значиения в таблице БД
        {
            CurrencyTable::delete($request["Delete"]);
        }
        elseif ($Add) // Добавление нового значения в таблицу БД
        {
            CurrencyTable::add(array("CODE" => $request["CODE"],"DATE"=> new Type\DateTime(strval($request["DATE"]),"Y-m-d"),"COURSE" => $request["COURSE"]));
        }
    }

    // Постраничная навигация
    $nav = new PageNavigation("nav");
    $nav->allowAllRecords(true)
        ->setPageSize(20)
        ->initFromUri();

    // Получение значений из таблицы БД
    $result = CurrencyTable::getList(array(
        "select" => array("ID", "CODE", "DATE", "COURSE"), // Имена полей, которые нужно получить
        "filter" => array(), // Фильтр для выборки
        "order" => array("CODE" => "ASC"), // Параметры сортировки
        "count_total" => true, // Параметр, который заставляет ORM выполнить отдельный запрос COUNT
        "offset" => $nav->getOffset(), // Смещение для limit
        "limit" => $nav->getLimit(), // Колличество записей
    ));

    // Моссив со значениями из БД
    $arResult[] = $result->fetchAll();
    // Массив для постраничной навигации
    $arResult["NAV"] = $nav->setRecordCount($result->getCount());

    // Массив с табами на странице настроек модуля
    $aTabs = array(
        array("DIV" => "edit1", "TAB" => Loc::getMessage("COOKIESSTUDIO_CURRENCY_TAB_SET"), "ICON" => "", "TITLE" => Loc::getMessage("COOKIESSTUDIO_CURRENCY_TAB_SET_ALT")),
        array("DIV" => "edit2", "TAB" => Loc::getMessage("COOKIESSTUDIO_CURRENCY_TAB_ADD"), "ICON" => "", "TITLE" => Loc::getMessage("COOKIESSTUDIO_CURRENCY_TAB_ADD_ALT")),
    );

    // Отрисовка формы настроек модуля
    $tabControl = new CAdminTabControl("tabControl", $aTabs);
    $tabControl->Begin();
    $tabControl->BeginNextTab();

    // Отрисовка таба с возможностью просмотра данных из БД, их изменения и удаления.
    ?>

    <form method="POST" name="cookiesstudio_currency_opt_form" action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($mid) ?>&lang=<?= LANGUAGE_ID ?>" ENCTYPE="multipart/form-data">
        <? echo bitrix_sessid_post();?>
        <tr>
            <td style="text-align:left">Дата</td>
            <td style="text-align:left">Код</td>
            <td style="text-align:left">Курс</td>
            <td style="text-align:left"></td>
        </tr>
        <?php foreach($arResult["0"] as $arItem):?>
            <tr>
                <td style="text-align:left"><?=$arItem["DATE"]->format('d-m-Y');?></td>
                <td style="text-align:left"><?=$arItem["CODE"];?></td>
                <td style="text-align:left">
                    <input type="text" size="<?php echo $type[1]?>" value="<?=$arItem["COURSE"];?>" name="<?=$arItem["ID"];?>">
                </td>
                <td style="text-align:center">
                    <button type="submit" class="adm-btn adm-btn-save" <?php if ($currencyPerms < "W") echo "disabled" ?> name="Update" id="<?=$arItem["ID"];?>" value="<?=$arItem["ID"];?>"><?php echo Loc::getMessage("COOKIESSTUDIO_CURRENCY_SAVE")?></button>
                    <button type="submit" class="adm-btn adm-btn-delete" <?php if ($currencyPerms < "W") echo "disabled" ?> name="Delete" id="<?=$arItem["ID"];?>" value="<?=$arItem["ID"];?>"><?php echo Loc::getMessage("COOKIESSTUDIO_CURRENCY_DELETE")?></button>
                </td>
            </tr>
        <?php endforeach;?>
    </form>
    <?php
    // Отрисовка постраничной навигации
    $APPLICATION->IncludeComponent(
        "bitrix:main.pagenavigation",
        "",
        array(
            "NAV_OBJECT" => $arResult["NAV"],
            "SEF_MODE" => "N",
        ),
        false
    );
    ?>

    <?php
    // Переход на следующий таб
    $tabControl->BeginNextTab();

    //  Отрисовка таба с возможностью добавления новых значений в БД.
    ?>

    <form method="POST" name="cookiesstudio_currency_opt_form" action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($mid) ?>&lang=<?= LANGUAGE_ID ?>" ENCTYPE="multipart/form-data">
        <?php echo bitrix_sessid_post();?>
        <tr>
            <td style="width:25%; text-align:left">
                <input type="date" size="<?php echo $type[1]?>" value="<?=date('Y-m-d');?>" name="DATE">
            </td>
            <td style="width:25%; text-align:left">
                <input type="text" size="<?php echo $type[1]?>" value="" name="CODE">
            <td style="width:25%; text-align:left">
                <input type="number" size="<?php echo $type[1]?>" value="" name="COURSE">
            </td>
            <td style="width:25%; text-align:center">
                <button type="submit" class="adm-btn-save" <?php if ($currencyPerms < "W") echo "disabled" ?> name="Add" id="<?=$arItem["ID"];?>" value="<?=$arItem["ID"];?>"><?php echo Loc::getMessage("COOKIESSTUDIO_CURRENCY_ADD")?></button>
            </td>
        </tr>
    </form>

    <?php $tabControl->End(); // Конец отрисовки формы?>

<?php endif;?>