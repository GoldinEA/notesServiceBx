<?php

use Bitrix\Main\UI\Extension;

B_PROLOG_INCLUDED === true || die();
/** @var array $arParams */
/** @var array $arResult */
/** @var CBitrixComponent $component */
Extension::load(['vue']);
$APPLICATION->SetTitle('Сервис заметок');
?>
<div id="app" class="container">

    <div class="form">
        <h2 class="form-title">{{ isEditing ? 'Редактировать заметку' : 'Создать заметку' }}</h2>

        <div class="form-group">
            <label for="title">Заголовок *</label>
            <input
                type="text"
                id="title"
                v-model="form.title"
                placeholder="Введите заголовок заметки"
                maxlength="255"
                :disabled="isSaving"
            >
        </div>

        <div class="form-group">
            <label for="content">Содержание *</label>
            <textarea
                id="content"
                v-model="form.content"
                placeholder="Введите содержание заметки"
                :disabled="isSaving"
            ></textarea>
        </div>

        <div v-if="errors.length > 0" class="error-message">
            <div v-for="(error, index) in errors" :key="index" class="error-item">{{ error }}</div>
        </div>

        <div v-if="successMessage" class="success-message">
            {{ successMessage }}
        </div>

        <div style="margin-top: 15px;">
            <button v-if="isEditing" class="btn btn-secondary" @click="cancelEdit" :disabled="isSaving">
                Отмена
            </button>
            <button
                class="btn btn-primary"
                @click="saveNote"
                :disabled="isSaving || !isValidForm"
            >
                {{ isSaving ? 'Сохраняю...' : (isEditing ? 'Сохранить изменения' : 'Создать заметку') }}
            </button>
        </div>
    </div>

    <div v-if="confirmDelete.visible" class="modal-overlay" @click.self="confirmDelete.visible = false">
        <div class="modal">
            <h3>Подтверждение удаления</h3>
            <p>Вы уверены, что хотите удалить эту заметку? Действие нельзя отменить.</p>
            <div class="modal-actions">
                <button
                    class="btn btn-secondary"
                    @click="confirmDelete.visible = false"
                    :disabled="confirmDelete.inProgress"
                >
                    Отмена
                </button>
                <button
                    class="btn btn-danger"
                    @click="confirmDeleteNote"
                    :disabled="confirmDelete.inProgress"
                >
                    {{ confirmDelete.inProgress ? 'Удаляю...' : 'Да, удалить' }}
                </button>
            </div>
        </div>
    </div>

    <div v-if="loading" class="loading">
        Загрузка...
    </div>
    <div v-else-if="notes.length === 0" class="empty">
        Заметок нет. Создайте первую!
    </div>
    <div v-else>
        <div class="notes-list">
            <div v-for="note in notes" :key="note.ID" class="note-card">
                <div class="note-title">{{ note.TITLE }}</div>
                <div class="note-content">{{ note.CONTENT }}</div>
                <div class="note-meta">
                    <span>Автор: {{ note.AUTHOR_ID }} | Создано: {{ formatDate(note.CREATED_AT) }}</span>
                    <div class="note-actions">
                        <button
                            class="btn btn-edit"
                            @click="editNote(note)"
                            :disabled="loading"
                        >
                            Редактировать
                        </button>
                        <button
                            class="btn btn-danger"
                            @click="openDeleteConfirm(note.ID)"
                            :disabled="loading"
                        >
                            Удалить
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="totalPages > 1" class="pagination">
            <button
                class="btn btn-secondary"
                :disabled="currentPage === 1 || loading"
                @click="previousPage"
            >
                ← Предыдущая
            </button>

            <span class="pagination-info">
                Страница {{ currentPage }} из {{ totalPages }}
                <small>(Всего: {{ totalItems }} заметок)</small>
            </span>

            <button
                class="btn btn-secondary"
                :disabled="currentPage === totalPages || loading"
                @click="nextPage"
            >
                Следующая →
            </button>
        </div>
    </div>
</div>

