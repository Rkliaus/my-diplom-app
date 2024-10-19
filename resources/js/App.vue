<template>
    <div>
      <!-- Поле для поиска вакансий -->
      <label for="search-input">Поиск вакансий по названию:</label>
      <input
        type="text"
        id="search-input"
        v-model="searchQuery"
        placeholder="Введите название вакансии"
      />

      <!-- Выбор страны -->
      <label for="country-select">Выберите страну:</label>
      <select id="country-select" v-model="selectedCountry">
        <option v-for="country in countries" :key="country.id" :value="country.id">
          {{ country.name }}
        </option>
      </select>

      <!-- Выбор города, если доступен -->
      <label v-if="cities.length > 0" for="city-select">Выберите город:</label>
      <select v-if="cities.length > 0" id="city-select" v-model="selectedCity">
        <option value="">Все города</option>
        <option v-for="city in cities" :key="city.id" :value="city.id">
          {{ city.name }}
        </option>
      </select>

      <!-- Индикация загрузки или ошибок -->
      <p v-if="loading">Загрузка данных...</p>
      <p v-else-if="error">{{ error }}</p>

      <!-- Список вакансий -->
      <ul v-else>
        <li v-for="(vacancy, index) in vacancies" :key="index">
          <p><strong>{{ vacancy.name }}</strong></p>
          <!-- Если информация о зарплате доступна -->
          <p v-if="vacancy.salary">
            Зарплата: {{ formatSalary(vacancy.salary) }}
          </p>
          <p v-else>
            Зарплата: Не указана
          </p>
        </li>
      </ul>

      <!-- Пагинация -->
      <div v-if="totalPages > 1" class="pagination">
        <button @click="prevPage" :disabled="currentPage === 0">Назад</button>
        <span>Страница {{ currentPage + 1 }} из {{ totalPages }}</span>
        <button @click="nextPage" :disabled="currentPage === totalPages - 1">Вперед</button>
      </div>
    </div>
  </template>

  <script setup>
  import { ref, onMounted, watch } from 'vue'
  import axios from 'axios'

  const countries = ref([]); // Список стран
  const cities = ref([]); // Список городов выбранной страны
  const vacancies = ref([]); // Список вакансий
  const loading = ref(false); // Состояние загрузки
  const error = ref(null); // Сообщение об ошибке
  const selectedCountry = ref(null); // Выбранная страна
  const selectedCity = ref(''); // Выбранный город
  const searchQuery = ref(''); // Строка поиска

  const currentPage = ref(0); // Текущая страница
  const totalPages = ref(1); // Общее количество страниц
  const perPage = 20; // Количество вакансий на одной странице

  // Получаем список стран с регионами и городами
  const fetchCountries = async () => {
    try {
      const response = await axios.get('https://api.hh.ru/areas');
      countries.value = response.data; // Загружаем все страны
    } catch (err) {
      console.error('Ошибка при загрузке стран:', err);
      error.value = 'Ошибка загрузки стран';
    }
  };

  // Обработка выбора страны и обновление списка городов
  watch(selectedCountry, () => {
    const country = countries.value.find(c => c.id === selectedCountry.value);
    cities.value = country ? country.areas : [];
    selectedCity.value = ''; // Сброс выбора города
    currentPage.value = 0; // Сброс страницы на первую
    fetchVacancies(); // Обновляем вакансии при изменении страны
  });

  // Форматирование зарплаты (от - до или одна сумма)
  const formatSalary = (salary) => {
    if (salary.from && salary.to) {
      return `${salary.from} - ${salary.to} ${salary.currency}`;
    } else if (salary.from) {
      return `от ${salary.from} ${salary.currency}`;
    } else if (salary.to) {
      return `до ${salary.to} ${salary.currency}`;
    }
    return 'Не указана';
  };

  // Загружаем вакансии с учетом выбранной страны, города и строки поиска
  const fetchVacancies = async () => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.get('/data', {
        params: {
          country: selectedCountry.value || null, // ID выбранной страны
          city: selectedCity.value || null, // ID города
          text: searchQuery.value, // Строка поиска
          page: currentPage.value, // Номер страницы
          per_page: perPage // Количество вакансий на странице
        }
      });

      vacancies.value = response.data.items; // vacancies находятся в "items"
      totalPages.value = Math.ceil(response.data.found / perPage); // Вычисляем общее количество страниц
    } catch (err) {
      console.error('Ошибка при загрузке данных:', err);
      error.value = 'Ошибка загрузки данных';
    } finally {
      loading.value = false;
    }
  };

  // Переключение на следующую страницу
  const nextPage = () => {
    if (currentPage.value < totalPages.value - 1) {
      currentPage.value += 1;
      fetchVacancies();
    }
  };

  // Переключение на предыдущую страницу
  const prevPage = () => {
    if (currentPage.value > 0) {
      currentPage.value -= 1;
      fetchVacancies();
    }
  };

  // Следим за изменениями строки поиска и города
  watch([searchQuery, selectedCity], () => {
    currentPage.value = 0; // Сбрасываем на первую страницу при изменении фильтров
    fetchVacancies(); // Обновляем вакансии при изменении фильтров
  });

  onMounted(() => {
    fetchCountries(); // Загружаем список стран при монтировании компонента
    fetchVacancies(); // Загружаем вакансии по умолчанию (без фильтров)
  });
  </script>

  <style>
  .pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 20px;
  }

  button {
    margin: 0 10px;
  }
  </style>
