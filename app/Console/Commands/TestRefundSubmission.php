<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\User;
use App\Models\SwiftPayWallet;
use App\Models\Refund;

class TestRefundSubmission extends Command
{
    protected $signature = 'app:test-refund-submit';
    protected $description = 'Test refund submission directly';

    public function handle()
    {
        $this->line('=== TESTING REFUND SUBMISSION ===\n');
        
        // Get user 3 (the one logged in)
        $user = User::find(3);
        if (!$user) {
            $this->error('User not found!');
            return;
        }
        
        $this->line("Testing with user: {$user->email}\n");
        
        // Get user's booking
        $booking = Booking::where('user_id', $user->id)->first();
        if (!$booking) {
            $this->error('No booking found for this user!');
            return;
        }
        
        $this->line("Booking ID: {$booking->id}");
        
        // Get user's wallet
        $wallet = SwiftPayWallet::where('user_id', $user->id)->where('status', 'active')->first();
        if (!$wallet) {
            $this->error('No active wallet found for this user!');
            return;
        }
        
        $this->line("Wallet ID: {$wallet->id}\n");
        
        // Now test the validation
        $this->line('=== TESTING VALIDATION ===\n');
        
        $validator = \Illuminate\Support\Facades\Validator::make(
            [
                'booking_id' => $booking->id,
                'reason' => 'Personal',
                'description' => 'aseRRR',
                'swift_pay_wallet_id' => $wallet->id
            ],
            [
                'booking_id' => 'required|exists:bookings,id',
                'reason' => 'required|string|min:5',
                'description' => 'required|string|min:10',
                'swift_pay_wallet_id' => 'required|exists:swift_pay_wallets,id'
            ]
        );
        
        if ($validator->fails()) {
            $this->line('VALIDATION FAILED:');
            foreach ($validator->errors()->all() as $error) {
                $this->line("  - $error");
            }
            $this->line("\nErrors array:");
            $this->line(json_encode($validator->errors(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } else {
            $this->line('✓ All validation passed!');
        }
    }
}
