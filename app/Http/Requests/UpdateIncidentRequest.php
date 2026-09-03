<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIncidentRequest extends FormRequest
{
    /**
     * التحقق مما إذا كان المستخدم مصرحاً له بإجراء الطلب.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * قواعد التحقق الخاصة بتحديث البلاغ.
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
            ],

            'latitude' => [
                'nullable',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'nullable',
                'numeric',
                'between:-180,180',
            ],

            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],

            'priority' => [
                'required',
                Rule::in([
                    'Faible',
                    'Moyenne',
                    'Élevée',
                    'Urgente',
                ]),
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048', // 2MB كحد أقصى
            ],
        ];
    }

    /**
     * الرسائل المخصصة لأخطاء التحقق.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Le titre du signalement est obligatoire.',
            'title.max' => 'Le titre ne doit pas dépasser 255 caractères.',

            'description.required' => 'La description est obligatoire.',

            'latitude.numeric' => 'La latitude doit être numérique.',
            'latitude.between' => 'La latitude doit être comprise entre -90 et 90.',

            'longitude.numeric' => 'La longitude doit être numérique.',
            'longitude.between' => 'La longitude doit être comprise entre -180 et 180.',

            'category_id.required' => 'Veuillez choisir une catégorie.',
            'category_id.exists' => 'La catégorie sélectionnée n\'existe pas.',

            'priority.required' => 'La priorité est obligatoire.',
            'priority.in' => 'La priorité sélectionnée est invalide.',

            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'L\'image doit être au format: jpeg, png, jpg, webp.',
            'image.max' => 'L\'image ne doit pas dépasser 2 Mo.',
        ];
    }
}