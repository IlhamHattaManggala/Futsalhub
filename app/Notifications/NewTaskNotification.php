<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;
use App\Models\Task;

class NewTaskNotification extends Notification
{
    use Queueable;

    public $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class, 'database'];
    }

    public function toWebPush($notifiable, $notification)
    {
        $tenantSlug = $notifiable->team ? $notifiable->team->slug : $notifiable->slug;
        $url = route('tasks.index', ['slug' => $tenantSlug]);

        return (new WebPushMessage)
            ->title('Tugas Baru: ' . $this->task->title)
            ->body('Anda mendapatkan tugas baru dari pelatih. Batas waktu: ' . \Carbon\Carbon::parse($this->task->due_date)->translatedFormat('d F Y, H:i') . ' WIB.')
            ->icon('/images/logo.png')
            ->action('Buka Tugas', 'view_task')
            ->data([
                'url' => $url,
            ]);
    }

    public function toArray($notifiable)
    {
        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'due_date' => $this->task->due_date,
            'message' => 'Anda mendapatkan tugas baru: ' . $this->task->title,
        ];
    }
}
