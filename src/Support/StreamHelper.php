<?php
/**
 * Part of the "charcoal-dev/base" package.
 * @link https://github.com/charcoal-dev/base
 */

declare(strict_types=1);

namespace Charcoal\Base\Support;

/**
 * StreamHelper provides utility methods for reading streams and writing their data
 * to either temporary files or memory with size limits.
 */
final class StreamHelper
{
    public static string $tempFilePrefix = "charcoal-";

    /**
     * @param string $stream
     * @param int $maxBytes
     * @return false|array{tmpPath: string, size: int}
     */
    public static function readStreamToTempFile(string $stream, int $maxBytes): false|array
    {
        if ($maxBytes <= 0) {
            return false;
        }

        $tmpPath = tempnam(sys_get_temp_dir(), self::$tempFilePrefix);
        if (!$tmpPath) {
            throw new \RuntimeException("Failed to create temporary file for reading");
        }

        error_clear_last();
        $temp = @fopen($tmpPath, "wb");
        if (!$temp) {
            $tempFileError = ErrorHelper::lastErrorToRuntimeException();
            @unlink($tmpPath);
            throw new \RuntimeException("Failed to open temp file for writing", previous: $tempFileError);
        }

        try {
            $fileSize = self::readStreamAndWrite($stream, $temp, $maxBytes);
        } catch (\RuntimeException $e) {
            @unlink($tmpPath);
            throw $e;
        } finally {
            @fclose($temp);
        }

        return ["tmpPath" => $tmpPath, "size" => $fileSize];
    }

    /**
     * @param string $stream
     * @param int $maxBytes
     * @return string
     */
    public static function readStreamToMemory(string $stream, int $maxBytes): string
    {
        if ($maxBytes <= 0) {
            return "";
        }

        error_clear_last();
        $mem = @fopen("php://temp/maxmemory:" . $maxBytes, "w+b");
        if (!$mem) {
            throw new \RuntimeException("Failed to open temp stream",
                previous: ErrorHelper::lastErrorToRuntimeException());
        }

        try {
            self::readStreamAndWrite($stream, $mem, $maxBytes);
            rewind($mem);
            return stream_get_contents($mem) ?: "";
        } finally {
            @fclose($mem);
        }
    }

    /**
     * @param string $stream
     * @param mixed $fp2
     * @param int $maxBytes
     * @return int
     */
    private static function readStreamAndWrite(string $stream, mixed $fp2, int $maxBytes): int
    {
        error_clear_last();
        $buffered = 0;
        $chunkSize = 1 << 20;
        $fp1 = @fopen($stream, "rb");
        if (!$fp1) {
            throw new \RuntimeException("Failed to open stream for reading: " . $stream,
                previous: ErrorHelper::lastErrorToRuntimeException());
        }

        try {
            while (!feof($fp1)) {
                $remaining = $maxBytes - $buffered;
                if ($remaining <= 0) {
                    break;
                }

                $bytes = self::readFromAndWriteTo($fp1, $fp2, min($chunkSize, $remaining));
                if ($bytes <= 0) {
                    break;
                }

                if (($buffered + $bytes) > $maxBytes) {
                    throw new \OverflowException(
                        sprintf("Stream body too large; Expected %d bytes, got %d", $maxBytes, ($buffered + $bytes))
                    );
                }

                $buffered += $bytes;
            }
        } catch (\OverflowException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new \RuntimeException("Stream read terminated; Check previous", previous: $e);
        } finally {
            @fclose($fp1);
        }

        return $buffered;
    }

    /**
     * @param mixed $read
     * @param mixed $write
     * @param int $maxBytes
     * @return int
     */
    private static function readFromAndWriteTo(mixed $read, mixed $write, int $maxBytes): int
    {
        error_clear_last();
        $chunk = @fread($read, $maxBytes);
        if ($chunk === false) {
            throw new \RuntimeException("Failed to read from input stream",
                previous: ErrorHelper::lastErrorToRuntimeException());
        }

        if ($chunk === "") {
            return 0;
        }

        $chunkSize = strlen($chunk);
        if ($chunkSize > 0) {
            self::writeChunk($write, $chunk, $chunkSize);
        }

        return $chunkSize;
    }

    /**
     * Writes a chunk of data to the specified stream resource.
     */
    private static function writeChunk(mixed $fp, string $chunk, int $length): void
    {
        error_clear_last();
        $wrote = 0;
        while ($wrote < $length) {
            $written = @fwrite($fp, $chunk, $length - $wrote);
            if ($written === false) {
                throw new \RuntimeException("Failed to write to temp stream",
                    previous: ErrorHelper::lastErrorToRuntimeException());
            }

            if ($written === 0) {
                throw new \RuntimeException("Short write (0 bytes) to temp stream");
            }

            $wrote += $written;
            if ($wrote < $length) {
                $chunk = substr($chunk, $written);
            }
        }
    }
}