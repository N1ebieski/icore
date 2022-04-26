<?php

namespace N1ebieski\ICore\Console\Commands\Update;

use Illuminate\Console\Command;
use N1ebieski\ICore\Utils\Updater\Updater;
use Illuminate\Contracts\Container\Container as App;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use Illuminate\Contracts\Translation\Translator as Lang;

class UpdateCommand extends Command
{
    /**
     * [protected description]
     * @var App
     */
    protected $app;

    /**
     * [protected description]
     * @var Config
     */
    protected $config;

    /**
     * Undocumented variable
     *
     * @var Storage
     */
    protected $storage;

    /**
     * Undocumented variable
     *
     * @var Lang
     */
    protected $lang;

    /**
     * Undocumented variable
     *
     * @var SchemaFactory
     */
    protected $schemaFactory;

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $backupPath;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'icore:update {version : The version to which the application files will be updated}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'iCore application updater.';

    /**
     * Undocumented function
     *
     * @param App $app
     * @param Config $config
     * @param Storage $storage
     * @param Lang $lang
     * @param SchemaFactory $schemaFactory
     * @param string $backupPath
     */
    public function __construct(
        App $app,
        Config $config,
        Storage $storage,
        Lang $lang,
        SchemaFactory $schemaFactory,
        string $backupPath = 'backup/icore'
    ) {
        parent::__construct();

        $this->app = $app;
        $this->config = $config;
        $this->storage = $storage;
        $this->lang = $lang;

        $this->schemaFactory = $schemaFactory;

        $this->backupPath = $backupPath;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function getFullBackupPath(): string
    {
        return $this->backupPath . '/' . str_replace('.', '', $this->argument('version'));
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function confirmation(): void
    {
        $this->info($this->lang->get('icore::update.update'));

        if (!$this->confirm($this->lang->get('icore::update.confirm'))) {
            exit;
        }
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function validateBackup(): void
    {
        $this->line($this->lang->get('icore::update.validate.backup'));

        if ($this->storage->disk('local')->exists($this->getFullBackupPath())) {
            $this->error($this->lang->get('icore::update.errors.backup.exists'));

            exit;
        }

        $this->info('OK');
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function backup(): void
    {
        $this->line($this->lang->get('icore::update.backup'));

        try {
            $updater = $this->app->make(Updater::class, [
                'schema' => $this->schemaFactory->makeSchema($this->argument('version'))
            ]);

            $updater->backup($this->getFullBackupPath());
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
    protected function update(): void
    {
        $this->line($this->lang->get('icore::update.update_files'));

        try {
            $updater = $this->app->make(Updater::class, [
                'schema' => $this->schemaFactory->makeSchema($this->argument('version'))
            ]);

            $updater->update();
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            exit;
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
        $bar = $this->output->createProgressBar(3);

        $this->line("iCore updater");
        $this->line("Author: Mariusz Wysokiński");
        $this->line("Version: {$this->config->get('icore.version')}");
        $this->line("\n");
        $this->confirmation();
        $this->line("\n");
        $bar->start();
        $this->line("\n");
        $this->validateBackup();
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->backup();
        $this->line("\n");
        $bar->advance();
        $this->line("\n");
        $this->update();
        $this->line("\n");
        $bar->finish();
    }
}
