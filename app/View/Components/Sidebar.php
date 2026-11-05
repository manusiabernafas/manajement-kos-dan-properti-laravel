<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Route;

class Sidebar extends Component
{
    public array $menu;

    public function __construct(array $menu)
    {
        $this->menu = $this->filterMenu($menu);
    }

    protected function filterMenu(array $sections): array
    {
        $user = auth()->user();

        $filtered = [];

        foreach ($sections as $section) {
            $items = array_values(array_filter($section['items'], function ($item) use ($user) {
                if (! Route::has($item['route'])) {
                    return false;
                }

                if (empty($item['permission'])) {
                    return true;
                }

                return $user && $user->can($item['permission']);
            }));

            if (count($items) > 0) {
                $filtered[] = ['title' => $section['title'], 'items' => $items];
            }
        }

        return $filtered;
    }

    public function render()
    {
        return view('components.sidebar');
    }
}
