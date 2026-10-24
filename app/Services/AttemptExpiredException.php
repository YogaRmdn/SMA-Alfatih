<?php

namespace App\Services;

use RuntimeException;

/**
 * Percobaan ujian sudah kedaluwarsa / ditutup — tidak bisa lagi menerima jawaban.
 */
class AttemptExpiredException extends RuntimeException {}
