<?php

namespace Modules\Blog\Entities;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;
use Modules\Blog\Presenters\PostPresenter;
use Modules\Core\Traits\NamespacedEntity;
use Modules\Media\Support\Traits\MediaRelation;
use Modules\Tag\Traits\TaggableTrait;
use Modules\Media\Entities\File;

class Post extends Model
{
    use Translatable, MediaRelation, PresentableTrait, NamespacedEntity, TaggableTrait;

    protected $table = 'blog__posts';
    protected static string $entityNamespace = 'encorecms/post';

    public $translatedAttributes = [
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'og_title',
        'og_description',
        'og_image',
        'og_type',
        'translatable_options'
    ];
    protected $fillable = [
        'template',
        'options',
        'category_id',
        'user_id',
        'status',
        'created_at'
    ];

    protected $presenter = PostPresenter::class;

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array'
    ];

    public function __construct(array $attributes = [])
    {
        if (config()->has('encore.blog.config.fillable.post')) {
            $this->fillable = config('encore.blog.config.fillable.post');
        }

        parent::__construct($attributes);
    }

    /**
     * |--------------------------------------------------------------------------
     * | RELATIONS
     * |--------------------------------------------------------------------------
     */

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'blog__post_category');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        $driver = config('encore.user.config.driver');

        return $this->belongsTo("Modules\\User\\Entities\\{$driver}\\User");
    }
    public function comments()
    {
        return $this->morphMany("Modules\\Comments\\Entities\\Comment", 'commentable');
    }
    /**
     * |--------------------------------------------------------------------------
     * | MUTATORS
     * |--------------------------------------------------------------------------
     */
    public function getOptionsAttribute($value)
    {
        try {
            return json_decode(json_decode($value));
        } catch (\Exception $e) {
            return json_decode($value);
        }
    }

    public function getSecondaryImageAttribute()
    {
        $thumbnail = $this->files()->where('zone', 'secondaryimage')->first();
        if (!$thumbnail) {
            $image = [
                'mimeType' => 'image/jpeg',
                'path' => null
            ];
        } else {
            $image = [
                'mimeType' => $thumbnail->mimetype,
                'path' => $thumbnail->path_string
            ];
        }
        return json_decode(json_encode($image));
    }

    public function getMainImageAttribute()
    {
        $thumbnail = $this->files()->where('zone', 'mainimage')->first();
        if (!$thumbnail) {
            $image = [
                'mimeType' => 'image/jpeg',
                'path' => url('modules/blog/img/post/default.jpg')
            ];
        } else {
            $image = [
                'mimeType' => $thumbnail->mimetype,
                'path' => $thumbnail->path_string
            ];
        }
        return json_decode(json_encode($image));

    }

    public function getGalleryAttribute()
    {


        $gallery = $this->filesByZone('gallery')->get();
        $response = [];
        foreach ($gallery as $img) {
            array_push($response, [
                'mimeType' => $img->mimetype,
                'path' => $img->path_string
            ]);
        }

        return json_decode(json_encode($response));
    }

    /**
     * URL post
     * @return string
     */
    public function getUrlAttribute()
    {

        return \URL::route(\LaravelLocalization::getCurrentLocale() . '.blog.' . $this->category->slug . '.post', [$this->slug??'']);

    }


    /**
     * Magic Method modification to allow dynamic relations to other entities.
     * @return string
     * @var $destination_path
     * @var $value
     */
    public function __call($method, $parameters)
    {
        #i: Convert array to dot notation
        $config = implode('.', ['encore.blog.config.relations.post', $method]);

        #i: Relation method resolver
        if (config()->has($config)) {
            $function = config()->get($config);

            return $function($this);
        }

        #i: No relation found, return the call to parent (Eloquent) to handle it.
        return parent::__call($method, $parameters);
    }

}
