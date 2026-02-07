<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'URL Shortener') }}</title>
    <style>
        :root {
            color-scheme: light;
        }
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
            color: #111827;
            background: #f8fafc;
        }
        .wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 20px;
        }
        .card {
            width: 100%;
            max-width: 560px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 28px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
        }
        h1 {
            margin: 0 0 8px 0;
            font-size: 24px;
            font-weight: 600;
        }
        p {
            margin: 0 0 20px 0;
            color: #4b5563;
        }
        form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        input[type="url"],
        input[type="text"] {
            flex: 1 1 320px;
            padding: 12px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
        }
        button {
            padding: 12px 16px;
            border: 0;
            border-radius: 8px;
            background: #111827;
            color: #ffffff;
            font-size: 14px;
            cursor: pointer;
        }
        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .hint {
            margin-top: 14px;
            font-size: 12px;
            color: #6b7280;
        }
        .footer {
            margin-top: 28px;
            font-size: 12px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <main class="card">
            <h1>Shorten a URL</h1>
            <p>Phase 1: minimal UI. Paste a long URL to create a short one.</p>

            <form method="POST" action="{{ route('urls.store') }}" autocomplete="off">
                @csrf
                <input type="url" name="long_url" placeholder="https://example.com/very/long/link" required value="{{ old('long_url') }}">
                <button type="submit">Shorten</button>
            </form>

            @if ($errors->any())
                <div class="hint error">
                    {{ $errors->first('long_url') }}
                </div>
            @endif

            @if (session('short_url'))
                <div class="hint">
                    Short URL: <strong>{{ session('short_url') }}</strong>
                </div>
            @endif

            <div class="hint">Test testttt.</div>
            <div class="footer">{{ config('app.name', 'URL Shortener') }} · MVP</div>
        </main>
    </div>
</body>
</html>
