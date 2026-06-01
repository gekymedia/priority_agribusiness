<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\User;
use App\Support\PhoneNormalizer;

class UssdStaffResolver
{
    /** @var array<int, string> */
    private const USER_USSD_ROLES = ['admin', 'poultry_manager', 'crop_manager'];

    /**
     * Resolve an approved staff member from a USSD caller phone number.
     *
     * Checks employees first, then users. Matches primary or alternate phone.
     *
     * @return array{
     *     type: 'employee'|'user',
     *     name: string,
     *     staff_id: string,
     *     access_level: string,
     *     farm_id: int|null,
     *     model: Employee|User
     * }|null
     */
    public function resolve(string $phone): ?array
    {
        $variants = PhoneNormalizer::variants($phone);
        if ($variants === []) {
            return null;
        }

        $employee = $this->findEmployee($variants);
        if ($employee) {
            return $this->formatEmployee($employee);
        }

        $user = $this->findUser($variants);
        if ($user) {
            return $this->formatUser($user);
        }

        return null;
    }

    /**
     * @param array<int, string> $variants
     */
    protected function findEmployee(array $variants): ?Employee
    {
        return Employee::query()
            ->where('is_active', true)
            ->where('status', 'approved')
            ->whereNotIn('access_level', ['viewer'])
            ->where(function ($query) use ($variants) {
                foreach ($variants as $variant) {
                    $query->orWhere('phone', $variant)
                        ->orWhere('phone_alt', $variant);
                }
            })
            ->first();
    }

    /**
     * @param array<int, string> $variants
     */
    protected function findUser(array $variants): ?User
    {
        return User::query()
            ->whereIn('role', self::USER_USSD_ROLES)
            ->where(function ($query) use ($variants) {
                foreach ($variants as $variant) {
                    $query->orWhere('phone', $variant)
                        ->orWhere('phone_alt', $variant);
                }
            })
            ->first();
    }

    /**
     * @return array{type: 'employee', name: string, staff_id: string, access_level: string, farm_id: int|null, model: Employee}
     */
    protected function formatEmployee(Employee $employee): array
    {
        return [
            'type' => 'employee',
            'name' => $employee->full_name,
            'staff_id' => $employee->employee_id,
            'access_level' => $employee->access_level,
            'farm_id' => $employee->farm_id,
            'model' => $employee,
        ];
    }

    /**
     * @return array{type: 'user', name: string, staff_id: string, access_level: string, farm_id: null, model: User}
     */
    protected function formatUser(User $user): array
    {
        return [
            'type' => 'user',
            'name' => $user->name,
            'staff_id' => 'USR-' . $user->id,
            'access_level' => $user->role ?? 'admin',
            'farm_id' => null,
            'model' => $user,
        ];
    }
}
