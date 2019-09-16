<?php

namespace N1ebieski\ICore\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentTaggable\Taggable;
use N1ebieski\ICore\Traits\FullTextSearchable;
use N1ebieski\ICore\Traits\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Carbon\Carbon;
use Mews\Purifier\Facades\Purifier;
use N1ebieski\ICore\Models\Category\Category;
use N1ebieski\ICore\Services\TagService;
use N1ebieski\ICore\Services\PostService;
use N1ebieski\ICore\Cache\PostCache;
use N1ebieski\ICore\Repositories\PostRepo;

/**
 * [Post description]
 */
class Post extends Model
{
    use Sluggable, Taggable, FullTextSearchable, Filterable;

    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'content_html',
        'content',
        'seo_title',
        'seo_desc',
        'seo_noindex',
        'seo_nofollow',
        'status',
        'comment',
        'published_at'
    ];

    /**
     * The columns of the full text index
     *
     * @var array
     */
    protected $searchable = [
        'title',
        'content'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'comment' => 1,
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value)
    {
        return $this->where('id', $value)
            ->with([
                'categories' => function($query) {
                    $query->withAncestorsExceptSelf();
                },
            ])->first() ?? abort(404);
    }

    // Overrides

    /**
    * Override metody z paczki Taggable bo ma hardcodowane nazwy tabel w SQL
     *
     * @param int|null $limit
     * @param int $minCount
     *
     * @return array
     */
    public function popularTags(int $limit = null, int $minCount = 1): array
    {
        $tags = app(TagService::class)->getPopularTags($limit, static::class, $minCount);

        return $tags->shuffle()->all();
    }

    /**
     * Override relacji tags, bo ma hardcodowane nazwy pól
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tags(): MorphToMany
    {
        $model = config('taggable.model');
        return $this->morphToMany($model, 'model', 'tags_models', 'model_id', 'tag_id')
            ->withTimestamps();
    }

    // Relations

    /**
     * [categories description]
     * @return [type] [description]
     */
    public function categories()
    {
        return $this->morphToMany('N1ebieski\ICore\Models\Category\Category', 'model', 'categories_models', 'model_id', 'category_id');
    }

    /**
     * [user description]
     * @return [type] [description]
     */
    public function user()
    {
        return $this->belongsTo('N1ebieski\ICore\Models\User');
    }

    /**
     * [comments description]
     * @return [type] [description]
     */
    public function comments()
    {
        return $this->morphMany('N1ebieski\ICore\Models\Comment\Comment', 'model');
    }

    // Accessors

    /**
     * [getModelTypeAttribute description]
     * @return string [description]
     */
    public function getModelTypeAttribute() : string
    {
        return get_class($this);
    }

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliSelfAttribute() : string
    {
        return 'post';
    }

    /**
     * [getCreatedAtDiffAttribute description]
     * @return string [description]
     */
    public function getCreatedAtDiffAttribute() : string
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    /**
     * [getUpdatedAtDiffAttribute description]
     * @return string [description]
     */
    public function getUpdatedAtDiffAttribute() : string
    {
        return Carbon::parse($this->updated_at)->diffForHumans();
    }

    /**
     * [getPublishedAtDiffAttribute description]
     * @return string [description]
     */
    public function getPublishedAtDiffAttribute() : string
    {
        return ($this->published_at != null) ? Carbon::parse($this->published_at)->diffForHumans() : '';
    }

    /**
     * [getContentHtmlAttribute description]
     * @return string [description]
     */
    public function getContentHtmlAttribute() : string
    {
         return Purifier::clean($this->attributes['content_html']);
    }

    /**
     * [getMetaTitleAttribute description]
     * @return string [description]
     */
    public function getMetaTitleAttribute() : string
    {
         return (!empty($this->attributes['seo_title'])) ? $this->attributes['seo_title'] : $this->title;
    }

    /**
     * [getMetaDescAttribute description]
     * @return string [description]
     */
    public function getMetaDescAttribute() : string
    {
         return (!empty($this->attributes['seo_desc'])) ? $this->attributes['seo_desc'] : $this->shortContent;
    }

    /**
     * Short content used in the listing
     * @return string [description]
     */
    public function getShortContentAttribute() : string
    {
        return substr($this->content, 0, 300);
    }

    /**
     * Full content without more link
     * @return string [description]
     */
    public function getNoMoreContentHtmlAttribute() : string
    {
        return str_replace('[more]', '', $this->content_html);
    }

    /**
     * Content to the point of more link
     * @return string [description]
     */
    public function getLessContentHtmlAttribute() : string
    {
        $cut = explode('<p>[more]</p>', $this->content_html);

        return (!empty($cut[1])) ? $cut[0] . '<a href="' . route('web.post.show', [
                'post' => $this->slug,
                '#more'
            ]) . '">' . trans('icore::posts.more') . '</a>' : $this->content_html;
    }

    /**
     * [getFirstImageAttribute description]
     * @return string|null [description]
     */
    public function getFirstImageAttribute() : ?string
    {
        preg_match('/<img.+src=[\'|"](.*?)[\'|"]/', $this->content_html, $image);

        return $image[1] ?? null;
    }

    // Mutators

    /**
     * [setPublishedAtAttribute description]
     * @param string|null $value [description]
     */
    public function setPublishedAtAttribute(string $value = null) : void
    {
        if ($value === null) {
            $this->attributes['published_at'] = null;
            return;
        }

        $this->attributes['published_at'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    /**
     * [setContentAttribute description]
     * @param string $value [description]
     */
    public function setContentAttribute(string $value) : void
    {
        $this->attributes['content'] = strip_tags(str_replace('[more]', '', $value));
    }

    // Scopes

    /**
     * [scopeFilterCategory description]
     * @param  Builder $query    [description]
     * @param  Category|null  $category [description]
     * @return Builder|null            [description]
     */
    public function scopeFilterCategory(Builder $query, Category $category = null) : ?Builder
    {
        return $query->when($category !== null, function($query) use ($category) {
            $query->whereHas('categories', function($q) use ($category) {
                return $q->where('category_id', $category->id);
            });
        });
    }

    /**
     * [scopeActive description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopeActive(Builder $query) : Builder
    {
        return $query->where([
            ['posts.status', '=', 1],
            ['posts.published_at', '!=', null]
        ]);
    }

    // Getters

    /**
     * [getRepo description]
     * @return PostRepo [description]
     */
    public function getRepo() : PostRepo
    {
        return app()->make(PostRepo::class, ['post' => $this]);
    }

    /**
     * [getCache description]
     * @return PostCache [description]
     */
    public function getCache() : PostCache
    {
        return app()->make(PostCache::class, ['post' => $this]);
    }

    /**
     * [getService description]
     * @return PostService [description]
     */
    public function getService() : PostService
    {
        return app()->make(PostService::class, ['post' => $this]);
    }
}
