<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIncidentRequest extends FormRequest
{
    /**
     * التحقق مما إذا كان المستخدم مسجل الدخول.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * قواعد التحقق الخاصة بإنشاء بلاغ جديد.
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

            'category_id' => [
                'required',
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

            // إضافة قاعدة التحقق من ملف الصورة
            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:2048', // الحد الأقصى 2 ميجابايت
            ],
        ];
    }

    /**
     * رسائل الخطأ المخصصة.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Le titre du signalement est obligatoire.',
            'title.max' => 'Le titre ne doit pas dépasser 255 caractères.',

            'description.required' => 'La description est obligatoire.',

            'latitude.required' => 'La latitude est requise.',
            'latitude.numeric' => 'La latitude doit être numérique.',
            'latitude.between' => 'La latitude doit être comprise entre -90 et 90.',

            'longitude.required' => 'La longitude est requise.',
            'longitude.numeric' => 'La longitude doit être numérique.',
            'longitude.between' => 'La longitude doit être comprise entre -180 et 180.',

            'category_id.required' => 'Veuillez choisir une catégorie.',
            'category_id.exists' => 'La catégorie sélectionnée n\'existe pas.',

            'priority.in' => 'La priorité sélectionnée est invalide.',

            // إضافة رسائل الخطأ الخاصة بالصورة
            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'L’image doit être au format JPEG, PNG, JPG ou GIF.',
            'image.max' => 'L’image ne doit pas dépasser 2 Mo.',
        ];
    }
}