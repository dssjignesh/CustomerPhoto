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
require([
        'jquery',
        'jquery/ui',
        'jquery/validate',
        'mage/translate'
    ], function ($) {
    //Validate Image FileSize
        $('.avatar.validate-image').on('change', function () {
            $('.profile-image, .avatar-file-upload').css({'opacity':'0.5'});
        });
        $.validator.addMethod(
            'validate-image',
            function (v, elm) {
                if (elm.value != '') {
                    var ext = elm.value.split('.').pop().toLowerCase();
                    if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
                        return false;
                    }
                }
                return true;
            },
            $.mage.__('Image invalid (Accepting format .gif .png .jpg .jpeg)')
        );
    });
