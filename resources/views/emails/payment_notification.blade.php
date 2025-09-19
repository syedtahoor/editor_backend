<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>New Donation Received</title>
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

        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #10B981;
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
            background: #e0e7ff;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            color: #3730a3;
            font-family: monospace;
        }
    </style>
</head>

<body style="background:#f4f5f7;padding:20px;">
    <div class="wrap">
        <div class="header">🎉 New Donation Received!</div>
        <div class="body">
            <div class="row">
                <span class="label">Donation Amount</span>
                <div class="value amount">${{ number_format($data['amount'], 2) }}</div>
            </div>
            
            <div class="row">
                <span class="label">Payment ID</span>
                <div class="value">
                    <span class="pill">{{ $data['payment_intent_id'] }}</span>
                </div>
            </div>
            
            <div class="row">
                <span class="label">Transaction Time</span>
                <div class="value">{{ $data['timestamp'] }}</div>
            </div>
        </div>
        <div class="footer">
            Thank you for supporting our platform! This donation will help us continue providing free animations and new features.
        </div>
    </div>
</body>

</html>
