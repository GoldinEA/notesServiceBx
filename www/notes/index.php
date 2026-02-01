<?php


require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
global $USER;
if (!$USER->IsAuthorized()) {
    LocalRedirect('/');
}

/** @var CMain $APPLICATION */
$APPLICATION->IncludeComponent(
    'notes.test',
    '.default'
);
?>

<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
