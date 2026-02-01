<?php

namespace Notes\Test\Controllers;

use Bitrix\Main\Engine\ActionFilter\Authentication;
use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Validation\Engine\AutoWire\ValidationParameter;
use Notes\Test\Dto\Request\CreateNoteDto;
use Notes\Test\Dto\Request\UpdateNoteDto;
use Notes\Test\Services\NoteService;
use OpenApi\Attributes as OA;


#[OA\Info(title: 'Notes API', version: '1.0.0', description: 'API для заметок')]
class ApiController extends Controller
{

    public function getAutoWiredParameters(): array
    {
        $jsonList = $this
            ->getRequest()
            ->getJsonList();

        return [
            new ValidationParameter(
                CreateNoteDto::class,
                fn() => CreateNoteDto::createFromRequest($jsonList),
            ),
            new ValidationParameter(
                UpdateNoteDto::class,
                fn() => UpdateNoteDto::createFromRequest($jsonList),
            ),
        ];
    }


    public function configureActions(): array
    {
        return [
            'update' => [
                'prefilters' => [
                    new HttpMethod([
                        HttpMethod::METHOD_PATCH,
                    ]),
                    new Authentication(),
                ],
            ],
            'delete' => [
                'prefilters' => [
                    new HttpMethod([
                        HttpMethod::METHOD_DELETE,
                    ]),
                    new Authentication(),
                ]
            ],
            'add' => [
                'prefilters' => [
                    new HttpMethod([
                        HttpMethod::METHOD_POST,
                    ]),
                    new Authentication(),
                ]
            ],
            'get' => [
                'prefilters' => [
                    new HttpMethod([
                        HttpMethod::METHOD_GET,
                    ]),
                    new Authentication(),
                ]
            ],
            'list' => [
                'prefilters' => [
                    new HttpMethod([
                        HttpMethod::METHOD_GET,
                    ]),
                    new Authentication(),
                ]
            ],
        ];
    }

    #[OA\Post(
        path: '/api/v1/notes',
        operationId: 'createNote',
        summary: 'Создать новую заметку',
        tags: ['notes'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', type: 'string'),
                    new OA\Property(property: 'content', type: 'string'),
                ],
                type: 'object',
            ),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Заметка успешно создана',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer'),
                                new OA\Property(property: 'success', type: 'boolean'),
                            ],
                            type: 'object',
                        ),
                        new OA\Property(
                            property: 'errors',
                            type: 'array',
                            items: new OA\Items(type: 'string'),
                        ),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    public function addAction(CreateNoteDto $createNoteDto, NoteService $noteService): ?array
    {
        return $noteService
            ->create($createNoteDto)
            ->getData() ?: null;
    }


    #[OA\Get(
        path: '/api/v1/notes',
        operationId: 'getNotesList',
        summary: 'Получить список заметок',
        tags: ['notes'],
        parameters: [
            new OA\Parameter(
                name: 'page',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 1),
            ),
            new OA\Parameter(
                name: 'pageSize',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 5),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список заметок',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'notes',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'ID', type: 'string'),
                                    new OA\Property(property: 'TITLE', type: 'string'),
                                    new OA\Property(property: 'CONTENT', type: 'string'),
                                    new OA\Property(property: 'AUTHOR_ID', type: 'string'),
                                    new OA\Property(property: 'CREATED_AT', type: 'string', format: 'date-time'),
                                    new OA\Property(property: 'UPDATED_AT', type: 'string', format: 'date-time'),
                                ],
                                type: 'object',
                            ),
                        ),
                        new OA\Property(property: 'total', type: 'integer'),
                        new OA\Property(property: 'page', type: 'integer'),
                        new OA\Property(property: 'pageSize', type: 'integer'),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    public function listAction(NoteService $noteService, int $page = 1, int $pageSize = 5): ?array
    {
        return $noteService
            ->getAll($page, $pageSize)
            ->getData() ?: null;
    }


    #[OA\Get(
        path: '/api/v1/notes',
        operationId: 'getNoteById',
        summary: 'Получить заметку по ID',
        tags: ['notes'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Заметка найдена',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(
                                    property: 'note',
                                    properties: [
                                        new OA\Property(property: 'ID', type: 'string'),
                                        new OA\Property(property: 'TITLE', type: 'string'),
                                        new OA\Property(property: 'CONTENT', type: 'string'),
                                        new OA\Property(property: 'AUTHOR_ID', type: 'string'),
                                        new OA\Property(property: 'CREATED_AT', type: 'string', format: 'date-time'),
                                        new OA\Property(property: 'UPDATED_AT', type: 'string', format: 'date-time'),
                                    ],
                                    type: 'object',
                                ),
                            ],
                            type: 'object',
                        ),
                        new OA\Property(
                            property: 'errors',
                            type: 'array',
                            items: new OA\Items(type: 'string'),
                        ),
                    ],
                    type: 'object',
                ),
            ),
            new OA\Response(
                response: 404,
                description: 'Заметка не найдена',
            ),
        ],
    )]
    public function getAction(int $id, NoteService $noteService): ?array
    {
        return $noteService
            ->getById($id)
            ->getData() ?: null;
    }


    #[OA\Patch(
        path: '/api/v1/notes',
        operationId: 'updateNote',
        summary: 'Обновить заметку',
        tags: ['notes'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'id', type: 'integer'),
                    new OA\Property(property: 'title', type: 'string'),
                    new OA\Property(property: 'content', type: 'string'),
                ],
                type: 'object',
            ),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Заметка успешно обновлена',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer'),
                                new OA\Property(property: 'success', type: 'boolean'),
                                new OA\Property(property: 'message', type: 'string'),
                            ],
                            type: 'object',
                        ),
                        new OA\Property(
                            property: 'errors',
                            type: 'array',
                            items: new OA\Items(type: 'string'),
                        ),
                    ],
                    type: 'object',
                ),
            ),
            new OA\Response(
                response: 404,
                description: 'Заметка не найдена',
            ),
        ],
    )]
    public function updateAction(UpdateNoteDto $updateNoteDto, NoteService $noteService): ?array
    {
        return $noteService
            ->update($updateNoteDto)
            ->getData() ?: null;
    }


    #[OA\Delete(
        path: '/api/v1/notes',
        operationId: 'deleteNote',
        summary: 'Удалить заметку',
        tags: ['notes'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Заметка успешно удалена',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer'),
                                new OA\Property(property: 'success', type: 'boolean'),
                                new OA\Property(property: 'message', type: 'string'),
                            ],
                            type: 'object',
                        ),
                        new OA\Property(
                            property: 'errors',
                            type: 'array',
                            items: new OA\Items(type: 'string'),
                        ),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    public function deleteAction(int $id, NoteService $noteService): ?array
    {
        return $noteService
            ->delete($id)
            ->getData() ?: null;
    }
}