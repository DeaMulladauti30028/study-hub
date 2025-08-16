<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContributionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'   => ['required', 'string', 'max:120'],
            'content' => ['nullable', 'string', 'max:20000', 'required_without:file'],
            'file'    => ['nullable', 'file', 'required_without:content', 'max:10240',
                'mimetypes:image/*,application/pdf,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        ];
    }
    public function messages(): array
    {
        return [
            'content.required_without' => 'Add some text or upload a file.',
            'file.required_without'    => 'Upload a file or write some text.',
        ];
    }
}
