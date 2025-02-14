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
            background-color: #ece0e0;
            color: #1e2022;
            overflow:hidden;
        }
        .maintenance-container {
            border-top:4px solid #850F00;
            max-width: 500px;
            padding: 64px 40px;
            background: white;
            box-shadow: 0 4px 40px -20px #ff8a8a;
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
        .btn {
            display: inline-block;
            font-weight: 600;
            text-decoration: none;
            padding:16px 32px;
            background:#FFF;
            border:2px solid #000;
            color:#000;
            transition: all 0.3s ease;
        }
        .btn:hover{
            box-shadow: 0 8px 16px -8px rgba(0, 0, 0, 0.4);
        }
        .btn:focus{
            border-color: #CA2C22;
            box-shadow: 0 8px 16px -8px #CA2C22;
        }
    </style>
</head>
<body>
<div class="maintenance-container">
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 80px; height: 80px; color:#CA2C22;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
        </svg>
    </div>
    <h1>Unauthorized Access</h1>
    <p class="message">You don't have the necessary permissions to access this module.</p>
    <a href="/"
       class="btn">
        Back to Home
    </a>
</div>
</body>
</html>
