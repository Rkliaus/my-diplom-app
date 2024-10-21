<template>
    <div>
        <div class="filter-container">
            <input type="text" placeholder="Поиск по названию вакансии" v-model="searchTitle" />
            <input type="text" placeholder="Город" v-model="searchCity" />
            <input type="text" placeholder="Компания" v-model="searchCompany" />
            <input type="number" placeholder="Минимальная зарплата" v-model="searchSalaryMin" class="no-arrows" />
        </div>

        <div class="vacancy-list">
            <div v-for="vacancy in paginatedVacancies" :key="vacancy.hh_id" class="vacancy-card" @click="openModal(vacancy)">
                <h2>{{ vacancy.title }}</h2>
                <p><strong>Компания:</strong> {{ vacancy.company }}</p>
                <p><strong>Зарплата:</strong> {{ vacancy.salary }}</p>
                <p><strong>Город:</strong> {{ vacancy.city }}</p>
                <p><strong>Опубликовано:</strong> {{ vacancy.published_at }}</p>
            </div>
        </div>

        <!-- Пагинация -->
        <div class="pagination">
            <button @click="prevPage" :disabled="currentPage === 1">Назад</button>
            <span>Страница {{ currentPage }} из {{ totalPages }}</span>
            <button @click="nextPage" :disabled="currentPage === totalPages">Вперед</button>
        </div>

        <!-- Модальное окно для просмотра вакансии -->
        <div v-if="isModalOpen" class="modal-overlay">
            <div class="modal">
                <h2>{{ selectedVacancy.title }}</h2>
                <p><strong>Компания:</strong> {{ selectedVacancy.company }}</p>
                <p><strong>Зарплата:</strong> {{ selectedVacancy.salary }}</p>
                <p><strong>Город:</strong> {{ selectedVacancy.city }}</p>
                <p><strong>Опубликовано:</strong> {{ selectedVacancy.published_at }}</p>
                <p><strong>ID:</strong> {{ selectedVacancy.id }}</p> <!-- Добавляем ID вакансии -->

                <button v-if="isUserAuthenticated" @click="deleteVacancy(selectedVacancy.id)" class="delete-button">Удалить вакансию</button> <!-- Кнопка удаления -->
                <button v-if="isUserAuthenticated" @click="openEditModal(selectedVacancy)" class="edit-button">Редактировать вакансию</button> <!-- Кнопка редактирования -->
                <button v-if="isUserAuthenticated" class="apply-button">Откликнуться</button>
                <Link v-else :href="route('register')" class="register-button">Зарегистрироваться</Link>
                <button @click="closeModal" class="close-button">Закрыть</button>
            </div>
        </div>

        <!-- Модальное окно для редактирования вакансии -->
        <div v-if="isEditModalOpen" class="modal-overlay">
            <div class="modal">
                <h2>Редактировать вакансию</h2>
                <form @submit.prevent="saveVacancy">
                    <label>Название:</label>
                    <input type="text" v-model="editVacancy.title" required>

                    <label>Компания:</label>
                    <input type="text" v-model="editVacancy.company" required>

                    <label>Зарплата от:</label>
                    <input type="number" v-model="editVacancy.salary_min" required>

                    <label>Зарплата до:</label>
                    <input type="number" v-model="editVacancy.salary_max" required>

                    <label>Город:</label>
                    <input type="text" v-model="editVacancy.city" required>

                    <!-- Убираем редактирование поля даты публикации -->
                    <button type="submit" class="save-button">Сохранить изменения</button>
                    <button @click="closeEditModal" class="close-button">Отмена</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { usePage, Link } from '@inertiajs/vue3';

