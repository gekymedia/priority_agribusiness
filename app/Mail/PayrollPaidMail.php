<?php

namespace App\Mail;

use App\Models\Payroll;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class PayrollPaidMail extends Mailable
{
    use Queueable, SerializesModels;

    public Payroll $payroll;

    public function __construct(Payroll $payroll)
    {
        $this->payroll = $payroll->loadMissing('employee');
    }

    public function build()
    {
        $employee = $this->payroll->employee;
        $period = Carbon::parse($this->payroll->pay_period)->format('M Y');
        $subject = "Salary Paid - {$employee->full_name} - {$period}";

        return $this->subject($subject)
            ->view('emails.payroll.paid', [
                'payroll' => $this->payroll,
            ]);
    }
}
