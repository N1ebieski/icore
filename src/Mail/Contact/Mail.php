<?php

namespace N1ebieski\ICore\Mail\Contact;

use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Translation\Translator as Lang;
use Illuminate\Contracts\Routing\UrlGenerator as URL;
use Illuminate\Contracts\Config\Repository as Config;

/**
 * [Mail description]
 */
class Mail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * [protected description]
     * @var array
     */
    protected $request;

    /**
     * Undocumented variable
     *
     * @var Lang
     */
    protected $lang;

    /**
     * Undocumented variable
     *
     * @var URL
     */
    protected $url;

    /**
     * Undocumented variable
     *
     * @var Config
     */
    protected $config;

    /**
     * [protected description]
     * @var string
     */
    protected string $email;

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param Lang $lang
     * @param URL $url
     * @param Config $config
     */
    public function __construct(Request $request, Lang $lang, URL $url, Config $config)
    {
        $this->request = $request;
        $this->lang = $lang;
        $this->url = $url;

        $this->email = $config->get('mail.from.address');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->request->input('title'))
            ->from($this->request->input('email'))
            ->to($this->email)
            ->markdown('icore::mails.contact')
            ->with([
                'subcopy' => $this->subcopy(),
                'content' => $this->request->input('content')
            ]);
    }

    /**
     * [subcopy description]
     * @return string [description]
     */
    protected function subcopy() : string
    {
        return $this->lang->get('icore::contact.subcopy.form', [
            'url' => $this->url->route('web.contact.show')
        ]);
    }
}
