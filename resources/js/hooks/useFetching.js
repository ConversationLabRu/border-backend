import {useState} from "react";

/**
 * Хук для запросов на разные сервисы
 * @param callback
 */
export const useFetching = (callback) => {

    // Индикатор загрузки
    const [isLoading, setIsLoading] = useState(false);

    // Сообщение об ошибке
    const [error, setError] = useState('');

    // лямбда запрос
    const fetching = async () => {
        try {
            setIsLoading(true);
            await callback();
        } catch (e) {
            setError(e.message);
        } finally {
            setIsLoading(false);
        }
    }

    return [fetching, isLoading, error];
}
