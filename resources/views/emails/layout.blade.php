<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Enduroam')</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f4f4f7; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        .email-wrapper { width: 100%; background-color: #f4f4f7; padding: 40px 0; }
        .email-container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        .email-header { background-color: #1a1a2e; padding: 32px; text-align: center; }
        .email-header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: 1px; }
        .email-body { padding: 32px; color: #333333; line-height: 1.6; font-size: 15px; }
        .email-body h2 { color: #1a1a2e; font-size: 20px; margin: 0 0 16px; }
        .email-body p { margin: 0 0 16px; }
        .email-footer { background-color: #f8f9fa; padding: 24px 32px; text-align: center; font-size: 13px; color: #888888; border-top: 1px solid #eeeeee; }
        .email-footer a { color: #1a1a2e; text-decoration: none; }
        .detail-table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        .detail-table td { padding: 10px 12px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
        .detail-table td:first-child { color: #888888; width: 140px; }
        .detail-table td:last-child { font-weight: 500; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 13px; font-weight: 600; text-transform: capitalize; }
        .badge--pending { background: #fff3cd; color: #856404; }
        .badge--confirmed { background: #d4edda; color: #155724; }
        .badge--completed { background: #cce5ff; color: #004085; }
        .badge--cancelled { background: #f8d7da; color: #721c24; }
        .btn { display: inline-block; padding: 12px 24px; background-color: #1a1a2e; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px; }
        .quote { background: #f8f9fa; border-left: 3px solid #dee2e6; padding: 16px; margin: 16px 0; font-size: 14px; color: #666666; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <div class="email-header">
                <h1>Enduroam</h1>
            </div>
            <div class="email-body">
                @yield('content')
            </div>
            <div class="email-footer">
                <p>&copy; {{ date('Y') }} Enduroam. All rights reserved.</p>
                <p><a href="{{ config('app.url') }}">enduroam.com</a></p>
            </div>
        </div>
    </div>
</body>
</html>
