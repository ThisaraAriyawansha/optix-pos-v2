<?php

namespace App\Support;

use App\Models\User;
use App\Models\Worker;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Employee IDs (EMP-0001, EMP-0002, …) are one series shared by labourers (workers.code)
 * and system users (users.employee_code), so no two people ever carry the same ID.
 */
class EmployeeCode
{
    public const PREFIX = 'EMP-';

    public static function next(): string
    {
        $codes = DB::table('workers')->where('code', 'like', self::PREFIX.'%')->pluck('code')
            ->merge(DB::table('users')->where('employee_code', 'like', self::PREFIX.'%')->pluck('employee_code'));

        $last = $codes->map(fn ($code) => (int) substr($code, strlen(self::PREFIX)))->max() ?? 0;

        return self::format($last + 1);
    }

    public static function format(int $number): string
    {
        return self::PREFIX.str_pad((string) $number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Validation rules for an editable employee ID: unique across both workers and users.
     * Left blank, the model assigns the next ID on save.
     */
    public static function rules(Worker|User|null $ignore = null): array
    {
        return [
            'nullable', 'string', 'max:20', 'regex:/^[A-Za-z0-9\-\/]+$/',
            Rule::unique('workers', 'code')->ignore($ignore instanceof Worker ? $ignore : null),
            Rule::unique('users', 'employee_code')->ignore($ignore instanceof User ? $ignore : null),
        ];
    }

    public static function normalize(?string $code): ?string
    {
        $code = strtoupper(trim((string) $code));

        return $code === '' ? null : $code;
    }
}
