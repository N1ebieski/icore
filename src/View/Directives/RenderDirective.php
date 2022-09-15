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

namespace N1ebieski\ICore\View\Directives;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\View\Compilers\BladeCompiler;

/**
 * iCore used Spatie View Component package before Laravel 7. Currently package
 * has been archived because newest versions of Laravel have own component system.
 * However old template files can still use render directive from Spatie View Component package.
 * That directive decorates old render directive to new system.
 */
class RenderDirective
{
    /**
     *
     * @var string
     */
    protected string $expression;

    /**
     *
     * @param Str $str
     * @param BladeCompiler $bladeCompiler
     * @return void
     */
    public function __construct(
        protected Str $str,
        protected BladeCompiler $bladeCompiler
    ) {
        //
    }

    /**
     *
     * @param string $expression
     * @return string
     * @throws InvalidArgumentException
     */
    public function __invoke(string $expression): string
    {
        $this->expression = $expression;

        $path = $this->getComponentPathFromExpression();
        $props = $this->getPropsFromExpression();

        $component = "<x-{$path}";

        foreach ($props as $key => $value) {
            $component .= " :{$key}=\"{$value}\"";
        }

        $component .= " />";

        return $this->bladeCompiler->compileString($component);
    }

    /**
     *
     * @return string
     */
    protected function getComponentPathFromExpression(): string
    {
        $expressionParts = explode(',', $this->expression, 2);

        return $this->str->substr($expressionParts[0], 1, -1);
    }

    /**
     *
     * @return array
     */
    protected function getPropsFromExpression(): array
    {
        $expressionParts = explode(',', $this->expression, 2);

        if (!isset($expressionParts[1])) {
            return [];
        }

        return json_decode('{' . $this->str->of($expressionParts[1])
            ->trim()
            ->replaceMatches('/=>\s*([\S\s]*?)(,\n\'|\s+\]$(?!\n+))/', '=> "$1"$2')
            ->replaceMatches('/\'(.*?)\'\s*=>/', '"$1" =>')
            ->replaceMatches('/\s+/', '')
            ->replace('=>', ':')
            ->substr(1, -1) . '}', true);
    }
}
