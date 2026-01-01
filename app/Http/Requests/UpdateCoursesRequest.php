<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCoursesRequest extends FormRequest
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
             'title'=>['required', 'string', 'max:255'],
            'image_url'=>['required'],
            'description'=>['required', 'string'],
            'level'=>['required','max:255'],
            'videos'=>['required'],
            'requirements'=>['required'],
            'rating'=>['nullable','max:255'],
            'duration'=>['nullable','max:255'],
            'category_id' => 'required|exists:categories,id'
        ];
    }
}
