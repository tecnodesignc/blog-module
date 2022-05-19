@extends('layouts.master')

@section('title')
    {{ $post->title }} | @parent
@stop
@section('meta')
    @include('blog.scripts.metas-post')
@endsection
@section('content')
    <section class="seomun_breadcrumb"
             style="background-image: url({{$category->mainimage->path??Theme::url('images/breadcrumb_bg.jpg')}});">
        <div class="seomun_overlay"></div>
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="breadcrumb_title">
                        <span class="seomun_span">{{$category->parent->title??'Noticias'}}</span>
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
                            <li><a href="{{$category->url}}">{{$category->title}}</a></li>
                            <li><a class="active" href="{{$post->url}}">{{$post->title}}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- End seomun_breadcrumb section -->


    <!-- Start seomun_single_blog section -->
    <section class="seomun_single_blog single_blog section_padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="single_blog_content">
                        <div class="single_content_box">
                            <div class="seomun_img_box">
                                <figure>
                                    <img src="{{$post->mainImage->path}}" class="img-fluid"
                                         alt="{{$post->title}}">
                                    <figcaption style="visibility: hidden">{{$post->title}}</figcaption>
                                </figure>
                            </div>
                            <div class="post_meta">
                                <ul>
                                    <li>
                                        <address><span><i class="far fa-user"></i>Por <a
                                                        href="#">{{$post->user->present()->fullName()}}</a></span>
                                        </address>
                                    </li>
                                    <li><span><i class="far fa-comments"></i><a
                                                    href="#">{{count($post->comments)}} {{trans('comments::comments.title.comments')}}</a></span>
                                    </li>
                                    <li>
                                        <div
                                                class="fb-like"
                                                data-share="true"
                                                data-width="450"
                                                data-show-faces="true">
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <h1>{{$post->title}}</h1>

                            <!-- The date and time when your article was originally published -->
                            <time class="op-published" datetime="{{$post->created_at->toAtomString()}}"
                                  style="visibility: hidden">{{$post->created_at->toRfc1123String()}}</time>

                            <!-- The date and time when your article was last updated -->
                            <time class="op-modified" dateTime="{{$post->updated_at->toAtomString()}}"
                                  style="visibility: hidden">{{$post->updated_at->toRfc1123String()}}</time>


                            {!! Adsense::render('728x90') !!}
                            <div class="seomun_content_box">
                                @php
                                    $paragraphs= explode( '</p>', $post->content );
                                    $cont=0;
                                @endphp

                                @foreach($paragraphs as $i=>$p)
                                {!! $p !!}</p>
                                @if(($i+1)%5==0)
                                    {!!Adsense::render('728x90')!!}
                                @endif
                                @endforeach
                            </div>
                        </div>
                        <div class="content_share_area">
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="tags_area">
                                        <h4>{{trans('tag::tags.tags')}}</h4>
                                        <ul class="tags_list">
                                            @foreach($tags as $i=>$tag)
                                                <li><a href="{{$tag->url}}">{{$tag->name}}</a></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="tags_area text-right">
                                        <h4>Comparta en Redes</h4>

                                        <ul class="social_link_2">
                                            <li><a href="#" target="_blank"
                                                   onclick="window.open('http://www.facebook.com/sharer.php?u={{$post->url }}','Facebook','width=600,height=300,left='+(screen.availWidth/2-300)+',top='+(screen.availHeight/2-150)+'')"
                                                   title="Facebook"><i class="fab fa-facebook-f"></i></a></li>
                                            <li><a href="#" target="_blank"
                                                   onclick="window.open('http://twitter.com/share?url={{ $post->url }}','Twitter share','width=600,height=300,left='+(screen.availWidth/2-300)+',top='+(screen.availHeight/2-150)+'')"
                                                   title="Twitter"><i class="fab fa-twitter"></i></a></li>
                                            <li><a href="#" target="_blank"
                                                   onclick="window.open('http://www.linkedin.com/shareArticle?mini=true&amp;url={{ $post->url }}','Linkedin','width=863,height=500,left='+(screen.availWidth/2-431)+',top='+(screen.availHeight/2-250)+'')"
                                                   title="Linkedin"><i class="fab fa-linkedin"></i></a></li>
                                            <li><a href="#" target="_blank" href="whatsapp://send?text={{$post->url}}"
                                                   data-action="share/whatsapp/share" title="Whatsapp"><i
                                                            class="fab fa-whatsapp"></i></a></li>
                                            {{-- <li><a href="#"><i class="fab fa-tumblr"></i></a></li>--}}
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="prev_next_area">
                            <div class="row">
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    @if ($previous = $post->present()->previous)
                                        <div class="prev_next_text ">
                                            <a href="{{$previous->url}}">{{trans('blog::posts.button.previous')}}</a>
                                            <a href="{{$previous->url}}"><h4>{{$previous->title}}</h4></a>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-lg-4">
                                    <div class="icon_box">
                                        <img src="assets/images/icon_4.png" alt="">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    @if ($next = $post->present()->next)
                                        <div class="prev_next_text text-right">
                                            <a href="{{$next->url}}">{{trans('blog::posts.button.next')}}</a>
                                            <a href="{{$next->url}}"><h4>{{$next->title}}</h4></a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="blog_admin">
                            <div class="row">
                                <div class="col-lg-12 justify-content-center">
                                    <div class="about_admin_area text-center">
                                        <div class="admin_images">
                                            <img src="{{$post->user->present()->gravatar()??''}}" class="images-fluid"
                                                 alt="{{$post->user->present()->fullName()}}">
                                        </div>
                                        <div class="admin_bio">
                                            <h4>{{$post->user->present()->fullName()}}</h4>
                                            <ul class="social_link_2">
                                                <li><a href="{{$post->user->fields->facebbok??'#'}}"><i
                                                                class="fab fa-facebook-f"></i></a></li>
                                                <li><a href="{{ $post->user->fields->twitter??'#' }}"><i
                                                                class="fab fa-twitter"></i></a></li>
                                                {{--  <li><a href="{{$post->user->fields->linkedin}}"><i class="fab fa-behance"></i></a></li>--}}
                                                <li><a href="{{$post->user->fields->instagram??'#'}}"><i
                                                                class="fab fa-instagram"></i></a></li>
                                                <li><a href="{{$post->user->fields->linkedin??'#'}}"><i
                                                                class="fab fa-linkedin-in"></i></a></li>
                                            </ul>
                                            <p>{{$post->user->fields->bio??''}} </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="comment_area">
                            <div class="comment_title">
                                <h4>Commentarios</h4>
                            </div>
                            <div class="fb-comments"
                                 data-href="https://www.tecnodesign.com.co/noticias/que-es-la-capacitacion-de-personal"
                                 data-width="" data-numposts="5"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- End seomun_single_blog section -->
@stop
@section('scripts')
    @parent
    <script type="text/javascript">
        (function ($) {
            let content = $('.single_content_box blockquote').html()
            $('.single_content_box blockquote').html('<div class="qoute_icon"><img src="{{Theme::url('images/qoute.png')}}" alt="blockquote"></div><div class="qoute_text">' + content + '</div>')
            $('.single_content_box blockquote').addClass('seomun_blockquote gray_bg')
        })(window.jQuery);
    </script>
    @include('blog.scripts.scripts-post')
    <style>
        .seomun_content_box .seomun_btn{
            padding: 10px 43px;
        }
    </style>
@stop
