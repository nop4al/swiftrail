<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'booking_id',
        'amount',
        'reason',
        'refund_reason',
        'description',
        'status',
        'swift_pay_wallet_id',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function swiftPayWallet()
    {
        return $this->belongsTo(SwiftPayWallet::class, 'swift_pay_wallet_id');
    }
}
