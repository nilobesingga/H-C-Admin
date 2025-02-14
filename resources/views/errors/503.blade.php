<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Maintenance - CRESCO</title>
    <style>
        body {
            text-align: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: #e7e7e7;
            color: #1e2022;
            overflow:hidden;
        }
        .maintenance-container {
            border-top:4px solid #004080;
            max-width: 500px;
            padding: 64px 40px;
            background: white;
            box-shadow: 0 4px 40px -20px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 40px;
            line-height: 40px;
            letter-spacing: -0.75px;
            margin-top: 24px;
            margin-bottom: 24px;
            color: #2c3038;
        }
        p {
            font-size: 18px;
            line-height: 1.6;
            color: #6c757d;
            margin-bottom: 20px;
        }
        .message{
            padding:10px 56px;
        }
        .estimated-time {
            display: inline-block;
            font-weight: 500;
            color: #004080;
            background: #FFF;
            margin-top: 20px;
            border:1px solid #01A4F7;
            padding:12px 24px;
            border-radius: 50px;
            outline:4px solid rgba(1, 164, 247, 0.2);
            box-shadow: 0 8px 40px -4px rgba(1, 164, 247, 0.4);
        }
    </style>
</head>
<body>
<div class="maintenance-container">
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 80px; height: 80px; color:#01A4F7;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75a4.5 4.5 0 0 1-4.884 4.484c-1.076-.091-2.264.071-2.95.904l-7.152 8.684a2.548 2.548 0 1 1-3.586-3.586l8.684-7.152c.833-.686.995-1.874.904-2.95a4.5 4.5 0 0 1 6.336-4.486l-3.276 3.276a3.004 3.004 0 0 0 2.25 2.25l3.276-3.276c.256.565.398 1.192.398 1.852Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.867 19.125h.008v.008h-.008v-.008Z" />
        </svg>
    </div>
    <h1>System Maintenance</h1>
    <p class="message">We're currently updating our systems to bring you an improved experience.</p>
    <p class="estimated-time">Expected downtime: ~30 minutes</p>
</div>
</body>
</html>
