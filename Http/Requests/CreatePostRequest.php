<?php

namespace Modules\Blog\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class CreatePostRequest extends BaseFormRequest
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
            'title' => 'required|min:2',
            'summary'=>'required|min:5',
            'content' => 'required|min:150'
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
            'summary.required'=> trans('blog::common.messages.summary is required'),
            'summary.min:5'=> trans('blog::common.messages.summary min 2 '),
            'content.required'=> trans('blog::common.messages.description is required'),
            'content.min:150'=> trans('blog::common.messages.description min 2 '),
        ];
    }
}
