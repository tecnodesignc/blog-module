<?php

namespace Modules\Blog\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Blog\Events\PostContentIsRendering;
use Cviebrock\EloquentSluggable\Sluggable;

class PostTranslation extends Model
{
    use Sluggable;

    public $timestamps = false;


    protected $fillable = [
        'title',
        'slug',
        'summary',
        'content',
        'meta_title',
        'meta_description',
        'og_title',
        'og_description',
        'og_image',
        'og_type',
        'meta_keywords',
        'translatable_options'
    ];
    protected $table = 'blog__post_translations';


    protected $casts = [
        'translatable_options' => 'array'
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getTranslatableOptionAttribute($value): mixed
    {

        $options=json_decode($value);
        return $options;


    }
    /**
     * @return mixed
     */
    public function getMetaDescriptionAttribute(): mixed
    {

        return $this->meta_description ?? $this->summary;
    }

    /**
     * @return mixed
     */
    public function getMetaTitleAttribute(): mixed
    {

        return $this->meta_title ?? $this->title;
    }

    /**
     * @param $content
     * @return string
     */
    public function getContentAttribute($content): string
    {
        event($event = new PostContentIsRendering($content));

        return $event->getContent();
    }

}
