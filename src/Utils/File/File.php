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

namespace N1ebieski\ICore\Utils\File;

use Illuminate\Http\UploadedFile;
use N1ebieski\ICore\Exceptions\File\NotFoundException;
use Illuminate\Contracts\Filesystem\Factory as Storage;

class File
{
    /**
     * Undocumented variable
     *
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     *
     * @param Storage $storage
     * @param UploadedFile $file
     * @param string $disk
     * @param string $temp_path
     * @return void
     */
    public function __construct(
        protected Storage $storage,
        protected UploadedFile $file,
        protected string $disk = 'public',
        protected string $temp_path = 'vendor/icore/temp'
    ) {
        $this->filesystem = $storage->disk($disk);
    }

    /**
     * Undocumented function
     *
     * @param UploadedFile $file
     * @return self
     */
    public function makeFromFile(UploadedFile $file): self
    {
        return new self($this->storage, $file, $this->disk, $this->temp_path);
    }

    /**
     *
     * @param string $path
     * @return File
     * @throws NotFoundException
     */
    public function makeFromPath(string $path): self
    {
        if (!$this->filesystem->exists($path)) {
            throw new \N1ebieski\ICore\Exceptions\File\NotFoundException();
        }

        $storagePath = $this->filesystem->path($path);

        return $this->makeFromFile(
            new UploadedFile(
                $storagePath,
                basename($storagePath),
                (mime_content_type($storagePath) ?: null),
                null,
                true
            )
        );
    }

    /**
     * Undocumented function
     *
     * @param array $paths
     * @return string|false
     */
    public function prepare(array $paths = []): mixed
    {
        foreach (array_merge($paths, [$this->temp_path]) as $path) {
            if ($this->filesystem->exists($path . "/" . $this->file->getClientOriginalName())) {
                return $path . "/" . $this->file->getClientOriginalName();
            }
        }

        return $this->uploadToTemp();
    }

    /**
     * Undocumented function
     *
     * @param string $path
     * @return string|false
     */
    public function upload(string $path): mixed
    {
        return $this->filesystem->putFile($path, $this->file);
    }

    /**
     * [uploadFile description]
     * @return string|false [description]
     */
    public function uploadToTemp(): mixed
    {
        return $this->filesystem->putFile($this->temp_path, $this->file);
    }

    /**
     * Undocumented function
     *
     * @param string $path
     * @return string
     */
    public function moveFromTemp(string $path): string
    {
        $tempPath = $this->temp_path . "/" . $this->file->getClientOriginalName();
        $toPath = $path . "/" . $this->file->getClientOriginalName();

        if (!$this->filesystem->exists($tempPath)) {
            throw new \N1ebieski\ICore\Exceptions\File\NotFoundException();
        }

        $this->filesystem->move($tempPath, $toPath);

        return $toPath;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function delete(string $path): bool
    {
        if (!$this->filesystem->exists($path)) {
            throw new \N1ebieski\ICore\Exceptions\File\NotFoundException();
        }

        return $this->filesystem->delete($path);
    }
}
