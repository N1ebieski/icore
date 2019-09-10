<?php

namespace N1ebieski\ICore\Services;

use Cviebrock\EloquentTaggable\Services\TagService as BaseTagService;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\ICore\Models\Tag;
use Illuminate\Database\Eloquent\Model;

/**
 * [TagService description]
 */
class TagService extends BaseTagService
{
    /**
     * @var Tag
     */
    protected $tag;

    /**
     * [__construct description]
     * @param Tag $tag [description]
     */
    public function __construct(Tag $tag)
    {
        $this->tag = $tag;
    }

    /**
     * Override metody z paczki Taggable bo ma hardcodowane nazwy tabel w SQL
     *
     * @param int|null $limit
     * @param Model|string|null $class
     * @param int $minCount
     * @return Collection
     */
    public function getPopularTags(int $limit = null, $class = null, int $minCount = 1) : Collection
    {
        $sql = 'SELECT t.*, COUNT(t.tag_id) AS taggable_count FROM ' . $this->tag->table . ' t LEFT JOIN `tags_models` tt ON tt.tag_id=t.tag_id';
        $bindings = [];

        if ($class) {
            $sql .= ' WHERE tt.model_type = ?';
            $bindings[] = ($class instanceof Model) ? get_class($class) : $class;
        }

        // group by everything to handle strict and non-strict mode in MySQL
        $sql .= ' GROUP BY t.tag_id, t.name, t.normalized, t.created_at, t.updated_at';

        if ($minCount > 1) {
            $sql .= ' HAVING taggable_count >= ?';
            $bindings[] = $minCount;
        }

        $sql .= ' ORDER BY taggable_count DESC';

        if ($limit) {
            $sql .= ' LIMIT ?';
            $bindings[] = $limit;
        }

        return $this->tag->fromQuery($sql, $bindings);
    }
}
