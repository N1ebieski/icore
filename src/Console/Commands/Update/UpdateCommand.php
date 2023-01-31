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

namespace N1ebieski\ICore\Console\Commands\Update;

use Illuminate\Console\Command;
use Illuminate\Contracts\Container\Container as App;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use Illuminate\Contracts\Translation\Translator as Lang;
use N1ebieski\ICore\Utils\Updater\Interfaces\UpdaterInterface;

class UpdateCommand extends Command
{
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
        protected App $app,
        protected Config $config,
        protected Storage $storage,
        protected Lang $lang,
        protected SchemaFactory $schemaFactory,
        protected string $backup_path = 'backup/vendor/icore'
    ) {
        parent::__construct();
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
            /** @var UpdaterInterface */
            $updater = $this->app->make(UpdaterInterface::class, [
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
            /** @var UpdaterInterface */
            $updater = $this->app->make(UpdaterInterface::class, [
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
