<div id="fb-root"></div>
<script async defer crossorigin="anonymous"
        src="https://connect.facebook.net/es_LA/sdk.js#xfbml=1&version=v4.0&appId={!!Setting::get('core::id-facebook')!!}&autoLogAppEvents=1"></script>

<script>(function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/es_LA/sdk.js#xfbml=1&version=v2.8";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>

<script type="application/ld+json">
{
	"@context": "http://schema.org",
	"@type": "BlogPosting",{{--NewsArticle--}}
    "@id":"{{$post->url}}",
	"mainEntityOfPage": {
		"@type": "WebPage",
		"@id": "{{$post->url}}"
	},
	"headline": "{{$post->title}}",
	"description": "{{$post->summary}}",
	"image": {
			"@type": "ImageObject",
			"url": "{{$post->mainImage->path}}"
		},
	"datePublished": "{{$post->created_at}}",
	"dateModified": "{{$post->updated_at}}",
	"articleBody":"{{$post->summary}}",
	"author": {
		"@type": "Person",
		"name": "{{$post->user->present()->fullName()}}"
	},
	"publisher": {
		"@type": "Organization",
		"name": "@setting('core::site-name')",
		"logo": {
			"@type": "ImageObject",
			"url": "{{$post->mainImage->path}}"
		}
	}
}
</script >
