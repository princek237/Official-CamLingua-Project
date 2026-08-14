<?php
/**
 * EmailTemplates — PHP 7.4 compatible
 * Returns HTML strings for transactional emails.
 */

declare(strict_types=1);

namespace App\Services;

class EmailTemplates
{
    /**
     * Subscription confirmation email sent after successful CamPay payment.
     *
     * @param array $data Keys: user_name, plan_name, billing_cycle, amount,
     *                         currency, operator, campay_code, expires_at, app_url
     */
    public static function subscriptionConfirmation(array $data): string
    {
        $name         = htmlspecialchars($data['user_name']    ?? 'Valued Customer');
        $plan         = htmlspecialchars($data['plan_name']    ?? 'Pro');
        $cycle        = ucfirst($data['billing_cycle']         ?? 'monthly');
        $amount       = number_format((float)($data['amount']  ?? 0), 0, '.', ',');
        $currency     = htmlspecialchars($data['currency']     ?? 'XAF');
        $operator     = htmlspecialchars($data['operator']     ?? 'Mobile Money');
        $txCode       = htmlspecialchars($data['campay_code']  ?? '—');
        $appUrl       = rtrim($data['app_url']                 ?? 'http://localhost/CamLingua', '/');
        $year         = date('Y');

        $expiresRaw   = $data['expires_at'] ?? '';
        $expiresLabel = $expiresRaw
            ? date('F j, Y', strtotime($expiresRaw))
            : 'Not set';

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Subscription Confirmed – CamLingua</title>
</head>
<body style="margin:0;padding:0;background:#f4f7f6;font-family:'Segoe UI',Arial,sans-serif;color:#1a1a1a;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7f6;padding:32px 16px;">
<tr><td align="center">

  <!-- Card -->
  <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;max-width:600px;width:100%;">

    <!-- Header -->
    <tr>
      <td style="background:#15803d;padding:32px 40px;text-align:center;">
        <div style="display:inline-block;background:#ffffff22;border-radius:8px;padding:6px 14px;margin-bottom:12px;">
          <span style="color:#ffffff;font-size:22px;font-weight:800;letter-spacing:-0.5px;">Cam<span style="color:#86efac;">Lingua</span></span>
        </div>
        <h1 style="color:#ffffff;font-size:24px;font-weight:700;margin:0;">Payment Confirmed ✓</h1>
        <p style="color:#bbf7d0;margin:8px 0 0;font-size:15px;">Your {$plan} subscription is now active</p>
      </td>
    </tr>

    <!-- Body -->
    <tr>
      <td style="padding:36px 40px;">

        <p style="font-size:16px;margin:0 0 20px;">Hi <strong>{$name}</strong>,</p>
        <p style="font-size:15px;color:#444;line-height:1.6;margin:0 0 28px;">
          Thank you for subscribing to CamLingua! Your payment has been received and your
          <strong>{$plan}</strong> plan is now active. You can start using all features immediately.
        </p>

        <!-- Receipt box -->
        <table width="100%" cellpadding="0" cellspacing="0"
               style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;margin-bottom:28px;">
          <tr><td style="padding:24px 28px;">
            <p style="font-size:13px;font-weight:700;text-transform:uppercase;color:#15803d;letter-spacing:.5px;margin:0 0 16px;">Payment Receipt</p>
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td style="padding:6px 0;font-size:14px;color:#555;">Plan</td>
                <td style="padding:6px 0;font-size:14px;font-weight:600;text-align:right;">{$plan}</td>
              </tr>
              <tr>
                <td style="padding:6px 0;font-size:14px;color:#555;">Billing cycle</td>
                <td style="padding:6px 0;font-size:14px;font-weight:600;text-align:right;">{$cycle}</td>
              </tr>
              <tr>
                <td style="padding:6px 0;font-size:14px;color:#555;">Amount paid</td>
                <td style="padding:6px 0;font-size:14px;font-weight:700;text-align:right;color:#15803d;">{$currency} {$amount}</td>
              </tr>
              <tr>
                <td style="padding:6px 0;font-size:14px;color:#555;">Paid via</td>
                <td style="padding:6px 0;font-size:14px;font-weight:600;text-align:right;">{$operator} (CamPay)</td>
              </tr>
              <tr>
                <td style="padding:6px 0;font-size:14px;color:#555;">Transaction code</td>
                <td style="padding:6px 0;font-size:13px;font-weight:600;text-align:right;color:#888;">{$txCode}</td>
              </tr>
              <tr>
                <td colspan="2" style="padding:12px 0 0;">
                  <hr style="border:none;border-top:1px solid #bbf7d0;margin:0;">
                </td>
              </tr>
              <tr>
                <td style="padding:10px 0 0;font-size:14px;color:#555;">Next renewal</td>
                <td style="padding:10px 0 0;font-size:14px;font-weight:600;text-align:right;">{$expiresLabel}</td>
              </tr>
            </table>
          </td></tr>
        </table>

        <!-- CTA -->
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
          <tr>
            <td align="center">
              <a href="{$appUrl}/translator.php"
                 style="display:inline-block;background:#15803d;color:#ffffff;text-decoration:none;
                        font-size:15px;font-weight:700;padding:14px 36px;border-radius:8px;">
                Start Translating →
              </a>
            </td>
          </tr>
        </table>

        <p style="font-size:14px;color:#777;line-height:1.6;margin:0 0 8px;">
          You can manage your subscription at any time from your
          <a href="{$appUrl}/subscription.php" style="color:#15803d;text-decoration:none;">account page</a>.
        </p>
        <p style="font-size:14px;color:#777;margin:0;">
          Questions? Reply to this email or visit our
          <a href="{$appUrl}/support.php" style="color:#15803d;text-decoration:none;">Help Center</a>.
        </p>

      </td>
    </tr>

    <!-- Footer -->
    <tr>
      <td style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:20px 40px;text-align:center;">
        <p style="font-size:12px;color:#9ca3af;margin:0;">
          © {$year} CamLingua · Translate. Connect. Preserve Cameroon's Languages.<br>
          This is an automated receipt — please keep it for your records.
        </p>
      </td>
    </tr>

  </table>
</td></tr>
</table>

</body>
</html>
HTML;
    }
}
