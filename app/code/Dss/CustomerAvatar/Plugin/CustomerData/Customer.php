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

namespace Dss\CustomerAvatar\Plugin\CustomerData;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Helper\View;
use Dss\CustomerAvatar\Block\Attributes\Avatar;

class Customer
{
    /**
     * @param CurrentCustomer $currentCustomer
     * @param View $customerViewHelper
     * @param Avatar $customerAvatar
     */
    public function __construct(
        protected CurrentCustomer $currentCustomer,
        protected View $customerViewHelper,
        protected Avatar $customerAvatar
    ) {
    }

    /**
     * @inheritdoc
     */
    public function afterGetSectionData()
    {
        if (!$this->currentCustomer->getCustomerId()) {
            return [];
        }
        $customer = $this->currentCustomer->getCustomer();
        if (!empty($customer->getCustomAttribute('profile_picture'))) {
            $file = $customer->getCustomAttribute('profile_picture')->getValue();
        } else {
            $file = '';
        }
        return [
            'fullname' => $this->customerViewHelper->getCustomerName($customer),
            'firstname' => $customer->getFirstname(),
            'websiteId' => $customer->getWebsiteId(),
            'avatar' => $this->customerAvatar->getAvatarCurrentCustomer($file)
        ];
    }
}
