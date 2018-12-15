<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TrampolineSpreadsheet;
use App\DoubleMiniSpreadsheet;
use App\TumblingSpreadsheet;

class ViewController extends Controller
{
    public function index()
    {
        $trampoline = TrampolineSpreadsheet::getGrouped();
        $doubleMini = DoubleMiniSpreadsheet::getGrouped();
        $tumbling = TumblingSpreadsheet::getGrouped();

        $athletes = $trampoline
            ->keys()
            ->merge($doubleMini->keys())
            ->merge($tumbling->keys())
            ->flip()
            ->map(function ($item, $name) use ($trampoline, $doubleMini, $tumbling) {
                return [
                    'trampoline' => $trampoline->has($name) ?: null,
                    'double_mini' => $doubleMini->has($name) ?: null,
                    'tumbling' => $tumbling->has($name) ?: null
                ];
            });
        
        return view('athletes', compact('athletes'));
    }
}
