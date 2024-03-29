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

namespace N1ebieski\ICore\Console\Commands;

use InvalidArgumentException;
use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Illuminate\Contracts\Mail\Mailer;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Database\DatabaseManager as DB;
use Illuminate\Contracts\Config\Repository as Config;
use Symfony\Component\Console\Exception\LogicException;
use Illuminate\Contracts\Translation\Translator as Lang;
use Illuminate\Contracts\Validation\Factory as Validator;
use Symfony\Component\Console\Exception\InvalidArgumentException as ExceptionInvalidArgumentException;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'icore:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'iCore application installer.';

    /**
     *
     * @param Composer $composer
     * @param Config $config
     * @param Mailer $mailer
     * @param Lang $lang
     * @param Validator $validator
     * @param DB $db
     * @param GuzzleClient $guzzle
     * @return void
     * @throws InvalidArgumentException
     * @throws ExceptionInvalidArgumentException
     * @throws LogicException
     */
    public function __construct(
        protected Composer $composer,
        protected Config $config,
        protected Mailer $mailer,
        protected Lang $lang,
        protected Validator $validator,
        protected DB $db,
        protected GuzzleClient $guzzle
    ) {
        parent::__construct();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function confirmation(): void
    {
        $this->info($this->lang->get('icore::install.install'));

        if (!$this->confirm($this->lang->get('icore::install.confirm'))) {
            exit;
        }
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function validateUrl(): void
    {
        $this->line($this->lang->get('icore::install.validate.url'));

        $validator = $this->validator->make(['url' => $this->config->get('app.url')], [
            'url' => [
                'bail',
                'required',
                'string',
                'regex:/^(https|http):\/\/([\da-z\.-]+)(\.[a-z]{2,6})/'
            ]
        ]);

        if ($validator->fails()) {
            $this->error($validator->errors()->first());

            exit;
        }

        $this->info('OK');
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function validateConnectionMail(): void
    {
        $this->line($this->lang->get('icore::install.validate.connection_mail'));

        try {
            $this->mailer->raw('Test', function ($message) {
                $message
                    ->to($this->config->get('mail.from.address'))
                    ->subject('Test');
            });
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            exit;
        }

        $this->info('OK');
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function validateConnectionDatabase(): void
    {
        $this->line($this->lang->get('icore::install.validate.connection_database'));

        try {
            $this->db->getPdo();
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            exit;
        }

        $this->info('OK');
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function validateLicense(): void
    {
        $this->line($this->lang->get('icore::install.validate.license'));

        try {
            $this->guzzle->request('GET', $this->config->get('app.url'), [
                'verify' => false,
                'headers' => [
                    'Accept' => 'application/json'
                ]
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->error(json_decode($e->getResponse()->getBody())->message);

            exit;
        } catch (\Exception $e) {
            //
        }

        $this->info('OK');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $bar = $this->output->createProgressBar(17);

        $this->line("iCore installer");
        $this->line("Author: Mariusz Wysokiński");
        $this->line("Version: {$this->config->get('icore.version')}");
        $this->line("\n");

        $this->confirmation();

        $this->line("\n");

        $bar->start();

        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.langs'));
        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'icore.lang', '--force' => true]);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");

        $this->validateUrl();

        $this->validateConnectionMail();

        $this->validateConnectionDatabase();

        $this->validateLicense();

        $this->line("\n");

        $bar->advance();

        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.migrations'));
        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'icore.migrations', '--force' => true]);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.factories'));
        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'icore.factories', '--force' => true]);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.seeders'));
        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'icore.seeders', '--force' => true]);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.config'));
        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'icore.config', '--force' => true]);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.js'));
        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'icore.js', '--force' => true]);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.sass'));
        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'icore.sass', '--force' => true]);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.views'));
        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'icore.views.web', '--force' => true]);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.public'));
        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'icore.public.images', '--force' => true]);

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'icore.public.css', '--force' => true]);

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'icore.public.js', '--force' => true]);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.vendor'));
        $this->line("\n");

        $this->call('vendor:publish', ['--provider' => 'N1ebieski\LogicCaptcha\Providers\LogicCaptchaServiceProvider', '--force' => true]);

        $this->line("\n");

        $this->call('vendor:publish', ['--provider' => 'Proengsoft\JsValidation\JsValidationServiceProvider', '--force' => true]);

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'fm-css', '--force' => true]);

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'fm-js', '--force' => true]);

        $this->line("\n");

        $this->call('vendor:publish', ['--tag' => 'fm-views', '--force' => true]);

        $this->line("\n");

        $this->call('vendor:publish', ['--provider' => 'Laravel\Sanctum\SanctumServiceProvider', '--tag' => 'sanctum-migrations', '--force' => true]);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");
        $this->line($this->lang->get('icore::install.dump'));
        $this->line("\n");

        $this->composer->dumpOptimized();

        $this->line("\n");
        $this->info("OK");
        $this->line("\n");

        $bar->advance();

        $this->line("\n");
        $this->line($this->lang->get('icore::install.migrate'));
        $this->line("\n");

        $this->call('migrate:fresh', ['--path' => 'database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php', '--force' => true]);

        $this->line("\n");

        $this->call('migrate', ['--path' => 'database/migrations/vendor/icore', '--force' => true]);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");
        $this->line($this->lang->get('icore::install.seed'));
        $this->line("\n");

        $this->call('db:seed', ['--class' => 'N1ebieski\ICore\Database\Seeders\Install\InstallSeeder', '--force' => true]);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");
        $this->line($this->lang->get('icore::install.register_superadmin'));
        $this->line("\n");

        $this->call('icore:superadmin', []);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");
        $this->line($this->lang->get('icore::install.cache.routes'));
        $this->line("\n");

        $this->call('route:cache', []);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");
        $this->line($this->lang->get('icore::install.cache.config'));
        $this->line("\n");

        $this->call('config:cache', []);

        $this->line("\n");

        $bar->advance();

        $this->line("\n");
        $this->line($this->lang->get('icore::install.storage_link'));
        $this->line("\n");

        $this->call('storage:link', []);

        $this->line("\n");

        $bar->finish();
    }
}
