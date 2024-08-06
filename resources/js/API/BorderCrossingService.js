import axios from "axios";

export default class BorderCrossingService {

    static async getAllById(directionId) {
        let attempt = 0;

        while (attempt < 5) {
            try {
                const response = await axios.get(`/api/directions/borderCrossing?directionId=${directionId}`);
                console.log(response.data[0].direction)
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

}