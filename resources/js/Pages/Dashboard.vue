<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
// Function to fetch data
function fetchData() {
    axios.get('/data')
        .then(response => {
            console.log('Data saved and received:', response.data);
        })
        .catch(error => {
            console.error('Error fetching data:', error);
        });
}

// Call fetchData when the component is mounted
onMounted(() => {
    fetchData();
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        You're logged in!
                    </div>
                    <div class="mt-4">
                        <Link :href="route('vacancies')" class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
        Go to Vacancies
    </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
