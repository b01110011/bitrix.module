<?php

use Bitrix\Main\Localization\Loc;

if (!check_bitrix_sessid()) return;

if ($ex = $APPLICATION->GetException())
{
    print CAdminMessage::ShowMessage(
    [
        'TYPE' => 'ERROR',
        'MESSAGE' => Log::getMessage('MOD_UNINST_ERR'),
        'DETAILS' => $ex->GetString(),
        'HTML' => true
    ]);
}
else
{
    CAdminMessage::ShowNote(Log::getMessage('MOD_UNINST_OK'));
}
?>

<form action="<?=$APPLICATION->GetCurPage()?>">
    <input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
    <input type="submit" value="<?=Log::getMessage('MOD_BACK')?>">
</form>