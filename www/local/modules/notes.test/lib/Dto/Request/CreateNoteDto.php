<?php

namespace Notes\Test\Dto\Request;

use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\Request;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Type\ParameterDictionary;


class CreateNoteDto
{
    public function __construct(
        public string $title,
        public string $content,
        public string $authorId,
        public DateTime $createdAt,
        public DateTime $updatedAt,
    )
    {
    }

    public static function createFromRequest(ParameterDictionary $jsonList): CreateNoteDto
    {
        $userID = CurrentUser::get()->getId();
        return new self(
            title: $jsonList->get('title'),
            content: $jsonList->get('content'),
            authorId: $userID,
            createdAt:  new DateTime(),
            updatedAt: new DateTime()
        );
    }

}
