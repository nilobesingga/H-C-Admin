<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Reminder</title>
  <style>
    body {
      font-family: Inter, sans-serif;
      line-height: 1.6;
      color: #333333;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 600px;
      margin: 0 auto;
      padding: 25px;
      border: 1px solid #e0e0e0;
    }
    .header {
      padding: 10px 0;
      border-bottom: 1px solid #eeeeee;
    }
    .logo {
      width: 150px;
      height: auto;
    }
    .content {
      padding: 20px 0;
    }
    .amount {
      font-size: 32px;
      font-weight: bold;
      text-align: center;
      margin-top: 10px !important;
      margin-bottom: 0px !important;
    }
    .due-date {
      text-align: center;
      margin-bottom: 30px;
    }
    .invoice-details {
      margin: 20px 0;
    }
    .buttons {
      text-align: center;
      margin: 30px 0;
    }
    .btn {
      display: inline-block;
      width: 200px !important;
      padding: 5px 25px;
      margin: 0 10px;
      text-decoration: none;
      border-radius: 2px;
      font-weight: bold;
    }
    .btn-view {
      color: #333;
      border: 1px solid #333;
      background-color: white;
    }
    .btn-pay {
      color: white;
      background-color: #CC6633;
      border: 1px solid #CC6633;
    }
    .footer {
      font-style: italic;
      font-size: 13px !important;
      text-align: center;
      margin-top: 30px;
      color: #666;
    }
    .social-icons {
        text-align: center;
        margin: 5px auto !important;
    }
    .social-icon {
      display: inline-block;
      width: 30px;
      height: 30px;
      background-color: #CC6633;
      margin: 5px !important;
      border-radius: 3px;
      line-height: 30px;
      text-align: center;
    }
    .warning {
      color: #CC6633;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td style="padding: 10px 0;">
              <img src="{{ asset($data['logo']) }}" alt="Hensley&Cook" style="width: 150px; height: auto;">
            </td>
            <td style="text-align: right; padding: 10px 0;">
              <a href="{{ $data['preview_url'] }}" style="color: #333333; text-decoration: underline; font-size: 12px;">View in Browser</a>
            </td>
          </tr>
        </table>
    </div>
    <div class="content">
      <h3 style="text-align: center;">{{ $data['title'] }}</h3>
      <hr style="width: 80px !important; margin: 20px auto !important; text-align: center !important;">

      <div class="amount">{{ $data['amount'] . ' ' . $data['currency'] }}</div>
      <div class="due-date">Due Date: <strong>{{ $data['invoice_date'] }}</strong></div>
      <hr style="width: 50%; margin: 0px auto; border: 0.1px solid #e0e0e0;">
      <br/>
      <br/>
      <strong>Hi {{ $data['recipient_name'] ?? '' }},</strong>
      <br/>
      <br/>
        @if($data['title'] != 'Payment Reminder')
        <p>Thank you for your continued partnership.</p>
        <p>Attached to this email is your invoice for our services. The total amount of <strong>{{ $data['amount'] . ' ' . $data['currency'] }}</strong> is due <strong>{{ $data['invoice_date'] }}</strong>.</p>
        @else
            <p>This is a friendly reminder that your payment of <strong>{{ $data['amount'] . ' ' . $data['currency'] }}</strong> was due on <strong>{{ $data['invoice_date'] }}</strong>. Please ensure timely settlement to avoid any disruptions.</p>
        @endif

      <p class="invoice-details">To ensure accurate processing, kindly use <strong style="text-decoration: underline;">Invoice No: {{ $data['invoice_number'] }}</strong> as your payment reference. If you choose to pay via the online payment link, <span class="warning">please note that a 3% ( {{ $data['service_charge'] . ' ' .  $data['currency']}} ) service fee will apply</span>.</p>

      <p>Warm regards,<br>
      <strong>{{ $data['company'] }}</strong></p>
      <br/>
      <hr style="width: 50%; margin: 20px auto; border: 0.1px solid #e0e0e0;">
      <div class="buttons">
        <a href="{{ $data['preview_url'] }}" class="btn btn-view" style="color: #000000 !important;">View Invoice</a>
        <a href="{{ $data['payment_link'] }}" class="btn btn-pay" style="color: #ffffff !important;">Pay Now</a>
      </div>
        <hr style="width: 50%; margin: 20px auto; border: 0.1px solid #e0e0e0;">
      <div class="footer">
        If you have any questions or encounter any issues with payment, feel free to contact us.
        <div class="social-icons" style="text-align: center; margin: 10px !important;">
          <a href="#" class="social-icon">
            <img src="{{ asset('img/social/message.svg') }}" alt="Message" width="20" height="20" style="max-width: 20px; max-height: 20px; display: inline-block; padding-top : 5px !important;"/>
          </a>
          <a href="#" class="social-icon">
            <img src="{{ asset('img/social/call-sharp.svg') }}" alt="Telephone" width="20" height="20" style="max-width: 20px; max-height: 20px; display: inline-block; padding-top : 5px !important;"/>
          </a>
          <a href="#" class="social-icon">
            <img src="{{ asset('img/social/whatsapp.svg') }}" alt="Whatsapp" width="20" height="20" style="max-width: 20px; max-height: 20px; display: inline-block; padding-top : 5px !important;"/>
          </a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
