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

namespace Dss\CustomerAvatar\Plugin\Metadata\Form;

use Dss\CustomerAvatar\Model\Source\Validation\Image as ValidationImage;
use Magento\Framework\App\RequestInterface;

class Image
{
    /**
     * Image constructor.
     *
     * @param ValidationImage $validImage
     * @param RequestInterface $request
     */
    public function __construct(
        private ValidationImage $validImage,
        private RequestInterface $request
    ) {
    }

    /**
     * @inheritdoc
     */
    public function beforeExtractValue(\Magento\Customer\Model\Metadata\Form\Image $subject, $value)
    {
        $attrCode = $subject->getAttribute()->getAttributeCode();

        // Retrieve the file from the request object
        $file = $this->request->getFiles($attrCode);

        // Validate the image file
        if ($this->validImage->isImageValid($file['tmp_name'] ?? '', $attrCode) === false) {
            // Remove the file from $_FILES if validation fails
            $files = $this->request->getFiles();
            if (isset($files[$attrCode])) {
                unset($files[$attrCode]);
                $this->request->setFiles($files);
            }
        }

        return [$value];
    }
}
