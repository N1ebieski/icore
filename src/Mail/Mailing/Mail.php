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

namespace N1ebieski\ICore\Mail\Mailing;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use N1ebieski\ICore\ValueObjects\Lang;
use N1ebieski\ICore\Models\MailingLang\MailingLang;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Routing\UrlGenerator as URL;
use N1ebieski\ICore\Models\MailingEmail\MailingEmail;
use Illuminate\Contracts\Foundation\Application as App;
use Illuminate\Contracts\Translation\Translator as Trans;

class Mail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     *
     * @param MailingEmail $mailingEmail
     * @param URL $url
     * @param Trans $trans
     * @param Config $config
     * @param App $app
     * @return void
     */
    public function __construct(
        public MailingEmail $mailingEmail,
        protected URL $url,
        protected Trans $trans,
        protected Config $config,
        protected App $app
    ) {
        $prefLang = $this->getPrefLang();

        if ($prefLang) {
            $this->setLangInApp($prefLang);
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->mailingEmail->load(['mailing', 'morph']);

        return $this->subject($this->mailingEmail->mailing->title)
            ->to($this->mailingEmail->email)
            ->markdown('icore::mails.mailing')
            ->with([
                'content' => $this->getContent(),
                'subcopy' => $this->getSubcopy()
            ]);
    }

    /**
     *
     * @return bool
     */
    protected function isMultiLangEnabled(): bool
    {
        return count($this->config->get('icore.multi_langs')) > 1;
    }

    /**
     *
     * @return null|Lang
     */
    protected function getPrefLang(): ?Lang
    {
        if (
            $this->isMultiLangEnabled()
            && ($user = $this->mailingEmail->morph) instanceof \N1ebieski\ICore\Models\User
            && in_array($user->pref_lang->getValue(), $this->config->get('icore.multi_langs'))
        ) {
            /** @var Lang */
            return $user->pref_lang;
        }

        return null;
    }

    /**
     *
     * @param Lang $lang
     * @return Mail
     */
    protected function setLangInApp(Lang $lang): self
    {
        $this->app->setLocale($lang->getValue());

        return $this;
    }

    /**
     *
     * @return string
     */
    protected function getContent(): string
    {
        if (!$this->isMultiLangEnabled()) {
            return $this->mailingEmail->mailing->replacement_content_html;
        }

        if ($prefLang = $this->getPrefLang()) {
            /** @var MailingLang|null */
            $mailingLang = $this->mailingEmail->mailing->langs->firstWhere('lang', $prefLang);

            if ($mailingLang) {
                return $mailingLang->replacement_content_html;
            }
        }

        return $this->mailingEmail->mailing->langs->sortBy(function (MailingLang $mailingLang) {
            return array_search($mailingLang->lang, $this->config->get('icore.multi_langs'));
        })
        // @phpstan-ignore-next-line
        ->transform(fn (MailingLang $mailingLang): string => $mailingLang->replacement_content_html)
        ->implode('<br>');
    }

    /**
     * [subcopy description]
     * @return string|null [description]
     */
    protected function getSubcopy(): ?string
    {
        switch ($this->mailingEmail->model_type) {
            case \N1ebieski\ICore\Models\User::class:
                return $this->trans->get('icore::newsletter.subcopy.user', [
                    'cancel' => $this->url->route('web.profile.edit')
                ]);

            case \N1ebieski\ICore\Models\Newsletter::class:
                /** @var \N1ebieski\ICore\Models\Newsletter */
                $morph = $this->mailingEmail->morph;

                return $this->trans->get('icore::newsletter.subcopy.subscribe', [
                    'cancel' => $this->url->route('web.newsletter.update_status', [
                        $morph->id,
                        'token' => $morph->token->token,
                        'status' => $morph->status::INACTIVE
                    ]),
                ]);

            default:
                return null;
        }
    }
}
