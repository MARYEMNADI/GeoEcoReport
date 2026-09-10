<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIncidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

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
                'required',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'required',
                'numeric',
                'between:-180,180',
            ],

            'category_id' => [
               'nullable',
                'exists:categories,id',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:5120',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'L’image doit être au format JPG, JPEG, PNG ou WEBP.',
            'image.max' => 'L’image ne doit pas dépasser 5 Mo.',
            'image.uploaded' => 'Le téléchargement de l’image a échoué. Vérifiez sa taille et réessayez.',
        ];
    }
}