<template>
    <div>
      <h1>Вакансии</h1>
      <App /> <!-- Здесь мы используем App.vue -->
    </div>
    <div class="mt-4">
    <Link :href="route('dashboard')" class="px-4 py-2 font-bold text-white bg-green-500 rounded hover:bg-green-700">
        Back to Dashboard
    </Link>
</div>
  </template>

  <script setup>
  import App from '@/App.vue'; // Импортируйте ваш основной компонент
  import { Link } from '@inertiajs/vue3';
  </script>

  <style>
  /* Ваши стили здесь */
  </style>
