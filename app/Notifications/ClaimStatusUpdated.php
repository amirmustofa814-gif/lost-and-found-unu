<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ClaimStatusUpdated extends Notification
{
    use Queueable;

    public $claim;
    public $status;

    public function __construct($claim, $status)
    {
        $this->claim = $claim;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        // Tentukan pesan berdasarkan status
        if ($this->status === 'verified') {
            $pesan = "Selamat! Klaim Anda untuk '{$this->claim->foundItem->item_name}' DITERIMA ✅";
        } else {
            $pesan = "Maaf, Klaim Anda untuk '{$this->claim->foundItem->item_name}' DITOLAK ❌";
        }
        
        return [
            'message' => $pesan,
            'url' => route('claims.index'),
            'created_at' => now(),
        ];
    }
}