<?php

namespace App\Http\Controllers;

use App\Import;
use Carbon\Carbon;
use Illuminate\Http\Request;
use League\Csv\Reader;

class ImportController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('import');
    }

    /**
     * @param Request $request
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \League\Csv\Exception
     */
    public function store(Request $request)
    {
        $text = $request->get('text');

        $csv = Reader::createFromString($request->get('text'));
        $csv->setHeaderOffset(0);

        $errors = collect();

        $import = new Import($csv);
        $records = $import->onlyNew();

        $response = $import->saveImport($records);


        dd($response);
    }
}
