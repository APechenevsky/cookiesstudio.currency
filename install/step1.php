<?php

use Bitrix\Main\Localization\Loc;

// Проверка id сесии
if (!check_bitrix_sessid())
{
    return;
}

// Проверка на ошибки
if ($ex = $APPLICATION->GetException())
{
    // При возниконовении ошибки выводится информация о ней
    echo CAdminMessage::ShovMessage(array(
        "TYPE" => "ERROR",
        "MESSAGE" => Loc::getMessage("MOD_INST_ERR"),
        "DETAILS" => $ex->GetString(),
        "HTML" => true,
    ));
}
else
{
    // При отсутсвии ошибки выводится информация об успешной установке
    echo CAdminMessage::ShowNote(Loc::getMessage("MOD_INST_OK"));
}

//Создание формы, которая возвращает на список модулей
?>

<form action="<?php echo $APPLICATION->GetCurPage()?>">
    <input type="hidden" name="lang" value="<?php echo LANGUAGE_ID?>">
    <input type="submit" name="" value="<?php echo Loc::getMessage("MOD_BACK")?>">
</form>