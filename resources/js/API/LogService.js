import axios from "axios";
import api from "@/API/api-axios-config.js";

export default class LogService {

    static async sendLog(message) {
        try {
            const data = {
                message: message
            }

            const response = await api.post(`/api/log`, data);
            return response.data; // Успешный ответ, возвращаем данные
        } catch (error) {
            await new Promise(res => setTimeout(res, 1000)); // Задержка 1 секунда
        }
    }

}
