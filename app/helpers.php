<?php

function age_group($birthdate, $level) {
    
    list($d, $m, $y) = explode('/', $birthdate);
    

    $age = \Illuminate\Support\Carbon::now()->year - (int) $y;
    
    switch ($level) {
        case 'Level 8':
        case 'Level 9':
        case 'Level 10':
            
        case 'YE 11-12, YE 13-14, OE':
            if ($age >= 11 && $age <= 12) {
                return 'YE 11-12';
            } else if ($age >= 13 && $age <= 14) {
                return 'YE 13-14';
            } else {
                return 'OE';
            }

            break;
        case 'Senior Elite':
            return '';
            break;
    }
}

function trampoline_pdf($data) {
    $keyed = [
        'NAME' => $data['Name'],
        'MF' => $data['Gender'],
        'LEVEL' => $data['Level'],
        'AGEGROUP' => age_group($data['Birthdate'], $data['Level']),
        'TEAM' => config('app.team')
    ];

    foreach (range(1, 10) as $i) {
        // 1st Voluntary
        $keyed['ELEMENTS' . $i] = array_get($data, '1V - Skill ' . $i);
        $keyed['DD' . $i] = array_get($data, '1V - Skill ' . $i . ' DD');

        // 2nd Voluntary
        $keyed['ELEMENTS' . $i . '_3'] = array_get($data, '2V - Skill ' . $i);
        $keyed['DD' . $i . '_3'] = array_get($data, '2V - Skill ' . $i . ' DD');

        // 3rd Voluntary
        $keyed['ELEMENTS' . $i . '_2'] = array_get($data, '3V - Skill ' . $i);
        $keyed['DD' . $i . '_2'] = array_get($data, '3V - Skill ' . $i . ' DD');
    }

    $routineTotals = [1 => 'TOTAL', 2 => 'TOTAL_3', 3 => 'TOTAL_2'];

    foreach (range(1, 3) as $routine) {
        $total = collect($data)
            ->filter(function ($item, $key) use ($routine) {
                return preg_match('/' . $routine . 'V - Skill \d+ DD/', $key);
            })
            ->reduce(function ($carry, $item) {
                return $carry + (float) $item;
            });

        if ($total) {
            $keyed[$routineTotals[$routine]] = $total;
        }
    }

    return $keyed;
}

function doublemini_pdf($data) {
    $keyed = [
        'NAME' => $data['Name'],
        'MF' => $data['Gender'],
        'LEVEL' => $data['Level'],
        'AGEGROUP' => age_group($data['Birthdate'], $data['Level']),
        'TEAM' => config('app.team')
    ];

    foreach (range(1, 4) as $i) {
        $key = ($i === 1) ? '' : '_' . $i;
        
        $keyed['MOUNTERELEMENTS' . $key] = array_get($data, $i . 'P - Mounter Skill');
        $keyed['SPOTTERELEMENTS' . $key] = array_get($data, $i . 'P - Spotter Skill');
        $keyed['DISMOUNTELEMENTS' . $key] = array_get($data, $i . 'P - Dismount Skill');
        
        $keyed['MOUNTERDD' . $key] = array_get($data, $i . 'P - Mounter DD');
        $keyed['SPOTTERDD' . $key] = array_get($data, $i . 'P - Spotter DD');
        $keyed['DISMOUNTDD' . $key] = array_get($data, $i . 'P - Dismount DD');

        $total = collect($data)
            ->filter(function ($item, $key) use ($routine) {
                return preg_match('/' . $routine . 'P - \w DD/', $key);
            })
            ->reduce(function ($carry, $item) {
                return $carry + (float) $item;
            });

        if ($total) {
            $keyed['TOTALRow1' . $key] = $total;
        }
    }

    return $keyed;
}

function tumbling_pdf($data) {
    $keyed = [
        'NAME' => $data['Name'],
        'MF' => $data['Gender'],
        'LEVEL' => $data['Level'],
        'AGEGROUP' => age_group($data['Birthdate'], $data['Level']),
        'TEAM' => config('app.team')
    ];

    foreach (range(1, 4) as $pass) {
        
        foreach (range(1, 8) as $skill) {

            $key = ($pass === 1) ? '' : '_' . $pass;

            $keyed['1ELEMENTS' . $key] = array_get($data, $pass . 'P - Skill ' . $skill);
            $keyed['1DD' . $key] = array_get($data, $pass . 'P - Skill ' . $skill . ' DD');
        }

        $passTotals = [1 => 'TOTAL', 2 => 'TOTAL_3', 3 => 'TOTAL_2', 4 => 'TOTAL_3'];

        $total = collect($data)
            ->filter(function ($item, $key) use ($pass) {
                return preg_match('/' . $pass . 'P - Skill \d+ DD/', $key)
                    || preg_match('/' . $pass . 'P - Bonus', $key);
            })
            ->reduce(function ($carry, $item) {
                return $carry + (float) $item;
            });

        if ($total) {
            $keyed[$passTotals[$pass]] = $total;
        }
    }

    return $keyed;
}