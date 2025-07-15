<?php

namespace App\Mail;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class TaskAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    protected int $taskId;

    public function __construct(int $taskId)
    {
        $this->taskId = $taskId;
        Log::info('📩 TaskAssignedMail instantiated with ID: ', ['id' => $taskId]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Task Assigned Mail'
        );
    }

    public function content(): Content
    {
        // Load the task here inside the queue context
        $task = Task::with('assignee')->findOrFail($this->taskId);

        Log::info('📧 Loaded task inside queued mail:', ['title' => $task->title]);

        return new Content(
            view: 'task-assigned',
            with: ['task' => $task]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
