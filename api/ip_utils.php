<?php

/**
 * Check if an IP address is in a CIDR range or matches a specific IP.
 */
function ip_is_blacklisted($ip, $blacklist) {
    foreach ($blacklist as $blocked) {
        if (strpos($blocked, '/') !== false) {
            if (ip_in_range($ip, $blocked)) {
                return true;
            }
        } elseif ($ip === $blocked) {
            return true;
        }
    }
    return false;
}

/**
 * Check if an IP is in a CIDR range.
 */
function ip_in_range($ip, $range) {
    list($range, $netmask) = explode('/', $range, 2);
    $range_decimal = ip2long($range);
    $ip_decimal = ip2long($ip);
    $wildcard_decimal = pow(2, (32 - $netmask)) - 1;
    $netmask_decimal = ~ $wildcard_decimal;
    return (($ip_decimal & $netmask_decimal) == ($range_decimal & $netmask_decimal));
}
