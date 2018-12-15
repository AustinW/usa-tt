<?php

namespace App;

use App\Spreadsheet;
use Cache;
use Sheets;

class TrampolineSpreadsheet extends Spreadsheet
{
    public static function getGrouped()
    {
        $rows = Cache::rememberForever('spreadsheet', function () {
            $sheets = app('sheets');
    
            return $sheets
                ->spreadsheet(config('app.compcard.trampoline'))
                ->sheet('Copy of Form responses 1')
                ->get();
        });
    
        $header = $rows->pull(0);
    
        $values = Sheets::collection($header, $rows);
    
        return $values->groupBy('Name');
    }

    public function makeCompcardsFor($names)
    {
        $athletes = static::getGrouped();

        $timestamp = Carbon::now()->format('m-d-y h-i-s');
        Storage::makeDirectory('compcards/' . $timestamp);

        $files = collect();
        $errors = collect();
    
        $grouped->each(function ($athlete, $name) use ($timestamp, $files, $errors) {
            $pdf = new Pdf(storage_path('app/templates/trampoline.pdf'));
    
            $data = trampoline_pdf($athlete->last());
    
            $filename = 'app/compcards/' . $timestamp . '/' . str_slug($name) . '.pdf';
            $files->push($filename);
    
            $result = $pdf->fillForm($data)
                ->needAppearances()
                ->saveAs(storage_path($filename));
    
            if (!$result) {
                $errors->push($pdf->getError());
            }
        });

        if ($errors->count()) {
            return view('error', compact('errors'));
        }
    }
}
