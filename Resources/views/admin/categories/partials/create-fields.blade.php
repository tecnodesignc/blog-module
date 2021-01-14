<div class="box-body">
    <div class='form-group{{ $errors->has("{$lang}.title") ? ' has-error' : '' }}'>
        {!! Form::label("{$lang}[title]", trans('blog::category.form.title')) !!}
        {!! Form::text("{$lang}[title]", old("{$lang}.title"), ['class' => 'form-control', 'data-slug' => 'source', 'placeholder' => trans('blog::category.form.title')]) !!}
        {!! $errors->first("{$lang}.title", '<span class="help-block">:message</span>') !!}
    </div>
    <div class='form-group{{ $errors->has("{$lang}.description") ? ' has-error' : '' }}'>
        @editor('description', trans('blog::category.form.description'), old("{$lang}.description"), $lang)
    </div>


    @if (config('encore.blog.config.fields.category.partials.translatable.create') && config('encore.blog.config.fields.category.partials.translatable.create') !== [])
        @foreach (config('encore.blog.config.fields.category.partials.translatable.create') as $partial)
            @include($partial)
        @endforeach
    @endif
</div>
