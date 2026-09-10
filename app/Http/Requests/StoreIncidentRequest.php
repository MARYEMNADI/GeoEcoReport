<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIncidentRequest extends FormRequest
{
    /**
     * Vérifier si l'utilisateur est authentifié.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Règles de validation.
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
                'required',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'required',
                'numeric',
                'between:-180,180',
            ],

            /*
             * La catégorie est maintenant OPTIONNELLE.
             *
             * Si l'utilisateur ne la choisit pas,
             * GeoEco Assistant va la déterminer automatiquement.
             */
            'category_id' => [
                'nullable',
                'integer',
                'exists:categories,id',
            ],

            'priority' => [
                'nullable',
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
                'mimes:jpeg,jpg,png,webp',
                'max:5120',
            ],
        ];
    }

    /**
     * Messages personnalisés.
     */
    public function messages(): array
    {
        return [
            'title.required' =>
                'Le titre du signalement est obligatoire.',

            'title.max' =>
                'Le titre ne doit pas dépasser 255 caractères.',

            'description.required' =>
                'La description est obligatoire.',

            'latitude.required' =>
                'La latitude est requise.',

            'latitude.numeric' =>
                'La latitude doit être numérique.',

            'latitude.between' =>
                'La latitude doit être comprise entre -90 et 90.',

            'longitude.required' =>
                'La longitude est requise.',

            'longitude.numeric' =>
                'La longitude doit être numérique.',

            'longitude.between' =>
                'La longitude doit être comprise entre -180 et 180.',

            'category_id.integer' =>
                'La catégorie sélectionnée est invalide.',

            'category_id.exists' =>
                'La catégorie sélectionnée n\'existe pas.',

            'priority.in' =>
                'La priorité sélectionnée est invalide.',

            'image.image' =>
                'Le fichier doit être une image.',

            'image.mimes' =>
                'L’image doit être au format JPG, JPEG, PNG ou WEBP.',

            'image.max' =>
                'L’image ne doit pas dépasser 5 Mo.',

            'image.uploaded' =>
                'Le téléchargement de l’image a échoué. Vérifiez sa taille et réessayez.',
        ];
    }
}