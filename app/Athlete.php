<?php

namespace App;

class Athlete
{
    protected $name;

    protected $athlete;

    protected $filename;

    public function __construct($name, $athlete)
    {
        $this->name = $name;

        $this->athlete = $athlete;
    }

    public function makeCompcard($event, $timestamp = null)
    {
        if ( ! $timestamp) {
            $timestamp = Carbon::now()->format('m-d-y h-i-s');
            Storage::makeDirectory(config('app.compcard.folder') . '/' . $timestamp);
        }

        $pdf = new Pdf(storage_path(config('app.compcard.templates.' . $event)));

        $func = $event . '_pdf';

        $data = $func($this->athlete->last());

        $this->setFilename(implode('/', [
            'app',
            config('app.compcard.folder'),
            $timestamp,
            str_slug($this->name) . '.pdf'
        ]));

        $result = $pdf
            ->fillForm($data)
            ->needAppearances()
            ->saveAs(storage_path($this->getFilename()));
        
        if (!$result) {
            throw new \Exception('PDF could not be created. Error: ' . $pdf->getError());
        }

        return $result;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($value)
    {
        $this->name = $value;
    }

    public function getAthlete()
    {
        return $this->athlete;
    }

    public function setAthlete($value)
    {
        $this->athlete = $value;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function setFilename($value)
    {
        $this->filename = $value;
    }
}