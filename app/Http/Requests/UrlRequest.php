<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\PublicUrl;

class UrlRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'long_url' => [
                'required',
                'string',
                'max:2048',
                'url',
                'starts_with:http://,https://',
                new PublicUrl(),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $raw = $this->input('long_url');
        if (!is_string($raw)) {
            return;
        }

        $trimmed = trim(preg_replace('/[\x00-\x1F\x7F]/u', '', $raw));
        $parts = parse_url($trimmed);

        if ($parts === false || !isset($parts['scheme'], $parts['host'])) {
            $this->merge(['long_url' => $trimmed]);
            return;
        }

        $scheme = strtolower($parts['scheme']);
        $host = strtolower($parts['host']);
        $host = $this->wrapIpv6Host($host);

        $normalized = $scheme . '://';

        if (isset($parts['user'])) {
            $normalized .= $parts['user'];
            if (isset($parts['pass'])) {
                $normalized .= ':' . $parts['pass'];
            }
            $normalized .= '@';
        }

        $normalized .= $host;

        if (isset($parts['port'])) {
            $normalized .= ':' . $parts['port'];
        }

        $normalized .= $parts['path'] ?? '';

        if (isset($parts['query'])) {
            $normalized .= '?' . $parts['query'];
        }

        if (isset($parts['fragment'])) {
            $normalized .= '#' . $parts['fragment'];
        }

        $this->merge(['long_url' => $normalized]);
    }

    private function wrapIpv6Host(string $host): string
    {
        if (str_contains($host, ':') && !str_starts_with($host, '[') && !str_ends_with($host, ']')) {
            return '[' . $host . ']';
        }

        return $host;
    }
}
