@extends('layouts.master')

@section('title')
    {{$category->title}} | @parent
@stop
@section('meta')
    @include('blog.scripts.metas-category')
@endsection
@section('content')
    <section class="seomun_breadcrumb"
             style="background-image: url({{$category->mainimage->path??Theme::url('images/breadcrumb_bg.jpg')}});">
        <div class="seomun_overlay"></div>
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="breadcrumb_title">
                        <span class="seomun_span">{{$category->parent->title??'Digital agency'}}</span>
                        <h2>{{$category->title}}</h2>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="breadcrumb_link">
                        <ul>
                            <li><a href="{{url('/')}}">Home</a></li>
                            @if($category->parent_id)
                                <li><a href="{{$category->parent->url}}">{{$category->parent->title}}</a></li>
                            @endif
                            <li><a class="active" href="#">{{$category->title}}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- End seomun_breadcrumb section -->

    <section class="blog_standard seomun_blog_standard section_padding">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sidebar_widget_box" style="padding:0 25px">
                        {!!Adsense::render('160x600')!!}
                    </div>
                </div>
                <div class="col-lg-8">
                    @if ($posts->count())
                        <div class="blog_standard_main">
                            <div class="row">
                                @foreach ($posts as $index=>$post)
                                    <div class="col-lg-12">
                                        <div class="seomun_box blog_box">
                                            <a href="{{$post->url}}">
                                                <div class="seomun_img_box">
                                                    <img src="{{$post->mainImage->path}}" class="img-fluid"
                                                         alt="{{$post->title}}">
                                                </div>
                                            </a>
                                            <div class="seomun_info blog_info">
                                                @foreach ($post->tags as $i=>$tag)  <span
                                                        class="tag">{{$tag->name}}</span>  @endforeach
                                                <h3><a href="{{$post->url}}">{{$post->title}}</a></h3>
                                                <p>{{$post->summary}} </p>
                                            </div>

                                            <div class="post_meta">
                                                <ul>
                                                    <li><span><i class="far fa-comments"></i></span><a href="#">33
                                                            Comments</a></li>
                                                    <li><span><i class="far fa-user"></i> Por</span> <a
                                                                href="#">{{$post->user->present()->fullName()}}</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    @if(($index+1)%2==0)
                                        <div class="sidebar_widget_box" style="padding:0 25px">
                                            {!!Adsense::render('728x90','adsense::frontend.bootstrap.space')!!}
                                        </div>
                                    @endif

                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="seomun_pagination">
                        {{$posts->links('blog.pagination.pagination')}}
                    </div>
                </div>
            </div>
        </div>
    </section><!-- End blog_standard section  -->
@stop
@section('scripts')
    @parent
    {{-- @include('blog.category.scripts')--}}
@stop


