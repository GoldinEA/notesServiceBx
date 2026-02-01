BX.ready(function () {
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                notes: [],
                loading: false,
                isSaving: false,
                isEditing: false,
                editingNoteId: null,
                form: {
                    title: '',
                    content: '',
                },
                errors: [],
                successMessage: '',
                currentPage: 1,
                pageSize: 5,
                totalItems: 0,
                currentRequestId: 0,
                confirmDelete: {
                    visible: false,
                    noteId: null,
                    inProgress: false
                }
            };
        },

        computed: {
            totalPages() {
                return Math.ceil(this.totalItems / this.pageSize);
            },
            isValidForm() {
                return this.form.title?.trim() &&
                    this.form.content?.trim() &&
                    this.form.title.length <= 255;
            }
        },

        methods: {
            getApiHeaders(contentType = 'application/json') {
                return {
                    'Content-Type': contentType,
                    'X-Bitrix-Csrf-Token': BX.bitrix_sessid(),
                    'X-Requested-With': 'XMLHttpRequest'
                };
            },

            validateForm() {
                this.errors = [];

                const validations = [
                    {
                        check: !this.form.title?.trim(),
                        message: 'Заголовок обязателен'
                    },
                    {
                        check: !this.form.content?.trim(),
                        message: 'Содержание обязательно'
                    },
                    {
                        check: this.form.title.length > 255,
                        message: 'Заголовок не должен превышать 255 символов'
                    },
                    {
                        check: this.form.content.length > 10000,
                        message: 'Содержание не должно превышать 10000 символов'
                    }
                ];

                validations.forEach(({ check, message }) => {
                    if (check) this.errors.push(message);
                });

                return this.errors.length === 0;
            },

            handleApiError(error, context = '') {
                console.error(`API Error (${context}):`, error);

                let message = 'Произошла ошибка. Попробуйте позже.';

                if (error.name === 'TypeError') {
                    message = 'Ошибка сети. Проверьте соединение.';
                } else if (error.message.includes('HTTP')) {
                    message = `Серверная ошибка: ${error.message}`;
                } else if (error.message) {
                    message = error.message;
                }

                this.errors = [message];
            },

            async apiRequest(url, options = {}) {
                const { method = 'GET', body, headers = {}, ...rest } = options;
                const finalHeaders = {
                    ...this.getApiHeaders(),
                    ...headers
                };

                const finalOptions = {
                    method,
                    headers: finalHeaders,
                    ...(body && { body }),
                    ...rest
                };

                const response = await fetch(url, finalOptions);

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const result = await response.json();

                if (result.status !== 'success') {
                    const errors = result.errors || result.message || 'Неизвестная ошибка API';
                    throw new Error(Array.isArray(errors) ? errors.join(', ') : errors);
                }

                return result.data;
            },

            async loadNotes() {
                this.loading = true;
                this.errors = [];
                const currentRequestId = ++this.currentRequestId;

                try {
                    const data = await this.apiRequest(
                        `/api/v1/notes/list?page=${this.currentPage}&pageSize=${this.pageSize}`
                    );

                    if (currentRequestId !== this.currentRequestId) {
                        console.log('Игнорируем устаревший ответ');
                        return;
                    }

                    this.notes = data.notes || [];
                    this.totalItems = data.total || 0;

                } catch (error) {
                    this.handleApiError(error, 'loadNotes');
                } finally {
                    this.loading = false;
                }
            },

            goToPage(page) {
                if (page >= 1 && page <= this.totalPages) {
                    this.currentPage = page;
                    this.loadNotes();
                }
            },

            previousPage() {
                if (this.currentPage > 1) {
                    this.currentPage--;
                    this.loadNotes();
                }
            },

            nextPage() {
                if (this.currentPage < this.totalPages) {
                    this.currentPage++;
                    this.loadNotes();
                }
            },

            async saveNote() {
                if (this.isSaving || !this.validateForm()) return;

                this.isSaving = true;
                this.successMessage = '';
                this.errors = [];

                try {
                    const url = this.isEditing ? '/api/v1/notes/update' : '/api/v1/notes/add';
                    const method = this.isEditing ? 'PATCH' : 'POST';

                    const payload = {
                        ...(this.isEditing && { id: this.editingNoteId }),
                        title: this.form.title.trim(),
                        content: this.form.content.trim()
                    };

                    await this.apiRequest(url, {
                        method,
                        body: JSON.stringify(payload)
                    });

                    this.successMessage = this.isEditing
                        ? 'Заметка успешно обновлена'
                        : 'Заметка успешно создана';

                    if (this.isEditing) {
                        this.cancelEdit();
                    } else {
                        this.resetForm();
                    }

                    await this.loadNotes();

                    // ✅ Автоочистка успеха
                    setTimeout(() => {
                        this.successMessage = '';
                    }, 3000);

                } catch (error) {
                    this.handleApiError(error, 'saveNote');
                } finally {
                    this.isSaving = false;
                }
            },

            async editNote(note) {
                this.loading = true;
                this.errors = [];

                try {
                    const result = await this.apiRequest(`/api/v1/notes/get?id=${note.ID}`);
                    const data = result.note
                    this.isEditing = true;
                    this.form.title = data.TITLE || '';
                    this.form.content = data.CONTENT || '';
                    this.editingNoteId = data.ID

                    window.scrollTo({ top: 0, behavior: 'smooth' });

                } catch (error) {
                    this.handleApiError(error, 'editNote');
                    this.isEditing = false;
                } finally {
                    this.loading = false;
                }
            },

            cancelEdit() {
                this.isEditing = false;
                this.editingNoteId = null;
                this.resetForm();
            },

            resetForm() {
                this.form = { title: '', content: '' };
                this.errors = [];
            },

            // ✅ Удаление с модальным подтверждением
            openDeleteConfirm(noteId) {
                this.confirmDelete = {
                    visible: true,
                    noteId,
                    inProgress: false
                };
            },

            async confirmDeleteNote() {
                if (this.confirmDelete.inProgress) return;

                this.confirmDelete.inProgress = true;
                try {
                    await this.apiRequest(`/api/v1/notes/delete?id=${this.confirmDelete.noteId}`, {
                        method: 'DELETE'
                    });

                    this.confirmDelete.visible = false;
                    await this.loadNotes();

                } catch (error) {
                    this.handleApiError(error, 'deleteNote');
                } finally {
                    this.confirmDelete.inProgress = false;
                }
            },

            formatDate(dateString) {
                if (!dateString) return '';
                return new Date(dateString).toLocaleString('ru-RU');
            }
        },

        async mounted() {
            await this.loadNotes();
        }
    }).mount('#app');
});
