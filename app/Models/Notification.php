<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'type',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 是否已读
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * 标记为已读
     */
    public function markAsRead(): void
    {
        if (is_null($this->read_at)) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * 发送通知（静态方法）
     */
    public static function send(int $userId, string $title, string $content, string $type = 'info'): self
    {
        return self::create([
            'user_id' => $userId,
            'title' => $title,
            'content' => $content,
            'type' => $type,
        ]);
    }

    /**
     * 批量发送通知给多个用户
     */
    public static function sendToMany(array $userIds, string $title, string $content, string $type = 'info'): void
    {
        foreach ($userIds as $userId) {
            self::send($userId, $title, $content, $type);
        }
    }

    /**
     * 发送通知给所有用户
     */
    public static function sendToAll(string $title, string $content, string $type = 'info'): void
    {
        $userIds = User::pluck('id')->toArray();
        self::sendToMany($userIds, $title, $content, $type);
    }
}
