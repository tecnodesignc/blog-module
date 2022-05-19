<meta name="title" content="{{$category->meta_title}}" />
<meta name="description" content="{{$category->summary}}">
<!-- Schema.org para Google+ -->
<meta itemprop="name" content="{{$category->title}}">
<meta itemprop="description" content="{{$category->summary}}">
<meta itemprop="image" content="{{url($category->mainimage->path??'')}}">
<!-- Open Graph para Facebook-->
<meta property="og:title" content="{{$category->title}}"/>
<meta property="og:type" content="article"/>
<meta property="og:url" content="{{$category->url}}"/>
<meta property="og:image" content="{{url($category->mainimage->path??'')}}"/>
<meta property="og:description" content="{{$category->summary}}"/>
<meta property="og:site_name" content="{{Setting::get('core::site-name') }}"/>
<meta property="og:locale" content="{{config('config.oglocale')}}">
<meta property="fb:app_id" content="{{Setting::get('core::id-facebook')}}">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="{{ Setting::get('core::site-name') }}">
<meta name="twitter:title" content="{{$category->title}}">
<meta name="twitter:description" content="{{$category->summary}}">
<meta name="twitter:creator" content="{{Setting::get('core::twitter') }}">
<meta name="twitter:image:src" content="{{url($category->mainimage->path??'')}}">
