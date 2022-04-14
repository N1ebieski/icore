<?php

namespace N1ebieski\ICore\Utils\File;

use Illuminate\Http\UploadedFile;
use Illuminate\Contracts\Filesystem\Factory as Storage;

class FileUtil
{
    /**
     * [protected description]
     * @var string
     */
    protected $temp_path;

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

        $this->temp_path = $temp_path;
    }

    /**
     * Undocumented function
     *
     * @param UploadedFile $file
     * @return static
     */
    public function makeFromFile(UploadedFile $file)
    {
        return new static($this->storage, $file, $this->disk, $this->temp_path);
    }

    /**
     * Undocumented function
     *
     * @param UploadedFile $file
     * @return static
     */
    public function makeFromPath(string $path)
    {
        if (!$this->filesystem->exists($path)) {
            throw new \N1ebieski\ICore\Exceptions\File\NotFoundException();
        }

        $storagePath = $this->filesystem->path($path);

        return $this->makeFromFile(
            new UploadedFile($storagePath, basename($storagePath), mime_content_type($storagePath), null, true)
        );
    }

    /**
     * Undocumented function
     *
     * @param mixed $paths
     * @return string
     */
    public function prepare($paths): string
    {
        foreach ((array)$paths as $path) {
            if ($this->filesystem->exists($path)) {
                return $path;
            }
        }

        return $this->uploadToTemp();
    }

    /**
     * Undocumented function
     *
     * @param string $path
     * @return string
     */
    public function upload(string $path): string
    {
        return $this->filesystem->putFile($path, $this->file);
    }

    /**
     * [uploadFile description]
     * @return string [description]
     */
    public function uploadToTemp(): string
    {
        return $this->filesystem->putFile($this->temp_path, $this->file);
    }

    /**
     * Undocumented function
     *
     * @param string $path
     * @return boolean
     */
    public function moveFromTemp(string $path): bool
    {
        if (
            !$this->filesystem->exists(
                $this->temp_path . "/" . $this->file->getClientOriginalName()
            )
        ) {
            throw new \N1ebieski\ICore\Exceptions\File\NotFoundException();
        }

        return $this->filesystem->move(
            $this->temp_path . "/" . $this->file->getClientOriginalName(),
            $path . "/" . $this->file->getClientOriginalName()
        );
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
