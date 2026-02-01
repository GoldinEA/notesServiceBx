<?php

namespace Notes\Test\Dto\Request;

use Bitrix\Main\HttpRequest;
use Bitrix\Main\Request;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Type\ParameterDictionary;


class UpdateNoteDto
{
    public function __construct(
        public int $id,
        public string $title,
        public string $content,
        public DateTime $updatedAt,
    )
    {
    }

    public static function createFromRequest(ParameterDictionary $json): UpdateNoteDto
    {
        return new self(
            id: $json->get('id'),
            title: $json->get('title'),
            content: $json->get('content'),
            updatedAt: new DateTime()
        );
    }

}
