<?php

namespace N1ebieski\ICore\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;

class EnvCommand extends Command
{
    /**
     * [protected description]
     * @var Composer
     */
    protected $composer;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'icore:env';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a work environment';

    /**
     * Create a new command instance.
     *
     * @param Composer  $composer
     * @return void
     */
    public function __construct(Composer $composer)
    {
        parent::__construct();

        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $bar = $this->output->createProgressBar(7);

        $this->info("\r");
        $bar->start();
        $this->info("\n");
        $this->call('vendor:publish', ['--tag' => 'icore.migrations', '--force' => true], $this->getOutput());
        $this->info("\r");
        $bar->advance();
        $this->info("\n");
        $this->call('vendor:publish', ['--tag' => 'icore.factories', '--force' => true], $this->getOutput());
        $this->info("\r");
        $bar->advance();
        $this->info("\n");
        $this->call('vendor:publish', ['--tag' => 'icore.seeds', '--force' => true], $this->getOutput());
        $this->info("\r");
        $bar->advance();
        $this->info("\n");
        $this->composer->dumpOptimized();
        $this->info("\r");
        $bar->advance();
        $this->info("\n");
        $this->call('migrate:fresh', ['--path' => 'database/migrations/vendor/icore'], $this->getOutput());
        $this->info("\r");
        $this->call('migrate', ['--path' => 'database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php', '--force' => true], $this->getOutput());
        $this->line("\n");
        $bar->advance();
        $this->info("\n");
        $this->call('db:seed', ['--class' => 'N1ebieski\ICore\Seeds\Env\EnvSeeder'], $this->getOutput());
        $this->info("\r");
        $bar->advance();
        $this->line("\n");
        $this->call('icore:superadmin', [], $this->getOutput());
        $this->info("\n");
        $bar->finish();
    }
}
