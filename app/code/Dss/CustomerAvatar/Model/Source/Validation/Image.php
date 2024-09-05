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

namespace Dss\CustomerAvatar\Model\Source\Validation;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\UploaderFactory;

class Image
{
    /**
     * Constructor
     *
     * @param RequestInterface $request
     * @param UploaderFactory $uploaderFactory
     */
    public function __construct(
        protected RequestInterface $request,
        protected UploaderFactory $uploaderFactory
    ) {
    }

    /**
     * Check the image file
     *
     * @param string $attrCode
     * @return bool
     * @throws LocalizedException
     */
    public function isImageValid(string $attrCode): bool
    {
        if ($attrCode === 'profile_picture') {
            try {
                $uploader = $this->uploaderFactory->create(['fileId' => $attrCode]);
                $filePath = $uploader->validateFile()['tmp_name'];

                $imageFile = getimagesizefromstring($filePath);
                if ($imageFile === false) {
                    throw new LocalizedException(__('Invalid image file.'));
                }

                $validTypes = ['image/gif', 'image/jpeg', 'image/png'];
                if (!in_array($imageFile['mime'], $validTypes, true)) {
                    throw new LocalizedException(__('Invalid image type. Only GIF, JPEG, and PNG are allowed.'));
                }

                return true;
            } catch (\Exception $e) {
                throw new LocalizedException(__('Image validation failed: %1', $e->getMessage()));
            }
        }

        return true;
    }
}
