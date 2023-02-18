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

namespace N1ebieski\ICore\Utils\File\Interfaces;

use Illuminate\Http\UploadedFile;

interface FileInterface
{
    /**
     * Undocumented function
     *
     * @param UploadedFile $file
     * @return self
     */
    public function makeFromFile(UploadedFile $file): self;

    /**
     *
     * @param string $path
     * @return self
     */
    public function makeFromPath(string $path): self;

    /**
     * Undocumented function
     *
     * @param array $paths
     * @return string|false
     */
    public function prepare(array $paths = []): mixed;

    /**
     * Undocumented function
     *
     * @param string $path
     * @return string|false
     */
    public function upload(string $path): mixed;

    /**
     * [uploadFile description]
     * @return string|false [description]
     */
    public function uploadToTemp(): mixed;

    /**
     * Undocumented function
     *
     * @param string $path
     * @return string
     */
    public function moveFromTemp(string $path): string;

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function delete(string $path): bool;
}
