<?php

declare(strict_types=1);
/**
 * @license MIT License <https://opensource.org/licenses/MIT>
 *
 * Copyright (c) 2019 Dr. Holger Maerz
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace App\Services;

use App\Logger\KgsArchivesLoggerTrait;
use Symfony\Component\HttpKernel\KernelInterface;

class KgsArchivesDownload
{
    use KgsArchivesLoggerTrait;

    private const SGF_DIR = 'sgf';
    private $uploadDir;

    public function __construct(KernelInterface $appKernel)
    {
        $this->uploadDir = $appKernel->getProjectDir().'/public';
    }

    /**
     * Creates save directory if not existing. Downloads the file and saves the file to the upload directory.
     * Returns the local path on success.
     */
    public function download(string $uri, string $subDir): ?string
    {
        //var/www/nakade/public/sgf/2016_17
        $dir = $this->uploadDir.'/'.self::SGF_DIR.'/'.$subDir;
        if (!$this->createDir($dir)) {
            $msg = sprintf('Failed to create directory <%s>!', $dir);
            $this->logger->critical($msg);
            throw new \LogicException($msg);
        }

        $contents = file_get_contents($uri);
        if (false === $contents) {
            $this->logger->alert(sprintf('Failed to download file: "%s" !', $uri));

            return null;
        }

        $parts = pathinfo($uri);
        $basename = $parts['basename'];
        $file = $dir.'/'.$basename;
        if (false === file_put_contents($file, $contents)) {
            $this->logger->critical(sprintf('Failed to save file: "%s" !', $file));

            return null;
        }
        //local save path for entity
        return self::SGF_DIR.'/'.$subDir.'/'.$basename;
    }

    private function createDir(string $dir): bool
    {
        if (file_exists($dir)) {
            return true;
        }

        return mkdir($dir, 0755, true);
    }
}
