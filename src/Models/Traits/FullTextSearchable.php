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
     * @var array|string
     */
    public $search = [];

    /**
     * Prepares words for matching search
     * @return void [description]
     */
    protected function splitExactWords() : void
    {
        preg_match_all('/"(.*?)"/', $this->term, $words);

        foreach ($words[0] as $word) {
            $this->search[] = '+' . $word;
            $this->term = str_replace($word, '', $this->term);
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
    protected function splitWords() : void
    {
        $term = $this->removeSymbols();

        $words = explode(' ', $term);

        foreach ($words as $word) {
            if (strlen($word) >= 3) {
                if ($word === end($words)) {
                    $word .= '*';
                }
                $this->search[] = '+' . $word;
            }
        }
    }

    /**
     * Make fulltext search of term
     * @return string
     */
    protected function makeFullText() : string
    {
        $this->splitExactWords();
        $this->splitWords();

        return $this->prepareSearch();
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function prepareSearch() : string
    {
        return $this->search = implode(' ', (array)$this->search);
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
