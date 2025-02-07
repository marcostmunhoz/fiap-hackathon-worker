<?php

namespace App\Shared\Application\Service;

use App\Shared\Domain\ValueObject\Email;

interface EmailNotifierInterface
{
    public function notify(Email $email, string $subject, string $body): void;
}