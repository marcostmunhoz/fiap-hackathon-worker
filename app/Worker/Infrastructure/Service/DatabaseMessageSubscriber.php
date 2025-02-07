<?php

namespace App\Worker\Infrastructure\Service;

use App\Shared\Domain\Data\Message;
use App\Worker\Domain\Service\AbstractMessageSubscriber;
use Illuminate\Support\Facades\DB;

/**
 * @codeCoverageIgnore
 */
class DatabaseMessageSubscriber extends AbstractMessageSubscriber
{
    public function listen(): void
    {
        while (true) {
            $rows = $this->fetchDatabaseMessages();

            /** @var object{id: int, data: string} $row */
            foreach ($rows as $row) {
                $message = Message::fromJson($row->data);

                logger()->info("Processing message {$row->id}...");

                $this->handle($message->event, $message->data);
                $this->markAsAcked($row->id);

                logger()->info("Message {$row->id} processed.");
            }

            sleep(5);
        }
    }

    /**
     * @return object{id: int, data: string}[]
     */
    private function fetchDatabaseMessages(): array
    {
        return DB::table('messages')->get(['id', 'data'])->toArray();
    }

    private function markAsAcked(int $id): void
    {
        DB::table('messages')->where('id', $id)->delete();
    }
}