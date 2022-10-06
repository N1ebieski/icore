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

use InvalidArgumentException;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use Symfony\Component\Console\Exception\LogicException;
use Illuminate\Contracts\Translation\Translator as Lang;
use Symfony\Component\Console\Exception\InvalidArgumentException as ExceptionInvalidArgumentException;

class RollbackCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'icore:update:rollback {version : The version to which the application files will be restored}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'iCore application updater rollback.';

    /**
     *
     * @param Config $config
     * @param Storage $storage
     * @param Filesystem $filesystem
     * @param Lang $lang
     * @param string $backup_path
     * @return void
     * @throws InvalidArgumentException
     * @throws ExceptionInvalidArgumentException
     * @throws LogicException
     */
    public function __construct(
        protected Config $config,
        protected Storage $storage,
        protected Filesystem $filesystem,
        protected Lang $lang,
        protected string $backup_path = 'backup/vendor/icore'
    ) {
        parent::__construct();
    }

    /**
     *
     * @return string
     * @throws ExceptionInvalidArgumentException
     * @throws InvalidArgumentException
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
        $this->info($this->lang->get('icore::update.rollback'));

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

        if (!$this->storage->disk('local')->exists($this->getFullBackupPath())) {
            $this->error($this->lang->get('icore::update.errors.backup.no_exists'));

            exit;
        }

        $this->info('OK');
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function rollback(): void
    {
        $this->line($this->lang->get('icore::update.rollback_files'));

        $this->filesystem->copyDirectory(
            storage_path('app') . '/' . $this->getFullBackupPath(),
            base_path()
        );

        $this->filesystem->deleteDirectory(
            storage_path('app') . '/' . $this->getFullBackupPath()
        );

        $this->info('OK');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $bar = $this->output->createProgressBar(2);

        $this->line("iCore updater rollback");
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

        $this->rollback();

        $this->line("\n");

        $bar->finish();
    }
}
