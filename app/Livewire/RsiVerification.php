<?php

namespace App\Livewire;

use App\Services\RsiAccountVerificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class RsiVerification extends Component
{
    public string $verificationKey;

    #[Validate('required|alpha_num|min:2')]
    public string $playerName;

    public function mount(): void
    {
        $user = Auth::user();
        $this->playerName = Auth::user()->global_name ?? Auth::user()->disriminator;
        if (! $user->rsi_verification_key) {
            $verificationKey = Str::random(10);
            $user->rsi_verification_key = $verificationKey;
            $user->save();
            $this->verificationKey = $verificationKey;
        } else {
            $this->verificationKey = $user->rsi_verification_key;
        }
    }

    public function save(RsiAccountVerificationService $rsiAccountVerificationService): void
    {
        $user = Auth::user();
        $validated = $rsiAccountVerificationService->verifyBiographyKey($this->playerName, $this->verificationKey);

        if ($validated) {
            $user->rsi_verified = true;
            $user->rsi_verified_at = Carbon::now();
            $user->save();

            Toaster::success('Account Verified Successfully.');
            $this->redirect('/');
        } else {
            Toaster::error('Account Verification Failed.');
        }
    }

    public function render(): View
    {
        return view('livewire.rsi-verification');
    }
}
