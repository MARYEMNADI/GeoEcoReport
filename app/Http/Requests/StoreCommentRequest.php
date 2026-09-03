<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    /**
     * التحقق من أن المستخدم مسجل الدخول.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * قواعد التحقق الخاصة بإضافة تعليق.
     */
    public function rules(): array
    {
        return [
            'content' => [
                'required',
                'string',
                'min:2',
                'max:1000',
            ],
        ];
    }

    /**
     * رسائل الخطأ المخصصة.
     */
    public function messages(): array
    {
        return [
            'content.required' => 'Le commentaire est obligatoire.',
            'content.string' => 'Le commentaire doit être un texte.',
            'content.min' => 'Le commentaire doit contenir au moins 2 caractères.',
            'content.max' => 'Le commentaire ne doit pas dépasser 1000 caractères.',
        ];
    }
}