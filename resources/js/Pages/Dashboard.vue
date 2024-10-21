<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

// Создание формы с полями для вакансий
const form = useForm({
    title: '',
    company: '',
    salary_min: '',
    salary_max: '',
    city: '',
    published_at: '',
});

// Функция для отправки данных через POST-запрос
function createJob() {
    // Установка текущей даты и времени в формате ISO
    form.published_at = new Date().toISOString().slice(0, 19); // Убираем миллисекунды

    form.post(route('jobs.store'), {
        onSuccess: () => {
            console.log('Vacancy created successfully');
        },
        onError: (errors) => {
            console.error('Error creating vacancy:', errors);
        },
    });
}
</script>

<template>
    <Head title="Create Vacancy" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Create New Vacancy
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="createJob">
                            <!-- Поле для названия вакансии -->
                            <div class="mb-4">
                                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                                <input type="text" id="title" v-model="form.title" class="mt-1 block w-full" required>
                            </div>

                            <!-- Поле для компании -->
                            <div class="mb-4">
                                <label for="company" class="block text-sm font-medium text-gray-700">Company</label>
                                <input type="text" id="company" v-model="form.company" class="mt-1 block w-full" required>
                            </div>

                            <!-- Поля для минимальной и максимальной зарплаты -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Salary (Min - Max)</label>
                                <div class="flex gap-4">
                                    <input type="number" id="salary_min" v-model="form.salary_min" class="mt-1 block w-full" placeholder="Min Salary" required>
                                    <input type="number" id="salary_max" v-model="form.salary_max" class="mt-1 block w-full" placeholder="Max Salary" required>
                                </div>
                            </div>

                            <!-- Поле для города -->
                            <div class="mb-4">
                                <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                                <input type="text" id="city" v-model="form.city" class="mt-1 block w-full" required>
                            </div>

                            <!-- Поле для даты публикации -->
                            <!-- <div class="mb-4">
                                <label for="published_at" class="block text-sm font-medium text-gray-700">Published At</label>
                                <input type="date" id="published_at" v-model="form.published_at" class="mt-1 block w-full" required>
                            </div> -->

                            <!-- Кнопка для отправки данных -->
                            <div>
                                <button type="submit" class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                                    Create Vacancy
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
