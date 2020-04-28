<?php

namespace N1ebieski\ICore\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * [trait description]
 */
trait FullTextSearchable
{
    /**
     * Search names
     * @var string
     */
    protected $term;

    /**
     * Words prepared for searching in boolean mode fulltext
     * @var [type]
     */
    protected $search = [];

    /**
     * Setter
     * @param string $term [description]
     * @return self
     */
    public function setTerm($term) : self
    {
        $this->term = $term;

        return $this;
    }

    /**
     * Prepares words for matching search
     * @return void [description]
     */
    protected function exactWords() : void
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
    protected function words() : void
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
     * Prepares fulltext search of term
     * @return string
     */
    public function fullText() : string
    {
        $this->exactWords();
        $this->words();

        return implode(' ', (array)$this->search);
    }

    /**
     * Scope a query that matches a full text search of term.
     * @param  Builder $query [description]
     * @param  string  $term  [description]
     * @return Builder        [description]
     */
    public function scopeSearch(Builder $query, string $term) : Builder
    {
        $columns = implode(',', $this->searchable);

        return $query->whereRaw("MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE)", [
            $this->setTerm($term)->fullText()
        ]);
    }
}
