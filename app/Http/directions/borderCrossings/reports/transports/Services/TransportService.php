<?php

namespace App\Http\directions\borderCrossings\reports\transports\Services;

use App\Http\directions\borderCrossings\reports\transports\DTO\TransportDTO;
use App\Http\directions\borderCrossings\reports\transports\Entities\Transport;
use function PHPSTORM_META\map;

class TransportService
{
    public function getAllTransport()
    {
        $transports = Transport::all();

        $result = $transports->map(function (Transport $transport) {
                $transportDTO = new TransportDTO(
                    $transport->getAttributeValue("icon"),
                    $transport->getAttributeValue("id"),
                );

                return $transportDTO->toArray();
            });

        return $result;
    }
}
