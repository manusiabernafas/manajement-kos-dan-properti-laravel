<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Navbar extends Component
{
    public string $title;
    public array $breadcrumbs;
    public $notifications;
    public int $unreadCount;

    public function __construct(string $title = 'Dashboard', array $breadcrumbs = [])
    {
        $this->title = $title;
        $this->breadcrumbs = $breadcrumbs;

        $user = auth()->user();
        $this->notifications = $user ? $user->notifications()->latest()->limit(8)->get() : collect();
        $this->unreadCount = $user ? $user->unreadNotifications()->count() : 0;
    }

    public function render()
    {
        return view('components.navbar');
    }
}
