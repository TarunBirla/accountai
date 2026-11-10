<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc,dns', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'business_services' => ['nullable', 'array'],
            'business_services.*' => ['integer', 'exists:services,id'],
            'personal_services' => ['nullable', 'array'],
            'personal_services.*' => ['integer', 'exists:services,id'],
            // Honeypot field - must be empty
            'website_hp' => ['nullable', 'max:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $hasBusiness = !empty($this->input('business_services'));
            $hasPersonal = !empty($this->input('personal_services'));

            if (!$hasBusiness && !$hasPersonal) {
                $validator->errors()->add(
                    'services',
                    'Please select at least one Business or Personal accountancy service.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'website_hp.max' => 'Spam detected. Submission rejected.',
            'first_name.required' => 'Please enter your first name.',
            'last_name.required' => 'Please enter your last name.',
            'email.required' => 'A valid email address is required.',
            'phone.required' => 'Please provide a contact phone number.',
        ];
    }
}
