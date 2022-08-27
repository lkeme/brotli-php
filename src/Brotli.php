<?php
declare(strict_types=1);

namespace HelloNico\Brotli;

use HelloNico\Brotli\Exception\InvalidBinaryException;
use Symfony\Component\Process\Exception\ExceptionInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use HelloNico\Brotli\Exception\BrotliException;
use HelloNico\Brotli\Exception\CorruptInputException;
use HelloNico\Brotli\Exception\InvalidQualityException;
use loophp\phposinfo\Enum\FamilyName;
use loophp\phposinfo\OsInfo;

final class Brotli
{
    /**
     * @var string
     */
    public static string $binaryPath;

    /**
     * @var string
     */
    private static string $binaryName = 'brotli';

    /**
     * @param string $binaryPath By default, the "brotli" binary in the OS Path is used. You can change this behavior.
     */
    public static function setBinaryPath(string $binaryPath): void
    {
        self::$binaryPath = $binaryPath;
    }

    /**
     * @param string $data The raw data to compress
     * @param int $quality Compression level (0-11)
     * @return string The compressed data
     * @throws BrotliException If quality is invalid
     * @throws ExceptionInterface In case something went wrong with process
     */
    public static function compress(string $data, int $quality = 11): string
    {
        if ($quality < 0 || $quality > 11) {
            throw InvalidQualityException::create($quality);
        }

        return self::runBinary(['-q', $quality], $data);
    }

    /**
     * @param string $data The compressed data
     * @return string The uncompressed data
     * @throws BrotliException If data is not valid Brotli
     * @throws ExceptionInterface In case something went wrong with process
     */
    public static function uncompress(string $data): string
    {
        return self::runBinary(['-d'], $data);
    }

    private static function runBinary(array $arguments, string $stdin): string
    {
        if (null === self::$binaryPath) {
            self::setBinaryPath(self::getPackageBinaryPath());
        }

        array_unshift($arguments, self::$binaryPath);
        $proc = new Process($arguments, null, null, $stdin);

        try {
            $proc->mustRun();
        } catch (ProcessFailedException $exception) {
            if (strpos($proc->getErrorOutput(), 'corrupt input') === 0) {
                throw CorruptInputException::create($exception);
            }

            throw $exception;
        }

        return $proc->getOutput();
    }


    /**
     * Get binary path
     *
     * @return string
     */
    public static function getPackageBinaryPath(): string
    {
        if (null !== self::$binaryPath) {
            return self::$binaryPath;
        }

        $binaryPath = [dirname(__DIR__), 'bin'];

        $arch = strtolower(OsInfo::arch());

        if (OsInfo::isFamily(FamilyName::LINUX)) {
            array_push($binaryPath, 'linux', $arch, self::$binaryName);
        } elseif (OsInfo::isFamily(FamilyName::DARWIN)) {
            array_push($binaryPath, 'osx', $arch, self::$binaryName);
        } elseif (OsInfo::isFamily(FamilyName::WINDOWS)) {
            array_push($binaryPath, 'windows', $arch, self::$binaryName . '.exe');
        }

        $binaryPath = implode(DIRECTORY_SEPARATOR, $binaryPath);

        if (!is_file($binaryPath)) {
            throw InvalidBinaryException::create($binaryPath);
        }

        return self::$binaryPath = $binaryPath;
    }
}
