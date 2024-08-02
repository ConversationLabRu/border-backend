<?php

namespace App\Http\directions\borderCrossings\reports\transports\Services;

use App\Http\directions\borderCrossings\reports\transports\Entities\Transport;

class TransportService
{
    public function getAllTransport()
    {
        return Transport::all();
    }
}
