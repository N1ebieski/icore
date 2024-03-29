<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\ICore\Models\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Utils\Migration\Interfaces\MigrationRecognizeInterface;

trait HasFullTextSearchable
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
    protected function className(): string
    {
        return class_basename(strtolower(static::class));
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function splitAttributeMatches(): void
    {
        $columns = implode('|', Schema::getColumnListing($this->getTable()));

        preg_match_all('/(' . $columns . '):\"(.*?)\"/', $this->term, $matches);

        foreach ($matches[0] as $key => $value) {
            $this->search[trim($matches[1][$key])] = trim(str_replace('"', '', $matches[2][$key]));

            $this->term = str_replace($value, '', $this->term);
        }

        $this->term = trim($this->term);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function splitRelationMatches(): void
    {
        preg_match_all('/([a-z]+):\"(.*?)\"/', $this->term, $matches);

        foreach ($matches[0] as $key => $value) {
            $this->search[trim($matches[1][$key])] = '+"' . trim($matches[2][$key]) . '"';

            $this->term = str_replace($value, '', $this->term);
        }

        $this->term = trim($this->term);
    }

    /**
     * Prepares words for matching search
     * @return void [description]
     */
    protected function splitExactMatches(): void
    {
        preg_match_all('/"(.*?)"/', $this->term, $matches);

        foreach ($matches[0] as $match) {
            $this->search[$this->className()][] = '+' . $match;

            $this->term = str_replace($match, '', $this->term);
        }

        $this->term = trim($this->term);
    }

    /**
     *
     * @param string $match
     * @return bool
     */
    protected function isContainsSymbol(string $match): bool
    {
        return Str::contains($match, ['.', '-', '+', '<', '>', '@', '*', '(', ')', '~']);
    }

    /**
     * Prepares words for a loose search
     * @return void
     */
    protected function splitMatches(): void
    {
        $matches = explode(' ', $this->term);

        foreach ($matches as $match) {
            if (strlen($match) >= 3) {
                $match = $this->isContainsSymbol($match) ?
                    '"' . $match . '"'
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
    protected function getSearchAsString(): string
    {
        return implode(' ', (array)$this->search[$this->className()]);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function getColumnsAsString(): string
    {
        $columns = array_map([$this, 'getColumnAsString'], $this->searchable);

        return implode(',', $columns);
    }

    /**
     *
     * @param string $column
     * @return string
     */
    protected function getColumnAsString(string $column): string
    {
        $parts = explode('.', $column);

        return implode('.', array_map(function (string $part) {
            return '`' . $part . '`';
        }, $parts));
    }

    /**
     * Scope a query that matches a full text search of term.
     * @param  Builder $query [description]
     * @param  string  $term  [description]
     * @return Builder        [description]
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $this->term = $term;

        $this->splitAttributeMatches();
        $this->splitRelationMatches();
        $this->splitExactMatches();
        $this->splitMatches();

        return $query->when(array_key_exists($this->className(), $this->search), function (Builder $query) {
            return $query->whereRaw(
                "MATCH ({$this->getColumnsAsString()}) AGAINST (? IN BOOLEAN MODE)",
                [$this->getSearchAsString()]
            )
            ->when(
                App::make(MigrationRecognizeInterface::class)->contains('add_column_fulltext_index_to_all_tables'),
                function (Builder $query) {
                    foreach ($this->searchable as $column) {
                        $query->selectRaw(
                            "MATCH ({$this->getColumnsAsString($column)}) AGAINST (? IN BOOLEAN MODE) AS `{$column}_relevance`",
                            [$this->getSearchAsString()]
                        );
                    }

                    return $query;
                }
            );
        });
    }

    /**
     * Undocumented function
     *
     * @param Builder $query
     * @param string $term
     * @return Builder
     */
    public function scopeOrderBySearch(Builder $query, string $term): Builder
    {
        $this->term = $term;

        $this->splitAttributeMatches();
        $this->splitRelationMatches();
        $this->splitExactMatches();
        $this->splitMatches();

        return $query->when(array_key_exists($this->className(), $this->search), function (Builder $query) {
            return $query->when(
                App::make(MigrationRecognizeInterface::class)->contains('add_column_fulltext_index_to_all_tables'),
                function (Builder $query) {
                    foreach ($this->searchable as $column) {
                        $query->orderBy("{$column}_relevance", 'desc');
                    }

                    return $query;
                }
            );
        });
    }
}
