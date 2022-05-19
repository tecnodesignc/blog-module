<?php

namespace Modules\Blog\Entities;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class CategoryTranslation extends Model
{
    use Sluggable;

    public $timestamps = false;
    protected $fillable = ['title', 'description', 'slug', 'meta_title', 'meta_description', 'meta_keywords', 'translatable_options'];
    protected $table = 'blog__category_translations';
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

    public function getTranslatableOptionAttribute($value): mixed
    {

        $options=json_decode($value);
        return $options;

    }

    /**
     * @return mixed
     */
    public function getMetaTitleAttribute(): mixed
    {

        return $this->meta_title ?? $this->title;
    }
    /**
     * @return mixed
     */
    public function getMetaDescriptionAttribute(): mixed
    {

        return $this->meta_description ?? substr(strip_tags($this->description??''),0,150);
    }


}
