import axios from "axios";

export default class CameraService {

    static async getAllByDirectionId(borderCrossingId) {
        let attempt = 0;

        while (attempt < 5) {
            try {
                const response = await axios.get(`/api/directions/borderCrossing/cameras?borderCrossingId=${borderCrossingId}`);
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
