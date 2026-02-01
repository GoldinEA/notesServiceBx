<?php

namespace Notes\Test\Orm;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\DatetimeField;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\StringField;
use Bitrix\Main\Entity\TextField;
use Bitrix\Main\Entity\Validator\Length;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;

/**
 * ORM сущность для заметок
 */
class NotesTable extends DataManager
{
    /**
     * Возвращает имя таблицы
     */
    public static function getTableName(): string
    {
        return 'notes';
    }

    /**
     * Возвращает карту полей
     * @throws SystemException
     */
    public static function getMap(): array
    {
        return [
            new IntegerField(
                'ID',
                [
                    'primary' => true,
                    'autocomplete' => true,
                ]
            ),
            new StringField(
                'TITLE',
                [
                    'required' => true,
                    'validation' => function () {
                        return [
                            new Length(1, 255),
                        ];
                    },
                ]
            ),
            new TextField(
                'CONTENT',
                [
                    'required' => true,
                ]
            ),
            new IntegerField(
                'AUTHOR_ID',
                [
                    'required' => true,
                ]
            ),
            new DatetimeField(
                'CREATED_AT',
                [
                    'default_value' => function () {
                        return new DateTime();
                    },
                ]
            ),
            new DatetimeField(
                'UPDATED_AT',
                [
                    'default_value' => function () {
                        return new DateTime();
                    },
                ]
            ),
            new Reference(
                'AUTHOR',
                UserTable::class,
                Join::on('this.AUTHOR_ID', 'ref.ID')
            ),
        ];
    }
}
