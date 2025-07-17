<?php

namespace App\Helpers;

use App\Events\NotificationSent;
use App\Models\Employee;
use App\Models\Item;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Helper
{

    public static function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public static function generate_employee_number(): string
    {
        $prefix = now()->format('Y-m'); // e.g. "2025-07"

        // Get the highest xx for the current month
        $last = Employee::where('employee_number', 'like', "$prefix%")
            ->orderBy('employee_number', 'desc')
            ->first();

        $next = 1;

        if ($last) {
            $lastNumber = (int) substr($last->employee_number, -2); // Get last 2 digits
            $next = $lastNumber + 1;
        }

        do {
            $employeeNumber = sprintf('%s%02d', $prefix, $next);
            $exists = Employee::where('employee_number', $employeeNumber)->exists();
            $next++;
        } while ($exists); // Ensure it's unique

        return $employeeNumber; // e.g. "2025-0701"
    }
    public static function generateNextItemNumber(): string
    {
        $max = Item::max(DB::raw('CAST(number AS UNSIGNED)'));
        return (string) (($max ?? 0) + 1);
    }


}
