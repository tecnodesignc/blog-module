<?php

namespace Modules\Blog\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class UpdateCategoryRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'template' => 'required'
        ];
    }

    public function translationRules()
    {
        return [
            'title' => 'required|min:2'
        ];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'template.required' => trans('blog::common.messages.template is required'),
        ];
    }

    public function translationMessages()
    {
        return [
            'title.required' => trans('blog::common.messages.title is required'),
            'title.min:2'=> trans('blog::common.messages.title min 2 '),
        ];
    }
}
