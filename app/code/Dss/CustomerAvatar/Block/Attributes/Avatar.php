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

namespace Dss\CustomerAvatar\Block\Attributes;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Customer\Model\Customer;
use Magento\MediaStorage\Helper\File\Storage;

class Avatar extends Template
{
    /**
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param Repository $viewFileUrl
     * @param Customer $customer
     */
    public function __construct(
        Context $context,
        protected ObjectManagerInterface $objectManager,
        protected Repository $viewFileUrl,
        protected Customer $customer
    ) {
        parent::__construct($context);
    }

    /**
     * Check the file is already exist in the path.
     *
     * @param string $file
     * @return bool
     */
    public function checkImageFile(string $file): bool
    {
        $filesystem = $this->objectManager->get(\Magento\Framework\Filesystem::class);
        $directory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $fileName = CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER . '/' . ltrim($file, '/');
        $path = $directory->getAbsolutePath($fileName);
        if (!$directory->isFile($fileName)
            && !$this->objectManager->get(Storage::class)->processStorageFile($path)
        ) {
            return false;
        }
        return true;
    }

    /**
     * Get the avatar of the customer is already logged in
     *
     * @param string $file
     * @return string
     */
    public function getAvatarCurrentCustomer(string $file): string
    {
        if ($this->checkImageFile(($file)) === true) {
            return $this->getUrl('viewfile/avatar/view/', ['image' => base64_encode($file)]);
        }
        return $this->viewFileUrl->getUrl('Dss_CustomerAvatar::images/no-profile-photo.jpg');
    }

    /**
     * Get the avatar of the customer by the customer id
     *
     * @param int|bool $customer_id
     * @return string
     */
    public function getCustomerAvatarById(int|bool $customer_id = false): string
    {
        if ($customer_id) {
            $customerDetail = $this->customer->load($customer_id);
            if ($customerDetail && !empty($customerDetail->getProfilePicture())) {
                if ($this->checkImageFile(($customerDetail->getProfilePicture())) === true) {
                    return $this->getUrl(
                        'viewfile/avatar/view/',
                        ['image' => base64_encode($customerDetail->getProfilePicture())]
                    );
                }
            }
        }
        return $this->viewFileUrl->getUrl('Dss_CustomerAvatar::images/no-profile-photo.jpg');
    }
}
