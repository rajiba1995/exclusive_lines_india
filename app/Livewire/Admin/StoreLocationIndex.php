<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\StoreLocation;

class StoreLocationIndex extends Component
{
    public $activeTab = "Multi Brand Outlets";

    public function setTab($type)
    {
        $this->activeTab = $type;
    }

    public function render()
    {
        $storeLocations = StoreLocation::where('outlet_type', $this->activeTab)->get();
        return view('livewire.admin.store-location-index',[
            'storeLocations' => $storeLocations
        ]);
    }
}
