<?php

namespace App\Livewire;

use Livewire\Component;

class InboxNotifications extends Component
{
    public function getNotificationsProperty()
    {
        return auth()->user()->unreadNotifications;
    }

    public function estouCiente($notificationId)
    {
        $notification = auth()->user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function acessar($notificationId, $url)
    {
        $this->estouCiente($notificationId);
        if ($url && $url !== '#') {
            return redirect($url);
        }
    }

    public function limparTodas()
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function render()
    {
        return view('livewire.inbox-notifications');
    }
}
