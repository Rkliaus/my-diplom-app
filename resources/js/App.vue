<template>
    <div>
        <div class="filter-container">
            <input type="text" placeholder="Поиск по названию вакансии" v-model="searchTitle" />
            <input type="text" placeholder="Город" v-model="searchCity" />
            <input type="text" placeholder="Компания" v-model="searchCompany" />
            <input type="number" placeholder="Минимальная зарплата" v-model="searchSalaryMin" />
            <input type="number" placeholder="Максимальная зарплата" v-model="searchSalaryMax" />
        </div>

        <div class="vacancy-list">
            <div v-for="vacancy in paginatedVacancies" :key="vacancy.hh_id" class="vacancy-card"
                @click="openModal(vacancy)">
                <h2>{{ vacancy.title }}</h2>
                <p><strong>Компания:</strong> {{ vacancy.company }}</p>
                <p><strong>Зарплата:</strong> {{ vacancy.salary }}</p>
                <p><strong>Город:</strong> {{ vacancy.country }}</p>
                <p><strong>Опубликовано:</strong> {{ vacancy.published_at }}</p>
            </div>
        </div>

        <!-- Пагинация -->
        <div class="pagination">
            <button @click="prevPage" :disabled="currentPage === 1">Назад</button>
            <span>Страница {{ currentPage }} из {{ totalPages }}</span>
            <button @click="nextPage" :disabled="currentPage === totalPages">Вперед</button>
        </div>

        <!-- Модальное окно -->
        <div v-if="isModalOpen" class="modal-overlay">
            <div class="modal">
                <h2>{{ selectedVacancy.title }}</h2>
                <p><strong>Компания:</strong> {{ selectedVacancy.company }}</p>
                <p><strong>Зарплата:</strong> {{ selectedVacancy.salary }}</p>
                <p><strong>Город:</strong> {{ selectedVacancy.country }}</p>
                <p><strong>Опубликовано:</strong> {{ selectedVacancy.published_at }}</p>

                <button v-if="isUserAuthenticated" class="apply-button">Откликнуться</button>
                <Link v-else :href="route('register')" class="register-button">Зарегистрироваться</Link>
                <button @click="closeModal" class="close-button">Закрыть</button>
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
            searchSalaryMax: '',
            selectedVacancy: null,
            isModalOpen: false,
            currentPage: 1,
            itemsPerPage: 30,
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
                const salaryMax = this.searchSalaryMax ? parseInt(this.searchSalaryMax) : Infinity;

                return (
                    vacancy.title.toLowerCase().includes(this.searchTitle.toLowerCase()) &&
                    vacancy.city.toLowerCase().includes(this.searchCity.toLowerCase()) &&
                    vacancy.company.toLowerCase().includes(this.searchCompany.toLowerCase()) &&
                    salary[0] >= salaryMin &&
                    salary[1] <= salaryMax
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
    /* Указатель курсора при наведении на карточку */
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

.apply-button {
    background-color: green;
    /* Зеленый цвет кнопки */
    color: white;
    /* Цвет текста кнопки */
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-right: 10px;
    /* Отступ справа от кнопки "Откликнуться" */
}

.apply-button:hover {
    background-color: darkgreen;
    /* Темно-зеленый при наведении */
}

.close-button {
    background-color: red;
    /* Красный цвет кнопки "Закрыть" */
    color: white;
    /* Цвет текста кнопки */
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-left: 10px;
    /* Добавляем отступ слева */
}

.close-button:hover {
    background-color: darkred;
    /* Темно-красный при наведении */
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
</style>
