<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Notes\Test\Orm\NotesTable;

Loc::loadMessages(__FILE__);

class notes_test extends CModule
{
    public $MODULE_ID = 'notes.test';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_GROUP_RIGHTS = 'N';

    public function __construct()
    {
        $arModuleVersion = [];
        include __DIR__ . '/version.php';

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];

        $this->MODULE_NAME = 'Notes - Сервис заметок';
        $this->MODULE_DESCRIPTION = '';
    }

    /**
     * Установка модуля
     */
    public function doInstall()
    {
        global $APPLICATION;
        ModuleManager::registerModule($this->MODULE_ID);
        $this->registerAutoload();
        $this->createTable();

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage('LOCAL_NOTES_INSTALL_TITLE'),
            __DIR__ . '/step.php'
        );
    }

    /**
     * Удаление модуля
     */
    public function doUninstall()
    {
        global $APPLICATION;
        $this->dropTable();
        $this->unregisterAutoload();
        ModuleManager::unRegisterModule($this->MODULE_ID);

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage('LOCAL_NOTES_UNINSTALL_TITLE'),
            __DIR__ . '/unstep.php'
        );
    }

    /**
     * Создание таблицы для заметок
     */
    private function createTable()
    {
//        $connection = Application::getConnection();
//        $tableName = NotesTable::getTableName();
//        if (!$connection->isTableExists($tableName)) {
//            NotesTable::getEntity()->createDbTable();
//        }
        global $DB;

        $tableName = 'notes';

        $result = $DB->Query("SHOW TABLES LIKE '{$tableName}'");

        if (!$result->Fetch()) {
            $DB->Query("
                CREATE TABLE {$tableName} (
                    ID INT NOT NULL AUTO_INCREMENT,
                    TITLE VARCHAR(255) NOT NULL,
                    CONTENT TEXT NOT NULL,
                    AUTHOR_ID INT NOT NULL,
                    CREATED_AT DATETIME NOT NULL,
                    UPDATED_AT DATETIME NOT NULL,
                    PRIMARY KEY (ID),
                    INDEX IDX_AUTHOR_ID (AUTHOR_ID)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }
    }

    /**
     * Удаление таблицы
     */
    private function dropTable()
    {
//        $tableName = NotesTable::getTableName();
//        $connection = Application::getConnection();
//        if ($connection->isTableExists($tableName)) {
//            $connection->dropTable($tableName);
//        }
        global $DB;

        $DB->Query('DROP TABLE IF EXISTS b_notes');
    }

    /**
     * Регистрация автозагрузки классов
     */
    private function registerAutoload()
    {
        $documentRoot = $_SERVER['DOCUMENT_ROOT'];
        $modulePath = '/local/notes/lib';

        $arAutoload = [];

        // Читаем текущий файл автозагрузки
        $autoloadFile = $documentRoot . '/bitrix/modules/main/tools.php';
        if (file_exists($autoloadFile)) {
            // Добавляем в автозагрузку модуля
            CModule::AddAutoloadClasses(
                $this->MODULE_ID,
                [
                    '\\Notes\\Test\\Lib\\NotesTable' => 'lib/NotesTable.php',
                    '\\Notes\\Test\\Services\\NoteService' => 'lib/Services/NoteService.php',
                    '\\Notes\\Test\\Controllers\\ApiController' => 'lib/Controllers/ApiController.php',
                ]
            );
        }
    }

    /**
     * Удаление автозагрузки классов
     */
    private function unregisterAutoload()
    {
        // Классы автоматически удаляются из автозагрузки при удалении модуля
        // Bitrix сам обрабатывает это при вызове UnRegisterModule
    }
}
