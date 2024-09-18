<?php

declare(strict_types=1);

/**
 * Digit Software Solutions.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 * @category  Dss
 * @package   Dss_CustomerAvatar
 * @author    Extension Team
 * @copyright Copyright (c) 2024 Digit Software Solutions. ( https://digitsoftsol.com )
 */

namespace Dss\CustomerAvatar\Controller\Avatar;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Url\DecoderInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Filesystem\Io\File as FileIo;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Helper\File\Storage;
use Magento\Framework\Controller\Result\Raw;

class View extends Action
{
    /**
     * Constructor
     *
     * @param Context $context
     * @param RawFactory $resultRawFactory
     * @param DecoderInterface $urlDecoder
     * @param FileFactory $fileFactory
     */
    public function __construct(
        Context $context,
        protected RawFactory $resultRawFactory,
        protected DecoderInterface $urlDecoder,
        protected FileFactory $fileFactory
    ) {
        parent::__construct($context);
    }

    /**
     * View action
     */
    public function execute()
    {
        $file = null;
        $plain = false;

        if ($this->getRequest()->getParam('file')) {
            // download file
            $file = $this->urlDecoder->decode(
                $this->getRequest()->getParam('file')
            );
        } elseif ($this->getRequest()->getParam('image')) {
            // show plain image
            $file = $this->urlDecoder->decode(
                $this->getRequest()->getParam('image')
            );
            $plain = true;
        } else {
            throw new NotFoundException(__('Page not found.'));
        }

        /** @var Filesystem $filesystem */
        $filesystem = $this->_objectManager->get(Filesystem::class);
        $directory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $fileName = CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER . '/' . ltrim($file, '/');
        $path = $directory->getAbsolutePath($fileName);

        if (!$directory->isFile($fileName)
            && !$this->_objectManager->get(Storage::class)->processStorageFile($path)
        ) {
            throw new NotFoundException(__('Page not found.'));
        }

        $fileIo = $this->_objectManager->get(FileIo::class);

        if ($plain) {
            $pathInfo = $fileIo->getPathInfo($path);
            $extension = $pathInfo['extension'] ?? '';
            switch (strtolower($extension)) {
                case 'gif':
                    $contentType = 'image/gif';
                    break;
                case 'jpg':
                    $contentType = 'image/jpeg';
                    break;
                case 'png':
                    $contentType = 'image/png';
                    break;
                default:
                    $contentType = 'application/octet-stream';
                    break;
            }
            $stat = $directory->stat($fileName);
            $contentLength = $stat['size'];
            $contentModify = $stat['mtime'];

            /** @var Raw $resultRaw */
            $resultRaw = $this->resultRawFactory->create();
            $resultRaw->setHttpResponseCode(200)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Content-type', $contentType, true)
                ->setHeader('Content-Length', $contentLength)
                ->setHeader('Last-Modified', date('r', $contentModify));
            $resultRaw->setContents($directory->readFile($fileName));
            return $resultRaw;
        } else {
            $name = $fileIo->getPathInfo($path)['basename'] ?? '';
            return $this->fileFactory->create(
                $name,
                ['type' => 'filename', 'value' => $fileName],
                DirectoryList::MEDIA
            );
        }
    }
}
