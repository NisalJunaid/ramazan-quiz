<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuizRangeChanged implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public ?int $quizRangeId = null)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('quiz-range'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'QuizRangeChanged';
    }
}
