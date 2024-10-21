<template>
    <div>
        <h1>{{ isEditing ? 'Редактировать' : 'Добавить' }} вакансию</h1>
        <form @submit.prevent="submitForm">
            <input v-model="vacancy.title" placeholder="Название вакансии" required />
            <input v-model="vacancy.company" placeholder="Компания" required />
            <input v-model="vacancy.salary" placeholder="Зарплата" />
            <input v-model="vacancy.country" placeholder="Страна" required />
            <input v-model="vacancy.city" placeholder="Город" />
            <input type="date" v-model="vacancy.published_at" required />
            <button type="submit">{{ isEditing ? 'Сохранить' : 'Добавить' }}</button>
        </form>
    </div>
</template>

<script>
export default {
    props: {
        vacancyId: {
            type: Number,
            default: null,
        },
    },
    data() {
        return {
            isEditing: this.vacancyId !== null,
            vacancy: {
                title: '',
                company: '',
                salary: '',
                country: '',
                city: '',
                published_at: '',
            },
        };
    },
    mounted() {
        if (this.isEditing) {
            this.fetchVacancy();
        }
    },
    methods: {
        async fetchVacancy() {
            const response = await fetch(`/jobs/${this.vacancyId}`); // Обновлено для использования нового маршрута
            this.vacancy = await response.json();
        },
        async submitForm() {
            if (this.isEditing) {
                await fetch(`/jobs/${this.vacancyId}`, { // Обновлено для использования нового маршрута
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(this.vacancy),
                });
            } else {
                await fetch('/jobs', { // Обновлено для использования нового маршрута
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(this.vacancy),
                });
            }
            // Здесь можно добавить логику для перенаправления или отображения сообщения
        },
    },
};
</script>
