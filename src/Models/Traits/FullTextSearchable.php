<?php

namespace N1ebieski\ICore\Models\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Utils\MigrationUtil;
use Illuminate\Database\Eloquent\Builder;

trait FullTextSearchable
{
    /**
     * Search term
     * @var string
     */
    protected $term;

    /**
     * Words prepared for searching in boolean mode fulltext
     * @var array
     */
    public $search = [];

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function className() : string
    {
        return class_basename(strtolower(static::class));
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function splitModelMatches() : void
    {
        preg_match_all('/([a-z]+):\"(.*?)\"/', $this->term, $matches);

        foreach ($matches[0] as $match) {
            [$model, $word] = explode(':', $match);
            $this->search[$model] = '+' . $word;

            $this->term = str_replace($match, '', $this->term);
        }

        $this->term = trim($this->term);
    }

    /**
     * Prepares words for matching search
     * @return void [description]
     */
    protected function splitExactMatches() : void
    {
        preg_match_all('/"(.*?)"/', $this->term, $matches);

        foreach ($matches[0] as $match) {
            $this->search[$this->className()][] = '+' . $match;

            $this->term = str_replace($match, '', $this->term);
        }

        $this->term = trim($this->term);
    }

    /**
     * Undocumented function
     *
     * @param string $term
     * @return boolean
     */
    protected function isContainsSymbol(string $match) : bool
    {
        return Str::contains($match, ['.', '-', '+', '<', '>', '@', '*', '(', ')', '~']);
    }

    /**
     * Undocumented function
     *
     * @param string $match
     * @return string
     */
    protected function createExactMatch(string $match) : string
    {
        return '"' . $match . '"';
    }

    /**
     * Prepares words for a loose search
     * @return void
     */
    protected function splitMatches() : void
    {
        $matches = explode(' ', $this->term);

        foreach ($matches as $match) {
            if (strlen($match) >= 3) {
                $match = $this->isContainsSymbol($match) ?
                    $this->createExactMatch($match)
                    : $match;

                if ($match === end($matches)) {
                    $match .= '*';
                }
                
                $this->search[$this->className()][] = '+' . $match;
            }
        }
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function search() : string
    {
        return implode(' ', (array)$this->search[$this->className()]);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function columns() : string
    {
        return implode(',', $this->searchable);
    }

    /**
     * Scope a query that matches a full text search of term.
     * @param  Builder $query [description]
     * @param  string  $term  [description]
     * @return Builder        [description]
     */
    public function scopeSearch(Builder $query, string $term) : Builder
    {
        $this->term = $term;

        $this->splitModelMatches();
        $this->splitExactMatches();
        $this->splitMatches();

        return $query->when(array_key_exists($this->className(), $this->search), function ($query) {
            $query->selectRaw("`{$this->getTable()}`.*")
                ->whereRaw("MATCH ({$this->columns()}) AGAINST (? IN BOOLEAN MODE)", [$this->search()]);

            $query->when(App::make(MigrationUtil::class)->contains('add_column_fulltext_index_to_all_tables'), function ($query) {
                foreach ($this->searchable as $column) {
                    $query->selectRaw("MATCH ({$column}) AGAINST (? IN BOOLEAN MODE) AS `{$column}_relevance`", [$this->search()]);
                }
            });
        });
    }

    /**
     * Undocumented function
     *
     * @param Builder $query
     * @param string $term
     * @return Builder
     */
    public function scopeOrderBySearch(Builder $query, string $term) : Builder
    {
        $this->term = $term;

        $this->splitModelMatches();
        $this->splitExactMatches();
        $this->splitMatches();

        return $query->when(array_key_exists($this->className(), $this->search), function ($query) {
            $query->when(App::make(MigrationUtil::class)->contains('add_column_fulltext_index_to_all_tables'), function ($query) {
                foreach ($this->searchable as $column) {
                    $query->orderBy("{$column}_relevance", 'desc');
                }
            });
        });
    }
}
