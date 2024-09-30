<?php

namespace App\Console\Commands;

use App\Http\directions\borderCrossings\reports\DTO\StatisticGraphTypeDTO;
use App\Http\directions\borderCrossings\reports\Services\ReportService;
use App\Utils\TelegramStatsUtils;
use AWS\CRT\Log;
use Carbon\Carbon;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class PublishPostGraph extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:publish-post-graph';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Команда публикует статистику за неделю по всем направлениям в канал';

    private TelegramStatsUtils $telegramStatsUtils;

    /**
     * @param TelegramStatsUtils $telegramStatsUtils
     */
    public function __construct(TelegramStatsUtils $telegramStatsUtils)
    {
        parent::__construct();
        $this->telegramStatsUtils = $telegramStatsUtils;
    }


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->telegramStatsUtils->sendGraphStat(8, false, "Багратионовск - Bezledy", "@BagratBez", "stat33.png");

        // ------------------

        $this->telegramStatsUtils->sendGraphStat(6, false, "Бенякони - Šalčininkai", "@BenShal", "stat44.png");

        // ------------------

        $this->telegramStatsUtils->sendGraphStat(9, false, "Брест - Terespol", "@BrestTerespol", "stat3.png");

        // ------------------

        $this->telegramStatsUtils->sendGraphStat(3, false, "Чернышевское - Kybartai", "@ChernKyb", "stat4.png");

        // ------------------

        $this->telegramStatsUtils->sendGraphStat(5, false, "Каменный Лог - Medininkai", "@KamLogMed", "stat5.png");

        // ------------------

        $this->telegramStatsUtils->sendGraphStat(7, false, "Мамоново II - Grzechotki", "@MamGrze", "stat55.png");

    }
}
