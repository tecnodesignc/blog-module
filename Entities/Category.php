<?php

namespace Modules\Blog\Entities;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laracasts\Presenter\PresentableTrait;
use Modules\Core\Traits\NamespacedEntity;
use Modules\Media\Support\Traits\MediaRelation;
use Modules\Media\Entities\File;

class Category extends Model
{
    use Translatable, MediaRelation, PresentableTrait, NamespacedEntity;

    protected $table = 'blog__categories';
    protected static string $entityNamespace = 'blog/category';
    public $translatedAttributes = [
        'title',
        'description',
        'slug',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'translatable_options'
    ];
    protected $fillable = [
        'template',
        'parent_id',
        'options'
    ];

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
        if (config()->has('encore.blog.config.fillable.category')) {
            $this->fillable = config('encore.blog.config.fillable.category');
        }
        parent::__construct($attributes);
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    /**
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * @return BelongsToMany
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'blog__post_category')->as('posts')->with('category');
    }

    /*
     * |--------------------------------------------------------------------------
     * | MUTATORS
     * |--------------------------------------------------------------------------
     */

    /**
     * @param $value
     * @return mixed
     */
    public function getOptionsAttribute($value): mixed
    {
        try {
            return json_decode(json_decode($value));
        } catch (\Exception $e) {
            return json_decode($value);
        }
    }

    /**
     * @return mixed
     */
    public function getSecondaryImageAttribute(): mixed
    {
        $thumbnail = $this->files()->where('zone', 'secondaryimage')->first();
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

    /**
     * @return mixed
     */
    public function getMainImageAttribute(): mixed
    {
        $thumbnail = $this->files()->where('zone', 'mainimage')->first();
        if (!$thumbnail) {
            if (isset($this->options->mainimage)) {
                $image = [
                    'mimeType' => 'image/jpeg',
                    'path' => url($this->options->mainimage)
                ];
            } else {
                $image = [
                    'mimeType' => 'image/jpeg',
                    'path' => url('modules/blog/img/post/default.jpg')
                ];
            }
        } else {
            $image = [
                'mimeType' => $thumbnail->mimetype,
                'path' => $thumbnail->path_string
            ];
        }
        return json_decode(json_encode($image));

    }

    /**
     * @return mixed
     */
    public function getUrlAttribute(): mixed
    {

        return \URL::route(\LaravelLocalization::getCurrentLocale() . '.blog.category.' . $this->slug);

    }

    /**
     * |--------------------------------------------------------------------------
     * | SCOPES
     * |--------------------------------------------------------------------------
     */
    public function scopeFirstLevelItems($query)
    {
        return $query->where('depth', '1')
            ->orWhere('depth', null)
            ->orderBy('lft', 'ASC');
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
        $config = implode('.', ['ecore.blog.config.relations.category', $method]);

        #i: Relation method resolver
        if (config()->has($config)) {
            $function = config()->get($config);
            $bound = $function->bindTo($this);

            return $bound();
        }

        #i: No relation found, return the call to parent (Eloquent) to handle it.
        return parent::__call($method, $parameters);
    }

}
