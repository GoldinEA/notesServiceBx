<?php

use Bitrix\Main\Loader;

Loader::includeModule('notes.test');


CJSCore::RegisterExt(
    'vue',
    [
        'js' => 'https://unpkg.com/vue@3/dist/vue.global.js',
        'skip_core' => true,
    ]
);


