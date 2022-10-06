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

class PushOnceDirective
{
    /**
     *
     * @var string
     */
    protected string $expression;

    /**
     *
     * @param string $expression
     * @return string
     */
    public function __invoke(string $expression): string
    {
        $this->expression = $expression;

        $push_name = $this->getNameFromExpression();
        $push_sub = $this->getSubFromExpression();

        $isDisplayed = '__pushonce_' . $push_name . '_' . $push_sub;

        return "<?php if(!isset(\$__env->{$isDisplayed})): \$__env->{$isDisplayed} = true; \$__env->startPush('{$push_name}'); ?>";
    }

    /**
     *
     * @return string
     */
    protected function getNameFromExpression(): string
    {
        $domain = explode('.', trim(substr($this->expression, 1, -1)));

        return $domain[0];
    }

    /**
     *
     * @return string
     */
    protected function getSubFromExpression(): string
    {
        $domain = explode('.', trim(substr($this->expression, 1, -1)));

        return $domain[1];
    }
}
