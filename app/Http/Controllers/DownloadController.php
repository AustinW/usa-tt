<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use mikehaertl\pdftk\Pdf;
use Cache;
use Sheets;
use Storage;
use App\Athlete;
use App\TrampolineSpreadsheet;
use App\DoubleMiniSpreadsheet;
use App\TumblingSpreadsheet;

class DownloadController extends Controller
{
    public function select(Request $request)
    {
        $tra = TrampolineSpreadsheet::getGrouped();
        $dmt = DoubleMiniSpreadsheet::getGrouped();
        $tra = TumblingSpreadsheet::getGrouped();

        $timestamp = Carbon::now()->format('m-d-y h-i-s');
        Storage::makeDirectory('compcards/'.$timestamp);

        $files = collect();

        if ($request->has('tra')) {
            collect($request->get('tra'))->each(function ($person) use ($tra, $timestamp) {
                $athlete = new Athlete($person['name'], $tra->get($person['name']));
                $athlete->makeCompcard('trampoline', $timestamp);

                $files->push($athlete->getFilename());
            });
        }

        if ($request->has('dmt')) {
            DoubleMiniSpreadsheet::makeCompcardsFor($request->get('dmt'));
        }

        if ($request->has('tum')) {
            TumblingSpreadsheet::makeCompcardsFor($request->get('tum'));
        }
    }

    public function trampoline()
    {
        $grouped = TrampolineSpreadsheet::getGrouped();
    
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

        // Zip all the compcards
    }
}
