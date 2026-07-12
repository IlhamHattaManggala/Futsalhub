<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;
use App\Models\Schedule;

class NewScheduleNotification extends Notification
{
    use Queueable;

    public $schedule;

    public function __construct(Schedule $schedule)
    {
        $this->schedule = $schedule;
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class, 'database'];
    }

    public function toWebPush($notifiable, $notification)
    {
        $tenantSlug = $notifiable->team ? $notifiable->team->slug : $notifiable->slug;
        $url = route('schedules.index', ['slug' => $tenantSlug]);

        $messageText = 'Jadwal baru (' . $this->schedule->type . ') telah ditambahkan: ' . $this->schedule->title . ' pada ' . \Carbon\Carbon::parse($this->schedule->start_time)->translatedFormat('d F Y, H:i') . ' WIB di ' . $this->schedule->location . '.';

        return (new WebPushMessage)
            ->title('Jadwal Baru: ' . $this->schedule->title)
            ->body($messageText)
            ->icon('/images/logo.png')
            ->action('Lihat Jadwal', 'view_schedule')
            ->data([
                'url' => $url,
            ]);
    }

    public function toArray($notifiable)
    {
        return [
            'schedule_id' => $this->schedule->id,
            'title' => $this->schedule->title,
            'type' => $this->schedule->type,
            'start_time' => $this->schedule->start_time,
            'location' => $this->schedule->location,
            'message' => 'Jadwal baru ditambahkan: ' . $this->schedule->title,
        ];
    }
}
