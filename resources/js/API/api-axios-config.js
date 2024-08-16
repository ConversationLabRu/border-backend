import axios from 'axios';
import { retrieveLaunchParams } from '@tma.js/sdk-react';

// Получаем начальные данные при создании экземпляра Axios
const { initDataRaw } = retrieveLaunchParams();

// Создаем экземпляр Axios с базовым URL
const api = axios.create({
    baseURL: process.env.APP_URL, // Ваш базовый URL API
});

// Перехватчик запроса для добавления заголовка Authorization
api.interceptors.request.use(
    (config) => {
        // Добавляем заголовок Authorization к каждому запросу
        config.headers['Authorization'] = `tma ${initDataRaw}`;
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

export default api;
