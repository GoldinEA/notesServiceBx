<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

global $APPLICATION;

$APPLICATION->SetTitle(Loc::getMessage('LOCAL_NOTES_UNINSTALL_TITLE'));

CAdminMessage::ShowNote(Loc::getMessage('LOCAL_NOTES_UNINSTALL_SUCCESS'));
?>

<form action="<?= $APPLICATION->GetCurPage() ?>" method="post">
    <?= bitrix_sessid_post() ?>
    <input type="hidden" name="lang" value="<?= LANG ?>">
    <input type="hidden" name="id" value="local.notes">
    <input type="hidden" name="uninstall" value="Y">
    <input type="hidden" name="step" value="2">
    
    <input type="submit" name="" value="<?= Loc::getMessage('LOCAL_NOTES_UNINSTALL_BACK') ?>">
</form>