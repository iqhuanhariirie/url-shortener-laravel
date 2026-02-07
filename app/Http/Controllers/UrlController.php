<?php

namespace App\Http\Controllers;

use App\Http\Requests\UrlRequest;
use App\Models\Url;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class UrlController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UrlRequest $request)
    {
        $data = $request->validated();

        $code = $this->generateShortCode();

        $url = Url::create([
            'long_url' => $data['long_url'],
            'short_url' => $code,
        ]);

        $this->cacheShortUrl($url->short_url, $url->long_url);

        return redirect()
            ->back()
            ->with([
                'short_url' => url($url->short_url),
                'long_url' => $url->long_url,
            ]);
    }

    /**
     * Redirect a short code to its long URL.
     */
    public function redirect(string $code)
    {
        $longUrl = $this->getCachedLongUrl($code);

        if ($longUrl === null) {
            abort(404);
        }

        return redirect()->away($longUrl);
    }

    /**
     * Display the specified resource.
     */
    public function show(Url $url)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Url $url)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UrlRequest $request, Url $url)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Url $url)
    {
        //
    }

    private function generateShortCode(int $length = 6): string
    {
        do {
            $code = Str::random($length);
        } while (Url::where('short_url', $code)->exists());

        return $code;
    }

    private function getCachedLongUrl(string $code): ?string
    {
        $cacheKey = $this->cacheKey($code);
        $cached = Cache::get($cacheKey);

        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        $longUrl = Url::where('short_url', $code)->value('long_url');

        if (is_string($longUrl) && $longUrl !== '') {
            $this->cacheShortUrl($code, $longUrl);
        }

        return $longUrl ?: null;
    }

    private function cacheShortUrl(string $code, string $longUrl): void
    {
        Cache::put($this->cacheKey($code), $longUrl, now()->addHours(24));
    }

    private function cacheKey(string $code): string
    {
        return 'short_url:' . $code;
    }
}
