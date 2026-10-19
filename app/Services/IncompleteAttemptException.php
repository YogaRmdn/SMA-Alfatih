<?php

namespace App\Services;

use RuntimeException;

/**
 * Dilempar ketika sesi pengisian belum lengkap sehingga tidak boleh dinilai.
 */
class IncompleteAttemptException extends RuntimeException {}
