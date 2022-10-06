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

namespace N1ebieski\ICore\Utils\Updater;

use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection as Collect;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use N1ebieski\ICore\Utils\Updater\Action\ActionFactory;
use N1ebieski\ICore\Utils\Updater\Schema\Interfaces\SchemaInterface;

class Updater
{
    /**
     *
     * @param SchemaInterface $schema
     * @param Storage $storage
     * @param Filesystem $filesystem
     * @param Str $str
     * @param Collect $collect
     * @param ActionFactory $actionFactory
     * @return void
     */
    public function __construct(
        protected SchemaInterface $schema,
        protected Storage $storage,
        protected Filesystem $filesystem,
        protected Str $str,
        protected Collect $collect,
        protected ActionFactory $actionFactory
    ) {
        //
    }

    /**
     * Undocumented function
     *
     * @param string $path
     * @return void
     */
    public function backup(string $path = 'backup'): void
    {
        foreach ($this->getPathsFromSchema() as $schemaPath) {
            $backupFullPath = $path . '/' . $schemaPath;

            if ($this->storage->disk('local')->exists($backupFullPath)) {
                continue;
            }

            if ($this->filesystem->type(base_path($schemaPath)) === 'dir') {
                $this->filesystem->copyDirectory(
                    base_path($schemaPath),
                    storage_path('app') . '/' . $backupFullPath
                );
            } else {
                if (!$this->filesystem->isDirectory(dirname(storage_path('app') . '/' . $backupFullPath))) {
                    $this->filesystem->makeDirectory(
                        dirname(storage_path('app') . '/' . $backupFullPath),
                        0755,
                        true
                    );
                }

                $this->filesystem->copy(
                    base_path($schemaPath),
                    storage_path('app') . '/' . $backupFullPath
                );
            }
        }
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function update(): void
    {
        foreach ($this->schema->pattern as $pattern) {
            foreach ($pattern['paths'] as $path) {
                if ($this->filesystem->missing(base_path($path))) {
                    continue;
                }

                if ($this->filesystem->type(base_path($path)) === 'file') {
                    $files = [$path];
                } else {
                    $files = $this->filesystem->allFiles(base_path($path));
                }

                foreach ($files as $filename) {
                    $contents = $this->filesystem->get($filename);

                    foreach ($pattern['actions'] as $action) {
                        $contents = $this->str->of($contents);

                        $matches = $contents->matchAll($action['search']);

                        if ($matches->isEmpty()) {
                            continue;
                        }

                        $contents = $this->actionFactory->makeAction($action)
                            ->handle($contents, $matches->toArray());
                    }

                    $this->filesystem->put($filename, $contents);
                }
            }
        }
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    protected function getPathsFromSchema(): array
    {
        $paths = [];

        $this->collect->make($this->schema->pattern)
            ->pluck('paths')
            ->flatten()
            ->each(function ($item) use (&$paths) {
                if (in_array($item, $paths)) {
                    return;
                }

                if ($this->filesystem->missing(base_path($item))) {
                    return;
                }

                if ($this->filesystem->type(base_path($item)) === 'dir') {
                    array_unshift($paths, $item);
                } else {
                    array_push($paths, $item);
                }
            });

        return $paths;
    }
}
