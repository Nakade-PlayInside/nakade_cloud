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

class KgsArchivesDownload
{
    use KgsArchivesLoggerTrait;

    private const SGF_DIR = 'sgf';

    public function download(string $uri, string $saveDir): ?string
    {
        $saveFile = $this->getFilePath($uri, $saveDir);

        $contents = $this->getContents($uri);
        if ($contents) {
            $saveFile = $this->putContents($saveFile, $contents);
        }

        return $saveFile;
    }

    private function putContents(string $file, string $contents): ?string
    {
        if (false === file_put_contents($file, $contents)) {
            $this->logger->critical(sprintf('Failed to save file: "%s" !', $file));

            return null;
        }

        return $file;
    }

    private function getContents(string $uri): ?string
    {
        $contents = file_get_contents($uri);
        if (false === $contents) {
            $this->logger->alert(sprintf('Failed to download file: "%s" !', $uri));

            return null;
        }

        return $contents;
    }

    private function getFilePath(string $uri, string $saveDir): string
    {
        $savePath = $this->createDir($saveDir);
        $parts = pathinfo($uri);
        $baseName = $parts['basename'];

        return $savePath.'/'.$baseName;
    }

    private function createDir(string $saveDir): string
    {
        $dir = self::SGF_DIR.'/'.$saveDir;
        if (!file_exists($dir)) {
            if (!mkdir($dir, 0755, true)) {
                $msg = sprintf('Failed to create directory <%s>!', $dir);
                $this->logger->critical($msg);
                throw new \LogicException($msg);
            }
        }

        return $dir;
    }
}
