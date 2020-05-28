<?php

namespace N1ebieski\ICore\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * [trait description]
 */
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
    protected function makeClassName() : string
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
            $this->search[$this->makeClassName()][] = '+' . $match;
            $this->term = str_replace($match, '', $this->term);
        }

        $this->term = trim($this->term);
    }

    /**
     * Auxiliary method. Removing symbols used by MySQL
     * @return string [description]
     */
    protected function removeSymbols() : string
    {
        $reservedSymbols = ['-', '+', '<', '>', '@', '*', '(', ')', '~'];

        return str_replace($reservedSymbols, '', $this->term);
    }

    /**
     * Prepares words for a loose search
     * @return void
     */
    protected function splitMatches() : void
    {
        $term = $this->removeSymbols();

        $matches = explode(' ', $term);

        foreach ($matches as $match) {
            if (strlen($match) >= 3) {
                if ($match === end($matches)) {
                    $match .= '*';
                }
                $this->search[$this->makeClassName()][] = '+' . $match;
            }
        }
    }

    /**
     * Make fulltext search of term
     * @return string
     */
    protected function makeFullText() : string
    {
        $this->splitModelMatches();
        $this->splitExactMatches();
        $this->splitMatches();

        return $this->makeSearch();
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function makeSearch() : string
    {
        return implode(' ', (array)$this->search[$this->makeClassName()]);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function makeColumns() : string
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

        return $query->whereRaw("MATCH ({$this->makeColumns()}) AGAINST (? IN BOOLEAN MODE)", [
            $this->makeFullText()
        ]);
    }
}
