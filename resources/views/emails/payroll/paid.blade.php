@php
    $employee = $payroll->employee;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Salary Payment Notification</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background: linear-gradient(135deg, #2e7d32, #4caf50); padding: 20px; border-radius: 8px 8px 0 0;">
            <h1 style="color: white; margin: 0; font-size: 24px;">{{ config('app.name') }}</h1>
            <p style="color: rgba(255,255,255,0.9); margin: 5px 0 0 0;">Salary Payment Notification</p>
        </div>
        
        <div style="background: #f9f9f9; padding: 30px; border: 1px solid #e0e0e0; border-top: none;">
            <p style="font-size: 16px;">Hello <strong>{{ $employee->full_name }}</strong>,</p>

            <p>Your salary has been <strong style="color: #2e7d32;">PAID</strong> for <strong>{{ \Carbon\Carbon::parse($payroll->pay_period)->format('F Y') }}</strong>.</p>

            <table cellpadding="10" cellspacing="0" border="0" style="width: 100%; border-collapse: collapse; margin: 20px 0; background: white; border-radius: 8px; overflow: hidden;">
                <tr style="background: #f5f5f5;">
                    <td style="border-bottom: 1px solid #e0e0e0; font-weight: 500;">Base Salary:</td>
                    <td style="border-bottom: 1px solid #e0e0e0; text-align: right;"><strong>GHS {{ number_format($payroll->base_salary, 2) }}</strong></td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #e0e0e0;">Allowances:</td>
                    <td style="border-bottom: 1px solid #e0e0e0; text-align: right;"><strong>GHS {{ number_format($payroll->allowances_total, 2) }}</strong></td>
                </tr>
                <tr style="background: #f5f5f5;">
                    <td style="border-bottom: 1px solid #e0e0e0;">Deductions:</td>
                    <td style="border-bottom: 1px solid #e0e0e0; text-align: right;"><strong>GHS {{ number_format($payroll->deductions_total, 2) }}</strong></td>
                </tr>
                <tr style="background: #e8f5e9;">
                    <td style="font-weight: bold; font-size: 16px;">Net Pay:</td>
                    <td style="text-align: right; font-weight: bold; font-size: 18px; color: #2e7d32;">GHS {{ number_format($payroll->net_pay, 2) }}</td>
                </tr>
            </table>

            <p><strong>Paid at:</strong> {{ $payroll->paid_at ? $payroll->paid_at->format('Y-m-d H:i') : now()->format('Y-m-d H:i') }}</p>

            <hr style="border: none; border-top: 1px solid #e0e0e0; margin: 30px 0;">

            <p style="color: #666; font-size: 14px;">
                Regards,<br>
                <strong>{{ config('app.name') }} - Accounts</strong>
            </p>
        </div>
        
        <div style="background: #333; padding: 15px; border-radius: 0 0 8px 8px; text-align: center;">
            <p style="color: #999; margin: 0; font-size: 12px;">
                This is an automated message from {{ config('app.name') }}
            </p>
        </div>
    </div>
</body>
</html>
