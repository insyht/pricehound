<?php

namespace App\Interfaces;

interface BelongsToUserInterface
{
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo;
}
