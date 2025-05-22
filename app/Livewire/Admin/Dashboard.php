<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Stock;

class Dashboard extends Component
{
    public $data;
    public function logout()
    {
        Auth::guard('admin')->logout();
        session()->invalidate();
        session()->regenerateToken();
        
        return redirect()->route('login');
    }
    public function mount(){
       
    }

    public function render()
    {
        
        $admin = Auth::guard('admin')->user();
        return view('livewire.admin.dashboard');
    }
}
