{*
/**
 * NOTICE OF LICENSE
 *
 * EuCookieSmart is a module for display a cookie law banner.
 * Copyright (C) 2017 Yuri Blanc
 * Email: yuxblank@gmail.com
 * Website: www.yuriblanc.it
 *
 * This program is distributed WITHOUT ANY WARRANTY
 * @license GNU General Public License v3.0
 */
*}


<script>

    var euCookieSmartConfig = {
        message: "{$cookieMsg}",
        acceptButton: "{$EUCOOKIESMART_BUTTON_ACCEPT}",
        acceptText: "{$cookieAcceptText|escape:"quotes"}",
        declineButton: {$EUCOOKIESMART_BUTTON_DECLINE},
        declineText: "{$cookieDeclineText}",
        policyButton: "{$EUCOOKIESMART_BUTTON_POLICY}",
        policyText: "{$cookiePolicyText|escape:"quotes"}",
        policyURL: "{$policyCMSlink}",
        acceptOnContinue: "{$EUCOOKIESMART_ACCEPT_CONTINUE}",
        expireDays: "{$EUCOOKIESMART_EXPIRE_DAYS|escape:"quotes"}",
        renewOnVisit: "{$EUCOOKIESMART_RENEW_VISIT}",
        effect: "{$EUCOOKIESMART_EFFECT|escape:"quotes"}",
        fixed: "{$EUCOOKIESMART_FIXED|boolval}",
        bottom: "{$EUCOOKIESMART_BOTTOM}"
    };


</script>
