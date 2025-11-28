<?php

use OxaPay\Laravel\OxaPayManager;

if (!function_exists('oxapay')) {
    /**
     * @return OxaPayManager
     */
    function oxapay(): OxaPayManager
    {
        return app('oxapay.manager');
    }
}
