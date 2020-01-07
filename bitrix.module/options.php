<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;

require_once __DIR__ .'/helper.php';

$module_id = bx_module_id();
$LOC = bx_loc_prefix();

Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/options.php');
Loc::loadMessages(__FILE__);

// проверка прав на настройки модуля
if ($APPLICATION->GetGroupRight($module_id) < 'S')
{
    $APPLICATION->AuthForm(Loc::getMessage('ACCESS DENIED'));
}

Loader::includeModule($module_id);

$request = HttpApplication::getInstance()->getContext()->getRequest();

// формируем вкладки и поля форм
$aTabs =
[
    [
        'DIV' => 'edit1',
        'TAB' => Loc::getMessage($LOC .'TAB_SETTINGS'),
        'OPTIONS' =>
        [
            [
                'field_text',
                Loc::getMessage($LOC .'FIELD_TEXT_TITLE'),
                '',
                ['textarea', 10, 50]
            ],
            [
                'field_line',
                Loc::getMessage($LOC .'FIELD_LINE_TITLE'),
                '',
                ['text', 10]
            ],
            [
                'field_list',
                Loc::getMessage($LOC .'FIELD_LIST_TITLE'),
                '',
                ['multiselectbox', ['var1' => 'var1', 'var2' => 'var2', 'var3' => 'var3', 'var4' => 'var4']]
            ]
        ]
    ],
    [
        'DIV' => 'edit2',
        'TAB' => Loc::getMessage('MAIN_TAB_RIGHTS'),
        'TITLE' => Loc::getMessage('MAIN_TAB_TITLE_RIGHTS')
    ]
];

// сохранение настроек
if ($request->isPost() && $request['Update'] && check_bitrix_sessid())
{
    foreach ($aTabs as $aTab)
    {
        foreach ($aTab['OPTIONS'] as $arOption)
        {
            if (!is_array($arOption)) continue; // строка с подсветкой, используется для разделения настроек в одной вкладке
            if ($arOption['note']) continue; // уведомление с подсветкой

            __AdmSettingsSaveOption($module_id, $arOption);
        }
    }
}

// вывод формы
$tabControl = new CAdminTabControl('tabControl', $aTabs);
?>

<? $tabControl->Begin(); ?>
<form method="POST"
    action="<?=$APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($request['mid'])?>&lang=<?=$request['lang']?>"
    name="<?=bx_module_id_prefix() . '_settings'?>">

    <?
    foreach ($aTabs as $aTab)
    {
        if ($aTab['OPTIONS'])
        {
            $tabControl->BeginNextTab();
            __AdmSettingsDrawList($module_id, $aTab['OPTIONS']);
        }
    }
    ?>

    <? $tabControl->BeginNextTab(); ?>
    <? require_once $_SERVER['DOCUMENT_ROOT'] .'/bitrix/modules/main/admin/group_rights.php'; ?>

    <? $tabControl->Buttons(); ?>
    <input type="submit" name="Update" value="<?=Loc::getMessage('MAIN_SAVE')?>">
    <input type="reset" name="reset" value="<?=Loc::getMessage('MAIN_RESET')?>">
    
    <?=bitrix_sessid_post()?>
</form>
<? $tabControl->End(); ?>