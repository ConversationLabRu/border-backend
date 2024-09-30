<?php

namespace App\Console\Commands;

use App\Utils\TelegramStatsUtils;
use Illuminate\Console\Command;

class PublishPostGraphFlipped extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:publish-post-graph-flipped';

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
        $this->telegramStatsUtils->sendGraphStat(8, true, "Bezledy - Багратионовск", "@BagratBez", "statFlip1.png");

        // ------------------

        $this->telegramStatsUtils->sendGraphStat(6, true, "Šalčininkai - Бенякони", "@BenShal", "statFlip2.png");

        // ------------------

        $this->telegramStatsUtils->sendGraphStat(9, true, "Terespol - Брест", "@BrestTerespol", "statFlip3.png");

        // ------------------

        $this->telegramStatsUtils->sendGraphStat(3, true, "Kybartai - Чернышевское", "@ChernKyb", "statFlip4.png");

        // ------------------

        $this->telegramStatsUtils->sendGraphStat(5, true, "Medininkai - Каменный Лог", "@KamLogMed", "statFlip5.png");

        // ------------------

        $this->telegramStatsUtils->sendGraphStat(7, true, "Grzechotki - Мамоново II", "@MamGrze", "statFlip6.png");

    }
}
