<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein-Youtube-Bundle.
 *
 * (c) Werbeagentur Dreibein GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace Dreibein\YoutubeBundle\Youtube;

use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Database;
use Contao\Files;
use Contao\FilesModel;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class PreviewImage
{
    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Database
     */
    private $database;

    /**
     * PreviewImage constructor.
     *
     * @param KernelInterface $kernel
     * @param LoggerInterface $logger
     */
    public function __construct(KernelInterface $kernel, LoggerInterface $logger)
    {
        $this->rootDir = $kernel->getContainer()->getParameter('kernel.project_dir');
        $this->logger = $logger;
        $this->database = Database::getInstance();
    }

    /**
     * Load the file model of a preview image by the given YouTube id.
     * If the directory of the file could not be loaded, they are created.
     *
     * @param string $youtubeId
     *
     * @return FilesModel|null
     */
    public function getImageById(string $youtubeId): ?FilesModel
    {
        try {
            // load the file from YouTube
            $data = file_get_contents('https://img.youtube.com/vi/' . $youtubeId . '/hqdefault.jpg');
        } catch (\Exception $e) {
            return null;
        }
        if (false === $data) {
            // image could not be loaded from youtube
            return null;
        }

        $dir = 'files/video-preview';
        $fileName = 'preview-' . $youtubeId . '.jpg';

        // Check if the file is already in the file system
        $fileModel = FilesModel::findByPath($dir . '/' . $fileName);
        if (null === $fileModel) {
            $fileModel = $this->createPreviewImage($dir, $fileName, $data);

            if (null === $fileModel) {
                // file model could not be created
                return null;
            }
        }

        return $fileModel;
    }

    /**
     * Try to create the preview image in the file system and save the new entry in the database.
     *
     * @param string $dir
     * @param string $fileName
     * @param string $data
     *
     * @return FilesModel|null
     */
    private function createPreviewImage(string $dir, string $fileName, string $data): ?FilesModel
    {
        // check if the directory exists
        $dirModel = FilesModel::findByPath($dir);
        if (null === $dirModel) {
            // try to create the directory
            $dirModel = $this->createPreviewDirectory($dir);

            // if creating the directory fails, stop the function
            if (null === $dirModel) {
                return null;
            }
        }

        // create the file and write the data to it
        try {
            $file = Files::getInstance();
            $fileResource = $file->fopen($dir . '/' . $fileName, 'wb');
            $file->fputs($fileResource, $data);
            $file->fclose($fileResource);
        } catch (\Throwable $e) {
            $this->logger->error(
                'Error on creating YouTube-preview-image: ' . $e->getMessage(),
                ['contao' => new ContaoContext(__METHOD__, 'ERROR')]
            );
        }

        // create the database entry for the preview file
        $fileModel = new FilesModel();
        $fileModel->pid = $dirModel->id;
        $fileModel->tstamp = time();
        $fileModel->name = $fileName;
        $fileModel->type = 'file';
        $fileModel->path = $dir . '/' . $fileName;
        $fileModel->extension = 'jpg';
        $fileModel->hash = md5_file($this->rootDir . '/' . $dir . '/' . $fileName);
        $fileModel->uuid = $this->database->getUuid();

        // return the model of the file
        return $fileModel->save();
    }

    /**
     * Create the missing directory to store the preview images.
     *
     * @param string $dir
     *
     * @return FilesModel|null
     */
    private function createPreviewDirectory(string $dir): ?FilesModel
    {
        $file = Files::getInstance();

        // try to create the missing directory
        if (!$file->mkdir($dir)) {
            $this->logger->error(
                'Error while creating YouTube-preview-directory',
                ['contao' => new ContaoContext(__METHOD__, 'ERROR')]
            );

            return null;
        }

        // create database entry for the directory
        $dirModel = new FilesModel();
        $dirModel->pid = null;
        $dirModel->tstamp = time();
        $dirModel->name = 'video_preview';
        $dirModel->type = 'folder';
        $dirModel->path = $dir;
        $dirModel->extension = '';
        $dirModel->hash = md5_file($this->rootDir . '/' . $dir);
        $dirModel->uuid = $this->database->getUuid();
        $dirModel = $dirModel->save();

        try {
            // generate the empty .public file inside the preview directory
            $publicFile = $file->fopen($dir . '/.public', 'wb');
            $file->fclose($publicFile);
        } catch (\Exception $e) {
            $this->logger->error(
                'Error while creating the .public file inside the YouTube-preview-directory',
                ['contao' => new ContaoContext(__METHOD__, 'ERROR')]
            );
        }

        // return the model of the directory
        return $dirModel;
    }
}
