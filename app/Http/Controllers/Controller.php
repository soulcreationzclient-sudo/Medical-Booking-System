<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Str;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public function uniquecode($model)
    {
        $modelClass = match ($model) {
            'user' => \App\Models\User::class,
            'hospital' => \App\Models\Hospital::class,
            'doctor' => \App\Models\User::class,
            default => throw new \InvalidArgumentException('Invalid model type'),
        };
        if ($model == 'user') {
            do {
                $code = Str::upper(Str::random(20));
            } while ($modelClass::where('api_code', $code)->exists());
        }
        if ($model == 'hospital') {
            do {
                $code = Str::upper(Str::random(20));
            } while ($modelClass::where('hospital_code', $code)->exists());
        }


        return $code;
    }
    public function doctorcode()
    {
        do {
            $code = Str::upper(Str::random(20));
        } while (
            Doctor::where('doctor_code', $code)
            ->where('hospital_id', auth()->user()->hospital_id)
            ->exists()
        );

        return $code;
    }
}
