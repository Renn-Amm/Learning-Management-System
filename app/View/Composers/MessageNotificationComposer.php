<?php

namespace App\View\Composers;

use App\Models\Message;
use Illuminate\View\View;

class MessageNotificationComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        if (auth()->check()) {
            $unreadMessagesCount = Message::where('to_id', auth()->id())
                ->unread()
                ->count();
            
            $view->with('unreadMessagesCount', $unreadMessagesCount);
        } else {
            $view->with('unreadMessagesCount', 0);
        }
    }
}
