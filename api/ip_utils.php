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

/**
 * Get the real client IP address, handling proxies like Vercel/Cloudflare.
 */
function get_real_ip_address()
{
    $remote_addr        = ! empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    $allowed_headers    = [
        'HTTP_CF_CONNECTING_IP',
        'HTTP_TRUE_CLIENT_IP',
        'HTTP_X_REAL_IP',
        'HTTP_X_FORWARDED_FOR'
    ];

    foreach ($allowed_headers AS $header) {
        if ( ! empty($_SERVER[$header])) {
            $ips = explode(',', $_SERVER[$header]);
            foreach ($ips AS $ip) {
                $ip = trim($ip);
                $ip = preg_replace('/:\d+$/', '', $ip);
                $ip = trim($ip, '[]');
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return trim($ip);
                }
            }
        }
    }
    return $remote_addr;
}

