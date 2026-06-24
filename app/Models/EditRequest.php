<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EditRequest extends Model
{
    protected $fillable = [
        'user_id',
        'model_type',
        'model_id',
        'original_data',
        'requested_data',
        'reason',
        'status', // 'pending', 'approved', 'rejected'
        'reviewer_id',
        'reviewed_at',
    ];

    protected $casts = [
        'original_data' => 'array',
        'requested_data' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Get the target model instance.
     */
    public function getTargetInstance()
    {
        if (class_exists($this->model_type)) {
            return $this->model_type::find($this->model_id);
        }
        return null;
    }

    /**
     * Approve this edit request and apply changes to the target model.
     */
    public function approve($reviewerId): bool
    {
        $modelClass = $this->model_type;
        if (!class_exists($modelClass)) {
            return false;
        }

        $model = $modelClass::find($this->model_id);
        if (!$model) {
            return false;
        }

        // Update the model with requested data
        $model->update($this->requested_data);

        // Update the request status
        return $this->update([
            'status' => 'approved',
            'reviewer_id' => $reviewerId,
            'reviewed_at' => now(),
        ]);
    }

    protected static function booted()
    {
        static::created(function ($editRequest) {
            $owner = \App\Models\User::where('role', 'owner')->first();
            if ($owner) {
                $modelName = class_basename($editRequest->model_type);
                $roleLabel = 'Staf';
                if ($editRequest->user) {
                    if ($editRequest->user->role === 'admin') {
                        $roleLabel = 'Admin';
                    } elseif ($editRequest->user->role === 'gudang') {
                        $roleLabel = 'Tim Gudang';
                    }
                }
                $userName = $editRequest->user ? $editRequest->user->name : '';
                
                \App\Models\Notification::create([
                    'user_id' => $owner->id,
                    'title' => 'Permintaan Edit Baru',
                    'message' => "{$roleLabel} {$userName} mengajukan edit data untuk {$modelName}.",
                    'link' => route('edit-requests.show', $editRequest->id),
                    'is_read' => false,
                ]);
            }
        });

        static::updated(function ($editRequest) {
            if ($editRequest->isDirty('status') && in_array($editRequest->status, ['approved', 'rejected'])) {
                $adminId = $editRequest->user_id;
                $statusLabel = $editRequest->status === 'approved' ? 'Disetujui' : 'Ditolak';
                $modelName = class_basename($editRequest->model_type);
                
                \App\Models\Notification::create([
                    'user_id' => $adminId,
                    'title' => "Permintaan Edit {$statusLabel}",
                    'message' => "Permintaan edit data Anda untuk {$modelName} telah {$editRequest->status} oleh Owner.",
                    'link' => route('edit-requests.show', $editRequest->id),
                    'is_read' => false,
                ]);
            }
        });
    }

    /**
     * Reject this edit request.
     */
    public function reject($reviewerId): bool
    {
        return $this->update([
            'status' => 'rejected',
            'reviewer_id' => $reviewerId,
            'reviewed_at' => now(),
        ]);
    }
}
