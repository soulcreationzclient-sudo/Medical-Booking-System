<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Inpersonrequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'patient_name'=>'required|string',
            'patient_phone'=>'required',
            'doctor_id'=>'required',
            'age'=>'required',
            'cause'=>'nullable|string',
            'booking_date'=>'required|date',
            'start_time'=>'required',
        ];
    }
}