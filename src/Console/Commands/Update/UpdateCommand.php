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
    protected $backup_path;

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
     * @param string $backup_path
     */
    public function __construct(
        App $app,
        Config $config,
        Storage $storage,
        Lang $lang,
        SchemaFactory $schemaFactory,
        string $backup_path = 'backup/vendor/icore'
    ) {
        parent::__construct();

        $this->app = $app;
        $this->config = $config;
        $this->storage = $storage;
        $this->lang = $lang;

        $this->schemaFactory = $schemaFactory;

        $this->backup_path = $backup_path;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function getFullBackupPath(): string
    {
        if (!is_string($this->argument('version'))) {
            throw new \InvalidArgumentException('The version argument must be a string.');
        }

        return $this->backup_path . '/' . str_replace('.', '', $this->argument('version'));
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

        if (!is_string($this->argument('version'))) {
            throw new \InvalidArgumentException('The version argument must be a string.');
        }

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

        if (!is_string($this->argument('version'))) {
            throw new \InvalidArgumentException('The version argument must be a string.');
        }

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
