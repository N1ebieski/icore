<?php

namespace N1ebieski\ICore\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Translation\Translator as Lang;
use Illuminate\Contracts\Validation\Factory as Validator;
use Illuminate\Database\DatabaseManager as DB;

class InstallCommand extends Command
{
    /**
     * [protected description]
     * @var Composer
     */
    protected $composer;

    /**
     * Undocumented variable
     *
     * @var Config
     */
    protected $config;

    /**
     * Undocumented variable
     *
     * @var Lang
     */
    protected $lang;

    /**
     * Undocumented variable
     *
     * @var Validator
     */
    protected $validator;

    /**
     * Undocumented variable
     *
     * @var DB
     */
    protected $db;

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
     * Undocumented function
     *
     * @param Composer $composer
     * @param Config $config
     * @param Lang $lang
     * @param Validator $validator
     * @param DB $db
     */
    public function __construct(
        Composer $composer,
        Config $config,
        Lang $lang,
        Validator $validator,
        DB $db
    ) {
        parent::__construct();

        $this->composer = $composer;
        $this->config = $config;
        $this->lang = $lang;
        $this->validator = $validator;
        $this->db = $db;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function confirmation() : void
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
    protected function validateUrl() : void
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
    protected function validateConnectionMail() : void
    {
        if ($this->config->get('mail.driver') !== 'smtp') {
            return;
        }
        
        $this->line($this->lang->get('icore::install.validate.connection_mail'));

        try {
            $transport = new \Swift_SmtpTransport(
                $this->config->get('mail.host'),
                $this->config->get('mail.port'),
                $this->config->get('mail.encryption')
            );
            $transport->setUsername($this->config->get('mail.username'));
            $transport->setPassword($this->config->get('mail.password'));

            $mailer = new \Swift_Mailer($transport);
            $mailer->getTransport()->start();
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
    protected function validateConnectionDatabase() : void
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
    protected function validateLicense() : void
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
        $this->call('vendor:publish', ['--tag' => 'icore.lang', '--force' => true], $this->getOutput());
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
        $this->call('vendor:publish', ['--tag' => 'icore.migrations', '--force' => true], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.factories'));
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'icore.factories', '--force' => true], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.seeds'));
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'icore.seeds', '--force' => true], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.config'));
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'icore.config', '--force' => true], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.js'));
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'icore.js', '--force' => true], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.sass'));
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'icore.sass', '--force' => true], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.views'));
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'icore.views.web', '--force' => true], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.public'));
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'icore.public.images', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'icore.public.css', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'icore.public.js', '--force' => true], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.publish.vendor'));
        $this->line("\n");
        $this->call('vendor:publish', ['--provider' => 'N1ebieski\LogicCaptcha\Providers\LogicCaptchaServiceProvider', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('vendor:publish', ['--provider' => 'Proengsoft\JsValidation\JsValidationServiceProvider', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'fm-css', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'fm-js', '--force' => true], $this->getOutput());
        $this->line("\n");
        $this->call('vendor:publish', ['--tag' => 'fm-views', '--force' => true], $this->getOutput());
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
        $this->call('migrate:fresh', ['--path' => 'database/migrations/vendor/icore', '--force' => true], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.seed'));
        $this->line("\n");
        $this->call('db:seed', ['--class' => 'N1ebieski\ICore\Seeds\Install\InstallSeeder', '--force' => true], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.register_superadmin'));
        $this->line("\n");
        $this->call('icore:superadmin', [], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.cache.routes'));
        $this->line("\n");
        $this->call('route:cache', [], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.cache.config'));
        $this->line("\n");
        $this->call('config:cache', [], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->line($this->lang->get('icore::install.storage_link'));
        $this->line("\n");
        $this->call('storage:link', [], $this->getOutput());
        $this->line("\n");
        $bar->finish();
    }
}
