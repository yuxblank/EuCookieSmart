{* todo refactor
/**
 * NOTICE OF LICENSE
 *
 * only18plus is a module for blocking and verifying user age
 * Copyright (C) 2017 Yuri Blanc
 * Email: info@yuriblanc.it
 * Website: www.yuriblanc.it
 *
 * This program is distributed WITHOUT ANY WARRANTY;
 */
*}


<script>

    var euCookieSmartConfig = {
        message: '{$cookieMsg}',
        acceptButton: '{$EUCOOKIESMART_BUTTON_ACCEPT}',
        acceptText: '{$cookieAcceptText|escape:"quotes"}',
        declineButton: false,
        declineText: '{$cookieDeclineText}',
        policyButton: '{$EUCOOKIESMART_BUTTON_POLICY}',
        policyText: '{$cookiePolicyText|escape:"quotes"}',
        policyURL: '{$policyCMSlink}',
        acceptOnContinue: '{$EUCOOKIESMART_ACCEPT_CONTINUE}',
        expireDays: '{$EUCOOKIESMART_EXPIRE_DAYS|escape:"quotes"}',
        renewOnVisit: '{$EUCOOKIESMART_RENEW_VISIT}',
        effect: '{$EUCOOKIESMART_EFFECT|escape:"quotes"}',
        fixed: '{$EUCOOKIESMART_FIXED|boolval}',
        bottom: '{$EUCOOKIESMART_BOTTOM}'
    };


</script>