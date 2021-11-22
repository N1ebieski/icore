<?php

namespace N1ebieski\ICore\Utils;

use Illuminate\Http\UploadedFile;
use N1ebieski\ICore\Utils\Traits\Factory;
use Illuminate\Contracts\Filesystem\Factory as Storage;

class FileUtil
{
    use Factory;

    /**
     * [$file description]
     * @var UploadedFile|null
     */
    protected $file;

    /**
     * [protected description]
     * @var string
     */
    protected $file_path = null;

    /**
     * [protected description]
     * @var string
     */
    protected $file_temp_path = null;

    /**
     * [protected description]
     * @var string|null
     */
    protected $path;

    /**
     * [protected description]
     * @var string
     */
    protected $temp_path = 'vendor/icore/temp';

    /**
     * [private description]
     * @var Storage
     */
    protected $storage;

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $disk;

    /**
     * Undocumented function
     *
     * @param Storage $storage
     * @param string $path
     * @param UploadedFile $file
     * @param string $disk
     */
    public function __construct(
        Storage $storage,
        string $path = null,
        UploadedFile $file = null,
        string $disk = 'public'
    ) {
        $this->storage = $storage;

        $this->path = $path;
        $this->file = $file;
        $this->disk = $disk;

        if ($this->file === null && !empty($this->path)) {
            $this->setFileFromPath($this->path);
        }

        if ($this->file instanceof UploadedFile) {
            $this->setFileTempPathFromFile($this->file);
            $this->setFilePathFromFile($this->file);
        }
    }

    /**
     * Undocumented function
     *
     * @param UploadedFile $file
     * @return static
     */
    protected function setFileTempPathFromFile(UploadedFile $file)
    {
        $this->file_temp_path = $this->temp_path . "/" . $file->getClientOriginalName();

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param UploadedFile $file
     * @return static
     */
    protected function setFilePathFromFile(UploadedFile $file)
    {
        $this->file_path = $this->path . "/" . $file->getClientOriginalName();

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $path
     * @return static
     */
    protected function setFileFromPath(string $path)
    {
        if ($this->storage->disk($this->disk)->exists($path)) {
            $storagePath = public_path('storage/') . $path;

            $this->file = new UploadedFile($storagePath, basename($storagePath), mime_content_type($storagePath), null, true);
            $this->path = dirname($path);
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFilePath(): ?string
    {
        return $this->file_path;
    }

    /**
     * @return string|null
     */
    public function getFileTempPath(): ?string
    {
        return $this->file_temp_path;
    }

    /**
     * [prepareFile description]
     * @return string [description]
     */
    public function prepare(): string
    {
        foreach ([$this->getFilePath(), $this->getFileTempPath()] as $path) {
            if ($this->storage->disk($this->disk)->exists($path)) {
                return $path;
            }
        }

        return $this->uploadToTemp();
    }

    /**
     * [move description]
     * @return bool [description]
     */
    public function moveFromTemp(): bool
    {
        return $this->storage->disk($this->disk)->move($this->getFileTempPath(), $this->getFilePath());
    }

    /**
     * [uploadFile description]
     * @return string [description]
     */
    public function upload(): string
    {
        $this->file_path = $this->storage->disk($this->disk)->putFile($this->path, $this->file);

        return $this->getFilePath();
    }

    /**
     * [uploadFile description]
     * @return string [description]
     */
    public function uploadToTemp(): string
    {
        $this->file_temp_path = $this->storage->disk($this->disk)->putFile($this->temp_path, $this->file);

        $this->file_path = $this->path . "/" . basename($this->getFileTempPath());

        return $this->getFileTempPath();
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function delete(): bool
    {
        if ($this->storage->disk($this->disk)->exists($this->getFilePath())) {
            return $this->storage->disk($this->disk)->delete($this->getFilePath());
        }

        return false;
    }
}
