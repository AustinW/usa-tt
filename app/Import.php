<?php

namespace App;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use League\Csv\Reader;

class Import
{
    /**
     * @var Reader
     */
    protected $reader;

    protected $records;

    protected $coda;

    /**
     * Import constructor.
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;

        $this->coda = new Coda();

        $this->records = $this->reader->getRecords();
    }

    /**
     * @return Collection
     */
    public function convertRecords()
    {
        return collect($this->records)
            ->filter(function ($record) {
                $expiration = Carbon::createFromFormat('m/d/Y', $record['Expiration']);
                return ($expiration->diffInYears() < 1);
            })->map(function ($record) {
                return [
                    // Active
                    'c-lMmfLo7AyD' => collect([
                        'Active' => 'Yes',
                        'Expired' => 'No'
                    ])->get($record['Status']),

                    // USAG #
                    'c-LgOU1k4klG' => $record['Member Number'],

                    // First Name
                    'c-w3nRuBnISb' => $record['First'],

                    // Last Name
                    'c-hwgu_i8p2X' => $record['Last'],

                    // Gender
                    'c-1dew3zFP_L' => substr($record['Gender'], 0, 1),

                    // Birthday
                    'c-Bb78YvCgYj' => $record['DOB']
                ];
            });
    }

    /**
     * @return Collection
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function codaRows()
    {
        $response = $this->coda->request('rows');

        $data = $this->coda->jsonContents($response);

        return collect($data->get('items'));
    }

    /**
     * @return Collection
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function onlyNew()
    {
        $import = $this->convertRecords();

        $currentRows = $this->codaRows();

        return collect(
            $import->filter(function ($importRow) use ($currentRows) {
                return ! $currentRows->firstWhere('name', $importRow['c-w3nRuBnISb'] . ' ' . $importRow['c-hwgu_i8p2X']);
            })->map(function ($row) {
                return $row;
            })
        );
    }

    /**
     * @param Collection $records
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function saveImport(Collection $records)
    {
        return $this->coda->request('rows', 'post', [
            'rows' => $records->map(function ($record) {
                return collect()->put('cells', collect($record)->map(function ($value, $column) {
                    return [
                        'column' => $column,
                        'value' => $value
                    ];
                })->values()->push([
                    'column' => 'c-AkifxGtsqh',
                    'value' => 'Imported'
                ]));
            })->values()->toArray()
        ]);
    }
}
