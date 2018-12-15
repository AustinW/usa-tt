<?php

namespace App\Http\Controllers;

use mikehaertl\pdftk\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function autofill(Request $request)
    {
        $template = storage_path($request->get('pdf'));
        
        if (!file_exists($template)) {
            return view('error');
        }

        $pdf = new Pdf($template);

        $output = storage_path('app/output/mobility-' . time() . '.pdf');

        $fields = collect($request->all())->map(function ($value, $key) {
            if ($key === 'athlete_birth_year') {
                $value = substr($value, -2);
            }

            if (str_contains($key, '_dd')) {
                $value = str_replace('0', '', $value);
            }

            return str_replace(['undefined'], '', $value);
        })->toArray();

        $filled = $pdf->fillForm($fields)
            ->needAppearances()
            ->saveAs($output);
        
        if (!$filled) {
            throw new \Exception($pdf->getError());
        }
        
        return response()->file($output)->deleteFileAfterSend();
    }
}
