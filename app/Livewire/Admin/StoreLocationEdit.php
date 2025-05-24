<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\StoreLocation;
use App\Models\Brand;
class StoreLocationEdit extends Component
{
     public $storeLocationId;

    public $name;
    public $address;
    public $contact_numbers = [];
    public $operating_time = [];
    public $selectedBrands = [];
    public $outlet_type;

    public $brandsList;

    public function mount($id)
    {
        $this->storeLocationId = $id;
        $storeLocation = StoreLocation::findOrFail($id);

        // Fill the form fields with existing data
        $this->name = $storeLocation->name;
        $this->address = $storeLocation->address;
       // If these are JSON strings in DB, decode them
        $this->contact_numbers = is_string($storeLocation->contact_numbers) 
        ? json_decode($storeLocation->contact_numbers, true) 
        : ($storeLocation->contact_numbers ?? ['']);

        $this->operating_time = is_string($storeLocation->operating_time) 
            ? json_decode($storeLocation->operating_time, true) 
            : ($storeLocation->operating_time ?? [['time' => '']]);
        $this->outlet_type = $storeLocation->outlet_type;

        $this->brandsList = Brand::all();
    }

    public function addPhone()
    {
        $this->contact_numbers[] = '';
    }

    public function removePhone($index)
    {
        unset($this->contact_numbers[$index]);
        $this->contact_numbers = array_values($this->contact_numbers);
    }

    public function addTime()
    {
        $this->operating_time[] = ['time' => ''];
    }

    public function removeTime($index)
    {
        unset($this->operating_time[$index]);
        $this->operating_time = array_values($this->operating_time);
    }

    protected $rules = [
        'name' => 'required|string|max:255',
        'address' => 'required|string',
        'contact_numbers.*' => 'required|string|max:20',
        'operating_time.*.time' => 'required|string|max:100',
        // 'selectedBrands' => 'required|array|min:1',
        'outlet_type' => 'required|string',
    ];

    public function update()
    {
        $this->validate();

        $storeLocation = StoreLocation::findOrFail($this->storeLocationId);

        $storeLocation->name = $this->name;
        $storeLocation->address = $this->address;
        $storeLocation->contact_numbers = array_values($this->contact_numbers);
        $storeLocation->operating_time = array_values($this->operating_time);
        $storeLocation->outlet_type = $this->outlet_type;

        $storeLocation->save();

        // Sync brands (assuming many-to-many relationship)
       

        session()->flash('success', 'Store location updated successfully.');

        // Optionally redirect or stay on page
        return redirect()->route('admin.store_location.index');
    }

    public function render()
    {
        return view('livewire.admin.store-location-edit');
    }
}