export default {
    components: {
        Link,
    },
    setup() {
        const page = usePage();
        return { page };
    },
    data() {
        return {
            vacancies: [],
            searchTitle: '',
            searchCity: '',
            searchCompany: '',
            searchSalaryMin: '',
            selectedVacancy: null,
            isModalOpen: false,
            currentPage: 1,
            itemsPerPage: 30,
            editVacancy: null, // Данные для редактируемой вакансии
            isEditModalOpen: false, // Управление состоянием модального окна редактирования
        };
    },
    computed: {
        isUserAuthenticated() {
            return this.page.props.auth.user !== null;
        },
        filteredVacancies() {
            return this.vacancies.filter(vacancy => {
                const salary = vacancy.salary ? vacancy.salary.split(' - ').map(s => parseInt(s)) : [0, Infinity];
                const salaryMin = this.searchSalaryMin ? parseInt(this.searchSalaryMin) : 0;

                return (
                    vacancy.title.toLowerCase().includes(this.searchTitle.toLowerCase()) &&
                    vacancy.city.toLowerCase().includes(this.searchCity.toLowerCase()) &&
                    vacancy.company.toLowerCase().includes(this.searchCompany.toLowerCase()) &&
                    salary[0] >= salaryMin
                );
            });
        },
        totalPages() {
            return Math.ceil(this.filteredVacancies.length / this.itemsPerPage);
        },
        paginatedVacancies() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            return this.filteredVacancies.slice(start, start + this.itemsPerPage);
        },
    },
    mounted() {
        this.fetchVacancies();
    },
    methods: {
        fetchVacancies() {
            axios.get('/headhunter/vacancies')
                .then(response => {
                    this.vacancies = response.data;
                })
                .catch(error => {
                    console.error('Ошибка при получении данных:', error);
                });
        },
        openModal(vacancy) {
            this.selectedVacancy = vacancy; // Устанавливаем выбранную вакансию
            this.isModalOpen = true; // Открываем модальное окно
        },
        closeModal() {
            this.isModalOpen = false; // Закрываем модальное окно
            this.selectedVacancy = null; // Сбрасываем выбранную вакансию
        },
        openEditModal(vacancy) {
            this.editVacancy = {
                ...vacancy,
                salary_min: parseInt(vacancy.salary.split(' - ')[0]),
                salary_max: parseInt(vacancy.salary.split(' - ')[1])
            }; // заполняем данные для редактирования
            this.isEditModalOpen = true;
        },
        closeEditModal() {
            this.isEditModalOpen = false;
            this.editVacancy = null; // Сбрасываем данные редактируемой вакансии
        },
        saveVacancy() {
    // Установка текущей даты публикации в формате ISO
    this.editVacancy.published_at = new Date().toISOString().slice(0, 19); // Убираем миллисекунды

    // Объединение зарплаты в строку формата "от - до"
    this.editVacancy.salary = `${this.editVacancy.salary_min} - ${this.editVacancy.salary_max}`;

    // Отправка данных на сервер с обновленной датой
    axios.put(`/jobs/${this.editVacancy.id}`, this.editVacancy)
        .then(response => {
            this.fetchVacancies(); // Обновляем список вакансий
            this.closeEditModal(); // Закрываем модальное окно
        })
        .catch(error => {
            console.error('Ошибка при обновлении вакансии:', error);
        });
},

        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
            }
        },
        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },
        deleteVacancy(id) {
            if (confirm('Вы уверены, что хотите удалить эту вакансию?')) {
                axios.delete(`/jobs/${id}`)
                    .then(response => {
                        this.fetchVacancies(); // Обновляем список вакансий
                        this.closeModal(); // Закрываем модальное окно
                    })
                    .catch(error => {
                        console.error('Ошибка при удалении вакансии:', error);
                    });
            }
        },
    },
};
</script>


<style scoped>
.filter-container {
    margin-bottom: 20px;
}

.filter-container input {
    margin-right: 10px;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

/* Убираем стрелочки для input с типом number */
.no-arrows {
    -moz-appearance: textfield; /* Для Firefox */
}

.no-arrows::-webkit-outer-spin-button,
.no-arrows::-webkit-inner-spin-button {
    -webkit-appearance: none; /* Для Chrome, Safari, Edge */
}

/* Остальные стили остаются без изменений */
.vacancy-list {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.vacancy-card {
    background-color: #f9f9f9;
    border: 1px solid #ccc;
    border-radius: 8px;
    padding: 16px;
    width: calc(33% - 20px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
    cursor: pointer;
}

.vacancy-card:hover {
    transform: translateY(-5px);
}

/* Стили для модального окна */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    width: 300px;
}

/* Остальные стили для кнопок и пагинации */
.apply-button {
    background-color: green;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-right: 10px;
}

.apply-button:hover {
    background-color: darkgreen;
}

.close-button {
    background-color: red;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-left: 10px;
}

.close-button:hover {
    background-color: darkred;
}

.register-button {
    background-color: blue;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-right: 10px;
    text-decoration: none;
    display: inline-block;
}

.register-button:hover {
    background-color: darkblue;
}

.pagination {
    margin-top: 20px;
    display: flex;
    justify-content: center;
    gap: 10px;
}

.pagination button {
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    background-color: #007bff;
    color: white;
    cursor: pointer;
}

.pagination button:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}

.pagination span {
    align-self: center;
}

.dashboard-button {
    display: inline-block;
    padding: 10px 15px;
    background-color: #4CAF50;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    margin-bottom: 20px;
    transition: background-color 0.3s;
}

.dashboard-button:hover {
    background-color: #45a049;
}

.delete-button {
    background-color: red;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-right: 10px;
}

.delete-button:hover {
    background-color: darkred;
}

.edit-button {
    background-color: #f0ad4e;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-right: 10px;
}

.edit-button:hover {
    background-color: #ec971f;
}

</style>
