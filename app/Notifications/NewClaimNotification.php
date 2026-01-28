<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewClaimNotification extends Notification
{
    use Queueable;

    public $claim;

    public function __construct($claim)
    {
        $this->claim = $claim;
    }

    public function via($notifiable)
    {
        return ['database']; // PENTING: Simpan ke database
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Ada klaim baru untuk barang: ' . $this->claim->foundItem->item_name,
            'url' => route('claims.index'), // Link saat diklik
            'created_at' => now(),
        ];
    }
}