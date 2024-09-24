<?php

namespace App\Console\Commands;

use App\Http\directions\borderCrossings\reports\Services\ReportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class PublishPostBenyakoni extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:publish-post-benyakoni';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Команда публикует прогноз по Бенякони - Šalčininkai в канал';

    private ReportService $reportService;

    /**
     * @param ReportService $reportService
     */
    public function __construct(ReportService $reportService)
    {
        parent::__construct();
        $this->reportService = $reportService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Каменный Лог - Medininkai
        $result = $this->reportService->getStatistics(6);

        $formatedText = "На текущий момент прогноз прохождения пограничного перехода *Бенякони \\- Šalčininkai* составляет:";

        // ------------------------------------------------

        if ($result->getTimeCarNotFlipped() != "Нет информации" || $result->getTimeCarNotFlipped() != "Нет информации") $formatedText .= "\n\n🇧🇾➡️🇱🇹";

        if ($result->getTimeCarNotFlipped() != "Нет информации") $formatedText .= "\n🚘: {$result->getTimeCarNotFlipped()}";

        if ($result->getTimeBusNotFlipped() != "Нет информации") $formatedText .= "\n🚌: {$result->getTimeBusNotFlipped()}";

        // -----------------------------------------------

        if ($result->getTimeCarFlipped() != "Нет информации" || $result->getTimeBusFlipped() != "Нет информации") $formatedText .= "\n\n🇱🇹➡️🇧🇾";

        if ($result->getTimeCarFlipped() != "Нет информации") $formatedText .= "\n🚘: {$result->getTimeCarFlipped()}";

        if ($result->getTimeBusFlipped() != "Нет информации") $formatedText .= "\n🚌: {$result->getTimeBusFlipped()}";

        $body = [
            'chat_id' => '@BenShal',
            'text' => $formatedText,
            'parse_mode' => 'MarkdownV2',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        [
                            'text' => 'Открыть приложение',
                            'url' => 'https://t.me/bordercrossingsbot/app'
                        ]
                    ]
                ]
            ])
        ];

        $response = Http::post("https://api.telegram.org/bot7215428078:AAFY67PRE0nifeLeoISEwznfE2WEiXF6-xU/sendMessage", $body);

        // Обработка ответа
        if ($response->successful()) {
            $this->info('Сообщение успешно отправлено в Telegram.');
        } else {
            $this->error('Ошибка отправки сообщения: ' . $response->body());
        }
    }
}
