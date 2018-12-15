<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Spreadsheet extends Model
{
    public static function getGrouped()
    {
        return collect();
    }
}
