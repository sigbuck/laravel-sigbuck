<?php

namespace Sigbuck\LaravelSignalbucket\Classes;

class SignalEntity
{
    public string $type;
    public string $message;
    public string $title;
    public ?string $url;
    public int $timestamp;
    public array $metadata;

    public function __construct(string $t, string $m, string $h){
        $this->type = $t;
        $this->message = $m;
        $this->timestamp = time();
        if($h){
            $this->title = $h;
        }
    }

    public function setURL( ?string $url ): void
    {
        $this->url = $url;
    }
    public function setMetadata( ?array $data ): void
    {
        if(is_array($data)) {
            $this->metadata = $data;
        }
    }
}
