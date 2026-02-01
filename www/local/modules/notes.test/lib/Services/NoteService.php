<?php

namespace Notes\Test\Services;

use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Notes\Test\Dto\Request\UpdateNoteDto;
use Notes\Test\Dto\Request\CreateNoteDto;
use Notes\Test\Orm\NotesTable;

/**
 * Сервис для работы с заметками
 */
class NoteService
{


    /**
     * Получить заметки с пагинацией
     */
    public function getAll(int $page = 1, int $pageSize = 5): Result
    {
        $result = new Result();

        try {
            $authorId = CurrentUser::get()->getId();
            $offset = ($page - 1) * $pageSize;

            $notes = NotesTable::getList([
                'select' => ['*'],
                'order' => ['CREATED_AT' => 'DESC'],
                'filter' => ['=AUTHOR.ID' => $authorId],
                'limit' => $pageSize,
                'offset' => $offset
            ])->fetchAll();

            $totalCount = NotesTable::getCount([
                '=AUTHOR.ID' => $authorId
            ]);

            $result->setData([
                'notes' => $notes,
                'total' => $totalCount,
                'page' => $page,
                'pageSize' => $pageSize,
            ]);
        } catch (\Exception $e) {
            $result->addError(new Error('Ошибка при получении списка заметок: ' . $e->getMessage(), 'GET_ALL_ERROR'));
        }

        return $result;
    }

    /**
     * Получить заметку по ID
     */
    public function getById(int $id): Result
    {
        $result = new Result();

        try {
            $note = NotesTable::getByPrimary($id, [
                'select' => ['*'],
            ])->fetch();

            if (!$note) {
                $result->addError(new Error('Заметка не найдена', 'NOTE_NOT_FOUND'));
                return $result;
            }

            $result->setData([
                'note' => $note,
            ]);
        } catch (\Exception $e) {
            $result->addError(new Error('Ошибка при получении заметки: ' . $e->getMessage(), 'GET_BY_ID_ERROR'));
        }

        return $result;
    }

    /**
     * Создать заметку
     */
    public function create(CreateNoteDto $createNoteDto): Result
    {
        $result = new Result();

        try {
            $fields = [
                'TITLE' => $createNoteDto->title,
                'CONTENT' => $createNoteDto->content,
                'AUTHOR_ID' => $createNoteDto->authorId,
                'CREATED_AT' => $createNoteDto->createdAt,
                'UPDATED_AT' => $createNoteDto->updatedAt,
            ];

            $addResult = NotesTable::add($fields);

            if (!$addResult->isSuccess()) {
                $this->setErrors($addResult, $result);
                return $result;
            }

            $result->setData([
                'id' => $addResult->getId(),
                'success' => true,
            ]);
        } catch (\Exception $e) {
            $result->addError(new Error('Ошибка при создании заметки: ' . $e->getMessage(), 'CREATE_ERROR'));
        }

        return $result;
    }

    /**
     * Обновить заметку
     */
    public function update(UpdateNoteDto $updateNodeDto): Result
    {
        $result = new Result();

        try {
            $updateData = [
                'TITLE' => $updateNodeDto->title,
                'CONTENT' => $updateNodeDto->content,
            ];

            $updateResult = NotesTable::update($updateNodeDto->id, $updateData);

            if (!$updateResult->isSuccess()) {
                $this->setErrors($updateResult, $result);
                return $result;
            }

            $result->setData([
                'id' => $updateNodeDto->id,
                'success' => true,
                'message' => 'Заметка успешно обновлена',
            ]);
        } catch (\Exception $e) {
            $result->addError(new Error('Ошибка при обновлении заметки: ' . $e->getMessage(), 'UPDATE_ERROR'));
        }

        return $result;
    }

    /**
     * Удалить заметку
     */
    public function delete(int $id): Result
    {
        $result = new Result();

        try {
            $deleteResult = NotesTable::delete($id);

            if (!$deleteResult->isSuccess()) {
                $this->setErrors($deleteResult, $result);
                return $result;
            }

            $result->setData([
                'id' => $id,
                'success' => true,
                'message' => 'Заметка успешно удалена',
            ]);
        } catch (\Exception $e) {
            $result->addError(new Error('Ошибка при удалении заметки: ' . $e->getMessage(), 'DELETE_ERROR'));
        }

        return $result;
    }

    private function setErrors(Result $source, Result $target): void
    {
        foreach ($source->getErrors() as $error) {
            $target->addError($error);
        }
    }
}
