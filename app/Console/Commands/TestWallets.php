<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SwiftPayWallet;
use App\Models\User;

class TestWallets extends Command
{
    protected $signature = 'app:test-wallets';
    protected $description = 'Check SwiftPay wallets';

    public function handle()
    {
        $this->line('=== ALL SWIFT PAY WALLETS ===\n');
        
        $wallets = SwiftPayWallet::with('user')->get();
        
        foreach ($wallets as $wallet) {
            $this->line("ID: {$wallet->id} | User ID: {$wallet->user_id} | Wallet: {$wallet->wallet_number} | Balance: {$wallet->balance} | Status: {$wallet->status}");
        }
        
        if ($wallets->count() === 0) {
            $this->line('No wallets found!');
        }
        
        // Test API response format for each user
        $this->line('\n=== API RESPONSE FORMAT BY USER ===\n');
        
        $users = User::all();
        foreach ($users as $user) {
            $this->line("User: {$user->email}");
            
            $userWallets = SwiftPayWallet::where('user_id', $user->id)
                ->where('status', 'active')
                ->get();
            
            if ($userWallets->count() === 0) {
                $this->line('  No active wallets');
            } else {
                foreach ($userWallets as $wallet) {
                    $this->line("  - {$wallet->wallet_number}: {$wallet->balance}");
                }
            }
        }
    }
}
