<?php


require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
if (!$USER->IsAuthorized()) {
    $USER->Authorize(1);
}

/** @var CMain $APPLICATION */
$APPLICATION->IncludeComponent(
    'notes.test',
    '.default'
);
?>

<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
