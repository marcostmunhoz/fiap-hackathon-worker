<?php

namespace App\Worker\Interface\Job;

use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * @codeCoverageIgnore
 */
class QueueableUseCaseJob implements ShouldQueue
{
    /**
     * @param class-string $useCase
     */
    public function __construct(
        private string $useCase,
        private mixed $data
    ) {
    }

    public function handle(): void
    {
        $useCase = resolve($this->useCase);
        $useCase->execute($this->data);
    }

    public function tries(): int
    {
        return 1;
    }
}