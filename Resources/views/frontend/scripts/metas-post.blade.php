<meta name="title" content="{{$post->meta_title??$post->title}} - @setting('core::site-name')" />
<meta name="description" content="{{$post->meta_description??$post->summary}}">
<meta name="author" content="{{$post->user->present()->fullName()}}">
<meta name="keywords" content="{{$post->meta_keywords?? setting('core::site-description').$post->title}}">
<meta name="genre" content="news">

<!-- Schema.org para Google+ -->
<meta itemprop="name" content="{{$post->meta_title??$post->title}} -  @setting('core::site-name')">
<meta itemprop="description" content="{{$post->meta_description??$post->summary}}">
<meta itemprop="image" content="{{$post->mainimage->path}}">
<!-- Open Graph para Facebook-->
<meta property="og:site_name" content="TECNODESIGN.COM.CO">
<meta property="og:title" content="{{$post->meta_title??$post->title}}"/>
<meta property="og:type" content="article"/>
<meta property="og:url" content="{{$post->url}}"/>
<meta property="og:locale" content="{{LaravelLocalization::getCurrentLocale()=='es'?'ES_la':'EN_us'}}"/>
<meta property="og:image" content="{{$post->mainimage->path}}"/>
<meta property="og:description" content="{{$post->meta_description??$post->summary}}"/>
<meta property="og:site_name" content="{{Setting::get('core::site-name') }}"/>
<meta property="og:locale" content="{{config('config.oglocale')}}">
<meta property="fb:app_id" content="{{Setting::get('core::id-facebook')}}">
<meta property="fb:pages" content="327593297303354">
<meta property="op:markup_version" content="v1.0">
<meta property="og:country_name" content="Colombia">


<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="{{ Setting::get('core::site-name') }}">
<meta name="twitter:title" content="{{$post->meta_title??$post->title}}">
<meta name="twitter:description" content="{{$post->meta_description??$post->summary}}">
<meta name="twitter:creator" content="{{Setting::get('core::twitter') }}">
<meta name="twitter:image:src" content="{{$post->mainimage->path}}">

<meta property="article:author" content="{{$post->user->present()->fullName()}}">
<meta property="article:section" content="{{$category->title}}">
<meta property="article:published_time" content="{{$post->created_at}}">
<meta name="article:modified_time" content="{{$post->updated_at}}">
