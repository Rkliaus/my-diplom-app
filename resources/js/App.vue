<template>
    <div>
      <!-- Поле для поиска вакансий -->
      <label for="search-input">Поиск вакансий по названию:</label>
      <input
        type="text"
        id="search-input"
        v-model="searchQuery"
        placeholder="Введите название вакансии"
        @input="fetchVacancies"
      />

      <!-- Выбор страны -->
      <label for="country-select">Выберите страну:</label>
      <select id="country-select" v-model="selectedCountry" @change="onCountryChange">
        <option v-for="country in countries" :key="country.id" :value="country.id">
          {{ country.name }}
        </option>
      </select>

      <!-- Выбор города, если доступен -->
      <label v-if="cities.length > 0" for="city-select">Выберите город:</label>
      <select v-if="cities.length > 0" id="city-select" v-model="selectedCity" @change="fetchVacancies">
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
    </div>
  </template>

  <script setup>
  import { ref, onMounted } from 'vue'
  import axios from 'axios'

  const countries = ref([]); // Список стран
  const cities = ref([]); // Список городов выбранной страны
  const vacancies = ref([]); // Список вакансий
  const loading = ref(false); // Состояние загрузки
  const error = ref(null); // Сообщение об ошибке
  const selectedCountry = ref(null); // Выбранная страна
  const selectedCity = ref(''); // Выбранный город
  const searchQuery = ref(''); // Строка поиска

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

  // Обработка выбора страны
  const onCountryChange = () => {
    const country = countries.value.find(c => c.id === selectedCountry.value);
    cities.value = country ? country.areas : [];
    selectedCity.value = ''; // Сброс выбора города
    fetchVacancies(); // Загружаем вакансии для выбранной страны
  };

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
          country: selectedCountry.value, // ID выбранной страны
          city: selectedCity.value || null, // ID города, если выбран
          text: searchQuery.value, // Строка поиска по названию вакансии
          page: 0,
          per_page: 20
        }
      });

      vacancies.value = response.data.items; // vacancies находятся в "items"
    } catch (err) {
      console.error('Ошибка при загрузке данных:', err);
      error.value = 'Ошибка загрузки данных';
    } finally {
      loading.value = false;
    }
  };

  onMounted(() => {
    fetchCountries(); // Загружаем список стран при монтировании компонента
    fetchVacancies(); // Загружаем вакансии по умолчанию (без фильтров)
  });
  </script>
