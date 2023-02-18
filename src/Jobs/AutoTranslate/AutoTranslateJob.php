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

use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Queue\SerializesModels;
use N1ebieski\ICore\ValueObjects\Lang;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Foundation\Application as App;
use Illuminate\Database\Eloquent\MassAssignmentException;
use N1ebieski\ICore\Models\Interfaces\TransableInterface;
use N1ebieski\ICore\Models\Interfaces\AutoTranslateInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use N1ebieski\ICore\Http\Clients\Google\Translate\TranslateClient;
use N1ebieski\ICore\Jobs\AutoTranslate\Data\Factories\InputDataFactory;
use N1ebieski\ICore\Jobs\AutoTranslate\Data\Factories\OutputDataFactory;

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
     * @var App
     */
    protected App $app;

    /**
     *
     * @param AutoTranslateInterface $model
     * @return void
     */
    public function __construct(protected AutoTranslateInterface $model)
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
        return $toModel->progress->isAutoTrans()
            && !$toModel->lang->isEquals($fromModel->lang)
            && (
                is_null($toModel->translated_at)
                || $this->carbon->parse($toModel->translated_at)->lessThanOrEqualTo(
                    $this->carbon->now()->subDays($this->config->get('icore.auto_translate.check_days'))
                )
            );
    }

    /**
     *
     * @param TranslateClient $client
     * @param Carbon $carbon
     * @param Config $config
     * @param App $app
     * @param InputDataFactory $inputDataFactory
     * @param OutputDataFactory $outputDataFactory
     * @return void
     * @throws BindingResolutionException
     * @throws InvalidFormatException
     */
    public function handle(
        TranslateClient $client,
        Carbon $carbon,
        Config $config,
        App $app,
        InputDataFactory $inputDataFactory,
        OutputDataFactory $outputDataFactory
    ) {
        $this->carbon = $carbon;
        $this->config = $config;
        $this->app = $app;

        if (!$this->verify()) {
            return;
        }

        $fromModel = $this->getFromModel();

        $inputData = $inputDataFactory->makeData($fromModel)->getInput();

        foreach ($this->config->get('icore.multi_langs') as $lang) {
            $toModel = $this->getToModelByLang($lang);

            if (!$this->verifyLangModel($fromModel, $toModel)) {
                continue;
            }

            $response = $client->translateMany([
                'strings' => array_values($inputData),
                'source' => $fromModel->lang->getValue(),
                'target' => $toModel->lang->getValue()
            ]);

            $attributes = [];

            foreach ($response->get('results') as $key => $value) {
                $attributes[array_keys($inputData)[$key]] = $value['text'];
            }

            $outputData = $outputDataFactory->makeData($toModel)->getOutput($attributes);

            $this->app->setLocale($lang);

            $this->model->makeService()->update(array_merge($outputData, [
                'translated_at' => $this->carbon->now(),
                'progress' => 0
            ]));
        }
    }

    /**
     *
     * @return TransableInterface
     */
    protected function getFromModel(): TransableInterface
    {
        /** @var Collection<TransableInterface> */
        $langs = $this->model->langs;

        /** @var TransableInterface */
        $fromModel = $langs->filter(function (TransableInterface $langModel) {
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
        /** @var TransableInterface|null */
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
