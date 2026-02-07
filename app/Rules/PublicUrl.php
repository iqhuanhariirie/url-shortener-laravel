<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PublicUrl implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('The :attribute must be a valid URL.');
            return;
        }

        $parts = parse_url($value);
        if ($parts === false || !isset($parts['host'])) {
            $fail('The :attribute must be a valid public URL.');
            return;
        }

        if (isset($parts['scheme']) && !in_array(strtolower($parts['scheme']), ['http', 'https'], true)) {
            $fail('The :attribute must start with http:// or https://.');
            return;
        }

        $host = strtolower($parts['host']);

        if ($host === 'localhost') {
            $fail('The :attribute must be a public URL.');
            return;
        }

        if ($this->isPrivateIp($host)) {
            $fail('The :attribute must be a public URL.');
        }
    }

    private function isPrivateIp(string $host): bool
    {
        if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return $this->isPrivateIpv4($host);
        }

        if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return $this->isPrivateIpv6($host);
        }

        return false;
    }

    private function isPrivateIpv4(string $ip): bool
    {
        $long = ip2long($ip);
        if ($long === false) {
            return false;
        }

        $ranges = [
            ['10.0.0.0', '10.255.255.255'],
            ['172.16.0.0', '172.31.255.255'],
            ['192.168.0.0', '192.168.255.255'],
            ['127.0.0.0', '127.255.255.255'],
            ['169.254.0.0', '169.254.255.255'],
        ];

        foreach ($ranges as [$start, $end]) {
            $startLong = ip2long($start);
            $endLong = ip2long($end);
            if ($startLong === false || $endLong === false) {
                continue;
            }
            if ($long >= $startLong && $long <= $endLong) {
                return true;
            }
        }

        return false;
    }

    private function isPrivateIpv6(string $ip): bool
    {
        $normalized = strtolower($ip);

        if ($normalized === '::1') {
            return true;
        }

        $prefix = substr($normalized, 0, 2);
        if ($prefix === 'fc' || $prefix === 'fd') {
            return true; // fc00::/7
        }

        $linkLocalPrefixes = ['fe8', 'fe9', 'fea', 'feb'];
        foreach ($linkLocalPrefixes as $linkLocalPrefix) {
            if (str_starts_with($normalized, $linkLocalPrefix)) {
                return true; // fe80::/10
            }
        }

        return false;
    }
}
