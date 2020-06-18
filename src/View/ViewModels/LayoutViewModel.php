<?php

namespace N1ebieski\ICore\View\ViewModels;

use Illuminate\Http\Request;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\Str;
use Illuminate\Contracts\Routing\UrlGenerator as Url;
use Spatie\ViewModels\ViewModel;

class LayoutViewModel extends ViewModel
{
    /**
     * [private description]
     * @var Request
     */
    protected $request;

    /**
     * [private description]
     * @var Config
     */
    protected $config;

    /**
     * [private description]
     * @var Str
     */
    protected $str;

    /**
     * [private description]
     * @var Url
     */
    protected $url;

    /**
     * [__construct description]
     * @param Request $request [description]
     * @param Config  $config  [description]
     * @param Str     $str     [description]
     * @param Url     $url     [description]
     */
    public function __construct(Request $request, Config $config, Str $str, Url $url)
    {
        $this->request = $request;
        $this->config = $config;
        $this->str = $str;
        $this->url = $url;
    }

    /**
     * [makeMeta description]
     * @param  array  $input     [description]
     * @param  string $separator [description]
     * @return string            [description]
     */
    public function makeMeta(array $input, string $separator) : string
    {
        return implode($separator, array_filter($input));
    }

    /**
     * [get_theme description]
     * @return string|null [description]
     */
    public function getTheme() : ?string
    {
        switch ((string)$this->request->cookie('themeToggle')) {
            case 'dark':
                return 'dark';

            case 'light':
                return '';
        }

        return $this->config->get('icore.theme');
    }

    /**
     * [get_stylesheet description]
     * @param string $assets
     * @return string [description]
     */
    public function getStylesheet(string $assets = 'css/vendor/icore') : string
    {
        $path = '/' . $assets . '/web/web';

        if ($this->str->contains($this->url->current(), '/admin')) {
            $path = '/' . $assets . '/admin/admin';
        }

        if (file_exists(public_path() . $path . '-' . $this->getTheme() . '.css')) {
            return $path . '-' . $this->getTheme() . '.css';
        }

        return $path . '.css';
    }
}
