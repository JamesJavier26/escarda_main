<?php

namespace App\Helpers;

use App\Events\NotificationSent;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;


if (! function_exists('next')) {
    function next(string $table, string $column = 'pe_no', int $startAt = 1): int
    {
        // Grab the current maximum (handles concurrency at DB level)
        $current = (int) DB::table($table)->max($column);

        // If there was nothing in the table, start at $startAt (default 1)
        // Otherwise just increment
        return $current ? $current + 1 : $startAt;
    }
}
if (! function_exists('notify_user')) {
    function notify_user(int $userId, string $title, string $message = '', array $extra = []): Notification
    {
        $notification = Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'attachment' => $extra['attachment'] ?? null,
            'action_button' => $extra['action_button'] ?? null,
        ]);

        broadcast(new NotificationSent($notification))->toOthers();

        return $notification;
    }
}
if (! function_exists('formatBytes')) {
    function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

}