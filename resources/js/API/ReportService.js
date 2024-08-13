import axios from "axios";
import {useNavigate} from "react-router-dom";

export default class ReportService {

    static async getLast(borderCrossingId) {
        let attempt = 0;

        while (attempt < 5) {
            try {
                const response = await axios.get(`/api/directions/borderCrossing/reports/last?borderCrossingId=${borderCrossingId}`);
                return response.data; // Успешный ответ, возвращаем данные
            } catch (error) {
                attempt += 1;
                console.error(`Attempt ${attempt} failed: ${error.message}`);
                if (attempt >= 5) {
                    console.error('Max retries reached. Throwing error.');
                    throw error; // Превышен лимит попыток, выбрасываем ошибку
                }
                // Если не достигнут лимит попыток, можно добавить задержку перед повтором
                await new Promise(res => setTimeout(res, 1000)); // Задержка 1 секунда
            }
        }
    }

    static async getAll(borderCrossingId) {
        let attempt = 0;

        while (attempt < 5) {
            try {
                const response = await axios.get(`/api/directions/borderCrossing/reports?borderCrossingId=${borderCrossingId}`);
                return response.data; // Успешный ответ, возвращаем данные
            } catch (error) {
                attempt += 1;
                console.error(`Attempt ${attempt} failed: ${error.message}`);
                if (attempt >= 5) {
                    console.error('Max retries reached. Throwing error.');
                    throw error; // Превышен лимит попыток, выбрасываем ошибку
                }
                // Если не достигнут лимит попыток, можно добавить задержку перед повтором
                await new Promise(res => setTimeout(res, 1000)); // Задержка 1 секунда
            }
        }
    }

    static async createReport(data) {
        let attempt = 0;

        while (attempt < 5) {
            try {
                const response = await axios.post(`/api/directions/borderCrossing/reports`, data);
                return response.data;
            } catch (error) {
                attempt += 1;
                console.error(`Attempt ${attempt} failed: ${error.message}`);
                if (attempt >= 5) {
                    console.error('Max retries reached. Throwing error.');
                    throw error; // Превышен лимит попыток, выбрасываем ошибку
                }
                // Если не достигнут лимит попыток, можно добавить задержку перед повтором
                await new Promise(res => setTimeout(res, 1000)); // Задержка 1 секунда
            }
        }
    }

}
