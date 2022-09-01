<?php

namespace N1ebieski\ICore\Utils\File;

use Illuminate\Http\UploadedFile;
use N1ebieski\ICore\Exceptions\File\NotFoundException;
use Illuminate\Contracts\Filesystem\Factory as Storage;

class File
{
    /**
     * [protected description]
     * @var string
     */
    protected $temp_path;

    /**
     * [protected description]
     * @var string
     */
    protected $disk;

    /**
     * Undocumented variable
     *
     * @var Storage
     */
    protected $storage;

    /**
     * Undocumented variable
     *
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * [$file description]
     * @var UploadedFile|null
     */
    protected $file;

    /**
     * Undocumented function
     *
     * @param Storage $storage
     * @param UploadedFile|null $file
     */
    public function __construct(
        Storage $storage,
        UploadedFile $file = null,
        string $disk = 'public',
        string $temp_path = 'vendor/icore/temp'
    ) {
        $this->storage = $storage;
        $this->filesystem = $storage->disk($disk);

        $this->file = $file;

        $this->disk = $disk;
        $this->temp_path = $temp_path;
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
