<?php

namespace App\Http\directions\borderCrossings\cameras\Dto;

class CameraDTO
{
    private string $url;
    private string $description;
    private string $photo;

    /**
     * @param string $url
     * @param string $description
     * @param string $photo
     */
    public function __construct(string $url, string $description, string $photo)
    {
        $this->url = $url;
        $this->description = $description;
        $this->photo = $photo;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPhoto(): string
    {
        return $this->photo;
    }

    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'description' => $this->description,
            'photo' => $this->photo
        ];
    }


}
