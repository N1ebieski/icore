<?php

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
     * Undocumented variable
     *
     * @var SchemaInterface
     */
    protected $schema;

    /**
     * Undocumented variable
     *
     * @var Storage
     */
    protected $storage;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Undocumented variable
     *
     * @var Str
     */
    protected $str;

    /**
     * @var Collect
     */
    protected $collect;

    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * Undocumented function
     *
     * @param SchemaInterface $schema
     * @param Storage $storage
     * @param Filesystem $filesystem
     * @param Str $str
     * @param Collect $collect
     * @param ActionFactory $actionFactory
     */
    public function __construct(
        SchemaInterface $schema,
        Storage $storage,
        Filesystem $filesystem,
        Str $str,
        Collect $collect,
        ActionFactory $actionFactory
    ) {
        $this->schema = $schema;

        $this->storage = $storage;
        $this->filesystem = $filesystem;
        $this->str = $str;
        $this->collect = $collect;

        $this->actionFactory = $actionFactory;

        $this->filesystem = $filesystem;
    }

    /**
     * Undocumented function
     *
     * @param string $path
     * @return void
     */
    public function backup(string $path = 'backup'): void
    {
        foreach ($this->getPathsFromSchema() as $disk => $paths) {
            foreach ($paths as $p) {
                $backupFullPath = $path . '/' . $disk . '/' . $p;

                if ($this->storage->disk('local')->exists($backupFullPath)) {
                    continue;
                }

                if ($this->storage->disk($disk)->getMetadata($p)['type'] === 'dir') {
                    $this->filesystem->copyDirectory(
                        resource_path($disk) . '/' . $p,
                        storage_path('app') . '/' . $backupFullPath
                    );
                } else {
                    $this->filesystem->copy(
                        resource_path($disk) . '/' . $p,
                        storage_path('app') . '/' . $backupFullPath
                    );
                }
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
            foreach ($pattern['paths'] as $disk => $paths) {
                foreach ($paths as $path) {
                    if ($this->storage->disk($disk)->missing($path)) {
                        continue;
                    }

                    if ($this->storage->disk($disk)->getMetadata($path)['type'] === 'file') {
                        $files = [$path];
                    } else {
                        $files = $this->storage->disk($disk)->allFiles($path);
                    }

                    foreach ($files as $filename) {
                        $contents = $this->storage->disk($disk)->get($filename);

                        foreach ($pattern['actions'] as $action) {
                            $contents = $this->str->of($contents);

                            if ($matches = $contents->matchAll($action['search'])) {
                                if ($matches->isEmpty()) {
                                    continue;
                                }

                                $contents = $this->actionFactory->makeAction($action)($contents, $matches->toArray());
                            }
                        }

                        $this->storage->disk($disk)->put($filename, $contents);
                    }
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
            ->each(function ($item) use (&$paths) {
                foreach ($item as $key => $value) {
                    foreach ($value as $v) {
                        if (!array_key_exists($key, $paths)) {
                            $paths[$key] = [];
                        }

                        if (in_array($v, $paths[$key])) {
                            continue;
                        }

                        if ($this->storage->disk($key)->missing($v)) {
                            continue;
                        }

                        if ($this->storage->disk($key)->getMetadata($v)['type'] === 'dir') {
                            array_unshift($paths[$key], $v);
                        } else {
                            array_push($paths[$key], $v);
                        }
                    }
                }
            });

        return $paths;
    }
}
