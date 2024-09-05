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

namespace Dss\CustomerAvatar\Model\Attribute\Backend;

use Dss\CustomerAvatar\Model\Source\Validation\Image;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

class Avatar extends AbstractBackend
{
    /**
     * Constructor
     *
     * @param Image $imageValidator
     */
    public function __construct(
        protected Image $imageValidator
    ) {
    }

    /**
     * Before save plugin
     *
     * @param DataObject $object
     * @return $this
     * @throws LocalizedException
     */
    public function beforeSave($object): self
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        if ($attrCode == 'profile_picture') {
            if ($this->imageValidator->isImageValid('tmp_name', $attrCode) === false) {
                throw new LocalizedException(
                    __('The profile picture is not a valid image.')
                );
            }
        }

        return parent::beforeSave($object);
    }
}
