<?php

namespace App\Utils;

use App\Http\directions\borderCrossings\Dto\DirectionCrossingDTO;

class TextFormaterUtils
{
    public static function countryToFlag(String $country)
    {
        if ($country == "Россия") return "🇷🇺";

        if ($country == "Польша") return "🇵🇱";

        if ($country == "Литва") return "🇱🇹";

        return "🇧🇾";
    }

    public static function transportToEmoji(int $transportId)
    {
        if ($transportId == 2) return "🚗";

        if ($transportId == 3) return "🚌";

        return "🚶";
    }

}
