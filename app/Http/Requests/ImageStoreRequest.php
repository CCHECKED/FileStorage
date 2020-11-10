<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImageStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'images' => [
                'required',
                'array',
            ],
            'images.*.file' => [
//                'required_without_all:images.*.url,images.*.base64',
                'image'
            ],
            'images.*.base64' => [
//                'required_without_all:images.*.file,images.*.url',
                'base64image'
            ],
            'images.*.url' => [
//                'required_without_all::images.*.file,images.*.base64',
                'url'
            ]
        ];
    }
}
