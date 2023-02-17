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

namespace N1ebieski\ICore\Crons\AutoTranslate;

use N1ebieski\ICore\Crons\AutoTranslate\Director;
use Illuminate\Contracts\Container\Container as App;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\ICore\Crons\AutoTranslate\Builder\Interfaces\BuilderInterface;

class AutoTranslateCron
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $builders = [
        \N1ebieski\ICore\Crons\AutoTranslate\Builder\PostBuilder::class,
        \N1ebieski\ICore\Crons\AutoTranslate\Builder\PageBuilder::class,
        \N1ebieski\ICore\Crons\AutoTranslate\Builder\Category\Post\CategoryBuilder::class,
        \N1ebieski\ICore\Crons\AutoTranslate\Builder\MailingBuilder::class
    ];

    /**
     *
     * @param App $app
     * @param Director $director
     * @param Config $config
     * @return void
     */
    public function __construct(
        protected App $app,
        protected Director $director,
        protected Config $config
    ) {
        //
    }

    protected function verify(): bool
    {
        return count($this->config->get('icore.multi_langs')) > 1
            && $this->config->get('icore.auto_translate.check_days') > 0;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function __invoke(): void
    {
        if (!$this->verify()) {
            return;
        }

        foreach ($this->builders as $builder) {
            /** @var BuilderInterface */
            $builder = $this->app->make($builder);

            $this->director->build($builder);
        }
    }
}
