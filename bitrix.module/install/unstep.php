<?php

use Bitrix\Main\Localization\Loc;

require_once __DIR__ .'/../helper.php';

if (!check_bitrix_sessid()) return;
?>

<form action="<?=$APPLICATION->GetCurPage()?>">
    <?=bitrix_sessid_post()?>
    <input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
    <input type="hidden" name="id" value="<?=bx_module_id()?>">
    <input type="hidden" name="uninstall" value="Y">
    <input type="hidden" name="step" value="2">
    <?=CAdminMessage::ShowMessage(Loc::getMessage('MOD_UNINST_WARN'))?>
    <p><?=Loc::getMessage('MOD_UNINST_SAVE')?></p>
    <p>
        <input type="checkbox" name="savedata" id="savedata" value="Y" checked>
        <label for="savedata"><?=Loc::getMessage('MOD_UNINST_SAVE_TABLES')?></label>
    </p>
    <input type="submit" value="<?=Loc::getMessage('MOD_UNINST_DEL')?>">
</form>