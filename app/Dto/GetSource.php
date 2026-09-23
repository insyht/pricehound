<?php

namespace App\Dto;

use App\Models\Hound;

class GetSource
{
    public function __construct(
        public Hound $hound,
        public string $id,
        public string $url,
        public array $headers,
        public string $callbackUrl
    ) {}
}
