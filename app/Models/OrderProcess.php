<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OrderProcess extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order_processes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'process_name',
        'status',
        'started_at',
        'completed_at',
        'responsible_person',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the order that owns the process.
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    /**
     * Get the status label in Japanese.
     *
     * @return string
     */
    public function getStatusLabel()
    {
        $statusLabels = [
            'pending' => '待機中',
            'in_progress' => '進行中',
            'completed' => '完了',
            'cancelled' => 'キャンセル',
            'on_hold' => '保留',
        ];

        return $statusLabels[$this->status] ?? $this->status;
    }

    /**
     * Get the process name label in Japanese.
     *
     * @return string
     */
    public function getProcessNameLabel()
    {
        $processLabels = [
            'order_received' => '受注',
            'design' => 'デザイン',
            'approval' => '承認',
            'printing' => '印刷',
            'sewing' => '縫製',
            'quality_check' => '品質検査',
            'packaging' => '梱包',
            'shipping' => '出荷',
        ];

        return $processLabels[$this->process_name] ?? $this->process_name;
    }

    /**
     * Get the duration of the process in hours.
     *
     * @return float|null
     */
    public function getDuration()
    {
        if (!$this->started_at || !$this->completed_at) {
            return null;
        }

        return Carbon::parse($this->started_at)->diffInHours(Carbon::parse($this->completed_at));
    }

    /**
     * Get the formatted duration.
     *
     * @return string
     */
    public function getFormattedDuration()
    {
        $hours = $this->getDuration();
        if ($hours === null) {
            return '未完了';
        }

        if ($hours < 1) {
            $minutes = round($hours * 60);
            return "{$minutes}分";
        }

        if ($hours < 24) {
            return round($hours, 1) . '時間';
        }

        $days = round($hours / 24, 1);
        return "{$days}日";
    }

    /**
     * Check if the process is completed.
     *
     * @return bool
     */
    public function isCompleted()
    {
        return $this->status === 'completed' && $this->completed_at !== null;
    }

    /**
     * Check if the process is in progress.
     *
     * @return bool
     */
    public function isInProgress()
    {
        return $this->status === 'in_progress' && $this->started_at !== null && $this->completed_at === null;
    }

    /**
     * Check if the process is pending.
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Mark the process as started.
     *
     * @param string|null $responsiblePerson
     * @return void
     */
    public function markAsStarted($responsiblePerson = null)
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
            'responsible_person' => $responsiblePerson ?: $this->responsible_person,
        ]);
    }

    /**
     * Mark the process as completed.
     *
     * @param string|null $notes
     * @return void
     */
    public function markAsCompleted($notes = null)
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'notes' => $notes ?: $this->notes,
        ]);
    }

    /**
     * Scope a query to only include pending processes.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include in progress processes.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope a query to only include completed processes.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
} 