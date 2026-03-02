<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\NotificationSetting;
use App\Models\User;
use App\Services\Notifications\Notifier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class CrudNotificationService
{
    protected Notifier $notifier;

    public function __construct(Notifier $notifier)
    {
        $this->notifier = $notifier;
    }

    public function notify(string $module, string $event, Model $record, $actor = null): void
    {
        if (!NotificationSetting::isEnabled($module, $event)) {
            return;
        }

        $channels = NotificationSetting::getChannels($module, $event);
        $admins = $this->getAdmins();
        
        if ($admins->isEmpty()) {
            return;
        }

        $message = $this->buildMessage($module, $event, $record, $actor);
        $subject = $this->buildSubject($module, $event);

        foreach ($admins as $admin) {
            $this->sendToAdmin($admin, $message, $subject, $channels);
        }
    }

    protected function getAdmins()
    {
        $adminEmployees = Employee::where('access_level', 'admin')
            ->where('is_active', true)
            ->where('status', 'approved')
            ->get();

        $adminUsers = User::where('role', 'admin')->get();

        return $adminEmployees->merge($adminUsers);
    }

    protected function buildSubject(string $module, string $event): string
    {
        $moduleLabel = $this->getModuleLabel($module);
        $eventLabel = ucfirst($event);
        
        return "[" . config('app.name') . "] {$moduleLabel} {$eventLabel}";
    }

    protected function buildMessage(string $module, string $event, Model $record, $actor = null): string
    {
        $moduleLabel = $this->getModuleLabel($module);
        $eventLabel = ucfirst($event);
        $actorName = $this->getActorName($actor);
        $summary = $this->getRecordSummary($module, $record);
        $time = now()->format('M d, Y h:i A');
        $appName = config('app.name');

        return "{$moduleLabel} {$eventLabel}\n\n{$summary}\n\nBy: {$actorName}\nAt: {$time}\n\n- {$appName}";
    }

    protected function getModuleLabel(string $module): string
    {
        return match ($module) {
            'egg_production' => 'Egg Production',
            'egg_sales' => 'Egg Sale',
            'bird_sales' => 'Bird Sale',
            'expenses' => 'Expense',
            'employees' => 'Employee',
            default => ucwords(str_replace('_', ' ', $module)),
        };
    }

    protected function getActorName($actor): string
    {
        if (!$actor) {
            return 'System';
        }

        if ($actor instanceof Employee) {
            return $actor->full_name;
        }

        if ($actor instanceof User) {
            return $actor->name;
        }

        return 'Unknown';
    }

    protected function getRecordSummary(string $module, Model $record): string
    {
        return match ($module) {
            'egg_production' => $this->eggProductionSummary($record),
            'egg_sales' => $this->eggSaleSummary($record),
            'bird_sales' => $this->birdSaleSummary($record),
            'expenses' => $this->expenseSummary($record),
            'employees' => $this->employeeSummary($record),
            default => "Record ID: {$record->id}",
        };
    }

    protected function eggProductionSummary(Model $record): string
    {
        $batch = $record->birdBatch?->name ?? 'Unknown Batch';
        $eggs = $record->eggs_collected ?? 0;
        $date = $record->date ? $record->date->format('M d, Y') : 'N/A';
        
        return "Batch: {$batch}\nEggs Collected: {$eggs}\nDate: {$date}";
    }

    protected function eggSaleSummary(Model $record): string
    {
        $qty = $record->quantity_sold ?? 0;
        $unit = $record->unit_type ?? 'units';
        $amount = number_format(($record->quantity_sold ?? 0) * ($record->price_per_unit ?? 0), 2);
        $buyer = $record->buyer_name ?? 'N/A';
        
        return "Quantity: {$qty} {$unit}\nAmount: GHS {$amount}\nBuyer: {$buyer}";
    }

    protected function birdSaleSummary(Model $record): string
    {
        $qty = $record->quantity_sold ?? 0;
        $amount = number_format(($record->quantity_sold ?? 0) * ($record->price_per_bird ?? 0), 2);
        $buyer = $record->buyer_name ?? 'N/A';
        
        return "Birds Sold: {$qty}\nAmount: GHS {$amount}\nBuyer: {$buyer}";
    }

    protected function expenseSummary(Model $record): string
    {
        $amount = number_format($record->amount ?? 0, 2);
        $category = $record->category?->name ?? 'Uncategorized';
        $description = $record->description ?? 'N/A';
        
        return "Amount: GHS {$amount}\nCategory: {$category}\nDescription: {$description}";
    }

    protected function employeeSummary(Model $record): string
    {
        $name = $record->full_name ?? ($record->first_name . ' ' . $record->last_name);
        $role = $record->access_level ?? 'N/A';
        $email = $record->email ?? 'N/A';
        
        return "Name: {$name}\nRole: {$role}\nEmail: {$email}";
    }

    protected function sendToAdmin($admin, string $message, string $subject, array $channels): void
    {
        $email = $admin->email ?? null;
        $phone = $admin->phone ?? null;

        if (in_array('email', $channels) && $email) {
            try {
                $this->notifier->email($email, $message, $subject);
            } catch (\Throwable $e) {
                Log::warning("CRUD notification email failed: " . $e->getMessage());
            }
        }

        if (in_array('sms', $channels) && $phone) {
            try {
                $this->notifier->sms($phone, $message);
            } catch (\Throwable $e) {
                Log::warning("CRUD notification SMS failed: " . $e->getMessage());
            }
        }

        if (in_array('gekychat', $channels) && $phone) {
            try {
                $this->notifier->gekychat($phone, $message);
            } catch (\Throwable $e) {
                Log::warning("CRUD notification GekyChat failed: " . $e->getMessage());
            }
        }
    }
}
