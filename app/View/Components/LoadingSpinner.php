<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LoadingSpinner extends Component
{
    public $size;
    public $color;
    public $text;

    public function __construct($size = 'md', $color = 'blue', $text = null)
    {
        $this->size = $size;
        $this->color = $color;
        $this->text = $text;
    }

    public function render()
    {
        return view('components.loading-spinner');
    }
}
