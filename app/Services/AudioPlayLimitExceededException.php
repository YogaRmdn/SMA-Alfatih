<?php

namespace App\Services;

use RuntimeException;

/**
 * Batas pemutaran audio tercapai.
 */
class AudioPlayLimitExceededException extends RuntimeException
{
    public function __construct(int $max)
    {
        parent::__construct("Batas pemutaran audio ({$max}x) sudah tercapai. Anda tidak dapat memutar audio ini lagi.");
    }
}
