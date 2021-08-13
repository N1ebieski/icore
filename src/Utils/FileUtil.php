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
     * @var UploadedFile
     */
    protected $file;

    /**
     * [protected description]
     * @var string
     */
    protected $file_path;

    /**
     * [protected description]
     * @var string
     */
    protected $file_temp_path;

    /**
     * [protected description]
     * @var string|null
     */
    protected $path;

    /**
     * [protected description]
     * @var string
     */
    protected $temp_path = 'vendor/idir/temp';

    /**
     * [private description]
     * @var Storage
     */
    protected $storage;

    /**
     * [__construct description]
     * @param Storage      $storage [description]
     * @param UploadedFile $file    [description]
     * @param string|null  $path    [description]
     */
    public function __construct(Storage $storage, UploadedFile $file = null, string $path = null)
    {
        $this->storage = $storage;

        if ($file === null && is_string($path)) {
            if ($this->storage->disk('public')->exists($path)) {
                $storagePath = public_path('storage/') . $path;

                $file = new UploadedFile($storagePath, basename($storagePath), mime_content_type($storagePath), null, true);
                $path = dirname($path);
            }
        }

        $this->file = $file;
        $this->path = $path;

        if ($file instanceof UploadedFile) {
            $this->fileTempPath();
            $this->filePath();
        }
    }

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->file_path;
    }

    /**
     * @return string
     */
    public function getFileTempPath(): string
    {
        return $this->file_temp_path;
    }

    /**
     * [getFileTempPath description]
     * @return string [description]
     */
    protected function fileTempPath(): string
    {
        return $this->file_temp_path = $this->temp_path . "/" . $this->file->getClientOriginalName();
    }

    /**
     * [getFilePath description]
     * @return string [description]
     */
    protected function filePath(): string
    {
        return $this->file_path = $this->path . "/" . $this->file->getClientOriginalName();
    }

    /**
     * [prepareFile description]
     * @return string [description]
     */
    public function prepare(): string
    {
        foreach ([$this->getFilePath(), $this->getFileTempPath()] as $path) {
            if ($this->storage->disk('public')->exists($path)) {
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
        return $this->storage->disk('public')
            ->move($this->getFileTempPath(), $this->getFilePath());
    }

    /**
     * [uploadFile description]
     * @return string [description]
     */
    public function upload(): string
    {
        $this->file_path = $this->storage->disk('public')
            ->putFile($this->path, $this->file);

        return $this->getFilePath();
    }

    /**
     * [uploadFile description]
     * @return string [description]
     */
    public function uploadToTemp(): string
    {
        $this->file_temp_path = $this->storage->disk('public')
            ->putFile($this->temp_path, $this->file);

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
        if ($this->storage->disk('public')->exists($this->getFilePath())) {
            return $this->storage->disk('public')->delete($this->getFilePath());
        }

        return false;
    }
}
