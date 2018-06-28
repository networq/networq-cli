<?php

namespace Networq\Cli\Utils;

require __DIR__.'/../../vendor/autoload.php';

use Composer\Script\Event;
use Seld\PharUtils\Timestamps;
use Symfony\Component\Finder\Finder;

/**
 * Class Compiler
 *
 * @package Networq\Cli\Utils
 */
class Compiler
{
    const DIR = __DIR__.'/../..';

    /**
     * @param Event $event
     */
    public static function buildPhar(Event $event): void
    {
        try {
            self::compile('networq.phar');
        } catch (\Exception $e) {
            echo 'Error occured: '.$e->getMessage();
        }
    }

    /**
     * @param string      $fileName
     * @param string|null $alias
     * @param int|null    $flags
     *
     * @throws \Exception
     */
    public static function compile(string $fileName, string $alias = null, int $flags = null): void
    {
        if (empty($fileName)) {
            throw new \Exception('Empty filename');
        }

        if (file_exists($fileName)) {
            unlink($fileName);
        }

        if ($alias === null) {
            $alias = $fileName;
        }

        $phar = new \Phar($fileName, $flags, $alias);
        $phar->setSignatureAlgorithm(\Phar::SHA1);
        $phar->startBuffering();

        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->name('.editorconfig')
            // preserve windows tools
            ->name('*.exe')
            // exclude test sources
            ->exclude('Tests')
            ->exclude('tests')
            ->exclude('docs')
            ->exclude('example')
            // exclude dev/unused dependencies
            ->exclude('vendor/seld')
            ->exclude('vendor/linkorb')
            ->notPath('vendor/symfony/finder')
            // exclude Utils folder
            ->notPath(__DIR__)
            ->in(__DIR__.'/../..');

        foreach ($finder as $file) {
            self::addFile($phar, $file);
        }

        self::addBin($phar);
        $phar->stopBuffering();

        $phar->setStub($phar->createDefaultStub('bin/networq'));

        unset($phar);

        // signature
        $util = new Timestamps($fileName);
        $util->updateTimestamps(new \DateTime());
        $util->save($fileName, \Phar::SHA1);
    }

    /**
     * @param \Phar $phar
     * @param       $file
     */
    private static function addFile(\Phar $phar, $file): void
    {
        $path = self::getRelativeFilePath($file);
        $content = file_get_contents($file);
        $phar->addFromString($path, $content);
    }

    /**
     * @param  \SplFileInfo $file
     *
     * @return string
     */
    private static function getRelativeFilePath($file)
    {
        $realPath = $file->getRealPath();
        $pathPrefix = dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR;
        $pos = strpos($realPath, $pathPrefix);
        $relativePath = ($pos !== false) ? substr_replace($realPath, '', $pos, strlen($pathPrefix)) : $realPath;

        return strtr($relativePath, '\\', '/');
    }

    /**
     * @param \Phar $phar
     */
    private static function addBin(\Phar $phar): void
    {
        $content = file_get_contents(__DIR__.'/../../bin/networq');
        $content = preg_replace('{^#!/usr/bin/env php\s*}', '', $content);
        $phar->addFromString('bin/networq', $content);
    }
}
