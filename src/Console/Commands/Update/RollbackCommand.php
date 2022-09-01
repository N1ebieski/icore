<?php

namespace N1ebieski\ICore\Console\Commands\Update;

use InvalidArgumentException;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use Symfony\Component\Console\Exception\LogicException;
use Illuminate\Contracts\Translation\Translator as Lang;
use Symfony\Component\Console\Exception\InvalidArgumentException as ExceptionInvalidArgumentException;

/**
 *
 * @author Mariusz Wysokiński <kontakt@intelekt.net.pl>
 */
class RollbackCommand extends Command
{
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
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Undocumented variable
     *
     * @var Lang
     */
    protected $lang;

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
        Config $config,
        Storage $storage,
        Filesystem $filesystem,
        Lang $lang,
        string $backup_path = 'backup/vendor/icore'
    ) {
        parent::__construct();

        $this->config = $config;
        $this->storage = $storage;
        $this->lang = $lang;
        $this->filesystem = $filesystem;

        $this->backup_path = $backup_path;
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
