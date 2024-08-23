<?php

namespace App\Http\directions\borderCrossings\cameras\Dto;

class CameraDTO
{
    private string $url;
    private string $description;
    private string $photo;
    private int $id;

    /**
     * @param string $url
     * @param string $description
     * @param string $photo
     */
    public function __construct(string $url, string $description, string $photo, int $id)
    {
        $this->url = $url;
        $this->description = $description;
        $this->photo = $photo;
        $this->id = $id;
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

    public function getId(): int
    {
        return $this->id;
    }



    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'description' => $this->description,
            'photo' => $this->photo,
            'id' => $this->id
        ];
    }


}
