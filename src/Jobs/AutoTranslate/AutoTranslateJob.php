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

namespace N1ebieski\ICore\Jobs\AutoTranslate;

use Eloquent;
use Throwable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Queue\SerializesModels;
use N1ebieski\ICore\ValueObjects\Lang;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\ICore\Models\Interfaces\MultiLangInterface;
use N1ebieski\ICore\Models\Interfaces\TransableInterface;
use N1ebieski\ICore\Http\Clients\Google\Translate\TranslateClient;

class AutoTranslateJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     *
     * @var Carbon
     */
    protected Carbon $carbon;

    /**
     *
     * @var Config
     */
    protected Config $config;

    /**
     *
     * @param Eloquent&MultiLangInterface $model
     * @return void
     */
    public function __construct(protected MultiLangInterface $model)
    {
        //
    }

    /**
     *
     * @return bool
     */
    protected function verify(): bool
    {
        return count($this->config->get('icore.multi_langs')) > 1
            && $this->model->auto_translate->isActive()
            && $this->model->langs->isNotEmpty();
    }

    /**
     *
     * @param TransableInterface $fromModel
     * @param TransableInterface $toModel
     * @return bool
     * @throws InvalidFormatException
     */
    protected function verifyLangModel(
        TransableInterface $fromModel,
        TransableInterface $toModel
    ): bool {
        $verify = $toModel->progress->isAutoTrans() && !$toModel->lang->isEquals($fromModel->lang);

        if (!is_null($this->config->get('icore.auto_translate.check_days'))) {
            $verify &= (
                $toModel->translated_at === null
                || $this->carbon->parse($toModel->translated_at)->lessThanOrEqualTo(
                    $this->carbon->now()->subDays($this->config->get('icore.auto_translate.check_days'))
                )
            );
        }

        return $verify;
    }

    /**
     * Execute the job.
     *
     * @param TranslateClient $client
     * @param Carbon $carbon
     * @param Config $carbon
     * @return void
     */
    public function handle(
        TranslateClient $client,
        Carbon $carbon,
        Config $config,
    ) {
        $this->carbon = $carbon;
        $this->config = $config;

        if (!$this->verify()) {
            return;
        }

        $fromModel = $this->getFromModel();

        foreach ($this->config->get('icore.multi_langs') as $lang) {
            $toModel = $this->getToModelByLang($lang);

            if (!$this->verifyLangModel($fromModel, $toModel)) {
                continue;
            }

            $strings = [];

            foreach ($fromModel->getTransable() as $field) {
                $strings[] = $fromModel->{$field} ?? '';
            }

            $response = $client->translateMany([
                'strings' => $strings,
                'source' => $fromModel->lang->getValue(),
                'target' => $toModel->lang->getValue()
            ]);

            $attributes = [];

            foreach ($response->get('results') as $key => $value) {
                $attributes[$toModel->getTransable()[$key]] = $value['text'];
            }

            $toModel->makeService()->createOrUpdate(array_merge($attributes, [
                $this->getBaseName() => $this->model->getKey(),
                'translated_at' => $this->carbon->now()
            ]));
        }
    }

    /**
     * The job failed to process.
     *
     * @param  Throwable  $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        //
    }

    /**
     *
     * @return string
     */
    protected function getBaseName(): string
    {
        return lcfirst(class_basename($this->model::class));
    }

    /**
     *
     * @return TransableInterface
     * @throws MassAssignmentException
     */
    protected function getFromModel(): TransableInterface
    {
        /** @var TransableInterface */
        $fromModel = $this->model->langs
            ->filter(function (TransableInterface $langModel) {
                return $langModel->progress->isFullTrans();
            })
            ->sortBy(function (TransableInterface $langModel) {
                return array_search($langModel->lang->getValue(), $this->config->get('icore.multi_langs'));
            })
            ->first();

        return $fromModel;
    }

    /**
     *
     * @param string $lang
     * @return TransableInterface
     * @throws MassAssignmentException
     */
    protected function getToModelByLang(string $lang): TransableInterface
    {
        /** @var TransableInterface */
        $toModel = $this->model->langs->firstWhere('lang', new Lang($lang));

        if (is_null($toModel)) {
            /** @var TransableInterface */
            $toModel = $this->model->langs()->make([
                'lang' => $lang,
                'progress' => 0
            ]);
        }

        return $toModel;
    }
}
