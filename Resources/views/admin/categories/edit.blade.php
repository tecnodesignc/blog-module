@extends('layouts.master')

@section('content-header')
  <h1>
    {{ trans('blog::category.title.edit category') }}
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{ route('dashboard.index') }}"><i
          class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
    <li><a href="{{ route('admin.blog.category.index') }}">{{ trans('blog::category.title.categories') }}</a></li>
    <li class="active">{{ trans('blog::category.title.edit category') }}</li>
  </ol>
@stop

@section('content')
  {!! Form::open(['route' => ['admin.blog.category.update', $category->id], 'method' => 'put']) !!}
  <div class="row">
    <div class="col-xs-12 col-md-9">
      <div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                  class="fa fa-minus"></i>
              </button>
            </div>
            <div class="nav-tabs-custom">
              @include('partials.form-tab-headers')
              <div class="tab-content">
                  <?php $i = 0; ?>
                @foreach (LaravelLocalization::getSupportedLocales() as $locale => $language)
                      <?php $i++; ?>
                  <div class="tab-pane {{ locale() == $locale ? 'active' : '' }}" id="tab_{{ $i }}">
                    @include('blog::admin.categories.partials.edit-fields', ['lang' => $locale])
                  </div>
                @endforeach

              </div>
            </div> {{-- end nav-tabs-custom --}}
          </div>
        </div>
        @if (config('encore.blog.config.fields.category.partials.normal.edit') && config('encore.blog.config.fields.category.partials.normal.edit') !== [])

          <div class="col-xs-12 ">
            <div class="box box-primary">
              <div class="box-header">
              </div>
              <div class="box-body ">
                @foreach (config('encore.blog.config.fields.category.partials.normal.edit') as $partial)
                  @include($partial)
                @endforeach

              </div>
            </div>
          </div>
        @endif
        <div class="col-xs-12 ">
          <div class="box box-primary">
            <div class="box-header">
            </div>
            <div class="box-body ">
              <div class="box-footer">
                <button type="submit"
                        class="btn btn-primary btn-flat">{{ trans('core::core.button.update') }}</button>
                <a class="btn btn-danger pull-right btn-flat"
                   href="{{ route('admin.blog.category.index')}}">
                  <i class="fa fa-times"></i> {{ trans('core::core.button.cancel') }}</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xs-12 col-md-3">
      <div class="row">
        <div class="col-xs-12 ">
          <div class="box box-primary">
            <div class="box-header">
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                    class="fa fa-minus"></i>
                </button>
              </div>
              <div class="form-group">
                <label>{{trans('blog::category.form.parent category')}}</label>
              </div>
            </div>
            <div class="box-body">
              <select class="form-control" name="parent_id" id="parent_id">
                <option value="0">-</option>
                @foreach ($categories as $parent)
                  <option
                    value="{{$parent->id}}" {{ old('parent_id', $parent->id) == $category->parent_id ? 'selected' : '' }}> {{$parent->title}}
                  </option>
                @endforeach
              </select><br>
                <hr/>
                <div class='form-group{{ $errors->has("template") ? ' has-error' : '' }}'>
                    {!! Form::label("template", trans('blog::posts.form.template')) !!}
                    {!! Form::select("template", $all_templates_category, old("template", $category->template), ['class' => "form-control", 'placeholder' => trans('blog::posts.form.template')]) !!}
                    {!! $errors->first("template", '<span class="help-block">:message</span>') !!}
                </div>
                <hr>
            </div>
          </div>
        </div>
        <div class="col-xs-12 ">
          <div class="box box-primary">
            <div class="box-header">
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                    class="fa fa-minus"></i>
                </button>
              </div>
              <div class="form-group">
                <label>{{trans('blog::category.form.image')}}</label>
              </div>
            </div>
            <div class="box-body">
              <div class="tab-content">
                @mediaSingle('mainimage',$category)
              </div>
            </div>
          </div>
        </div>
        @if(config('encore.blog.config.fields.category.secondaryImage'))
          <div class="col-xs-12 ">
            <div class="box box-primary">
              <div class="box-header">
                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                      class="fa fa-minus"></i>
                  </button>
                </div>
                <div class="form-group">
                  <label>{{trans('blog::category.form.secondary image')}}</label>
                </div>
              </div>
              <div class="box-body">
                <div class="tab-content">
                  @mediaSingle('secondaryimage',$category)
                </div>
              </div>
            </div>
          </div>
        @endif
      </div>
    </div>

  </div>
  {!! Form::close() !!}
@stop

@section('footer')
  <a data-toggle="modal" data-target="#keyboardShortcutsModal"><i class="fa fa-keyboard-o"></i></a> &nbsp;
@stop
@section('shortcuts')
  <dl class="dl-horizontal">
    <dt><code>b</code></dt>
    <dd>{{ trans('core::core.back to index') }}</dd>
  </dl>
@stop

@push('js-stack')
  <script type="text/javascript">
      $(document).ready(function () {
          $(document).keypressAction({
              actions: [
                  {key: 'b', route: "<?= route('admin.blog.category.index') ?>"}
              ]
          });
      });
  </script>
  <script>
      $(document).ready(function () {
          $('input[type="checkbox"], input[type="radio"]').iCheck({
              checkboxClass: 'icheckbox_flat-blue',
              radioClass: 'iradio_flat-blue'
          });
          $('.btn-box-tool').click(function (e) {
              e.preventDefault();
          });


      });

  </script>
  <style>

    .nav-tabs-custom > .nav-tabs > li.active {
      border-top-color: white !important;
      border-bottom-color: #3c8dbc !important;
    }

    .nav-tabs-custom > .nav-tabs > li.active > a, .nav-tabs-custom > .nav-tabs > li.active:hover > a {
      border-left: 1px solid #e6e6fd !important;
      border-right: 1px solid #e6e6fd !important;

    }


  </style>
@endpush
