<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>New Contact Message</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* Email-safe minimal styles (inline preferred) */
        .wrap {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #eee;
            overflow: hidden;
            font-family: Arial, Helvetica, sans-serif;
        }

        .header {
            background: #8087e1;
            color: #fff;
            padding: 20px 24px;
            font-size: 20px;
            font-weight: bold;
        }

        .body {
            padding: 24px;
            color: #222;
        }

        .row {
            margin-bottom: 14px;
        }

        .label {
            color: #555;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .value {
            font-size: 16px;
            margin-top: 4px;
        }

        .footer {
            padding: 16px 24px;
            color: #888;
            font-size: 12px;
            background: #fafafa;
            border-top: 1px solid #eee;
        }

        .pill {
            display: inline-block;
            background: #eef;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            color: #334;
        }
    </style>
</head>

<body style="background:#f4f5f7;padding:20px;">
    <div class="wrap">
        <div class="header">New Contact Form Submission</div>
        <div class="body">
            <div class="row">
                <span class="label">Name</span>
                <div class="value">{{ $data['name'] }}</div>
            </div>
            <div class="row">
                <span class="label">Email</span>
                <div class="value"><a href="mailto:{{ $data['email'] }}">{{ $data['email'] }}</a></div>
            </div>
            <div class="row">
                <span class="label">Phone</span>
                <div class="value">{{ $data['phone'] }}</div>
            </div>
            <div class="row">
                <span class="label">Message</span>
                <div class="value">{{ $data['message'] ?: '—' }}</div>
            </div>
            <div class="row">
                <span class="pill">Submitted: {{ now()->format('d M Y, h:i A') }}</span>
            </div>
        </div>
        <div class="footer">
            You can reply directly to this email to contact <strong>{{ $data['name'] }}</strong>.
        </div>
    </div>
</body>

</html>