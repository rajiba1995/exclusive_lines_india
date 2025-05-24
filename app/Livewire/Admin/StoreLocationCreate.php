<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Brand;
use App\Models\StoreLocation;

class StoreLocationCreate extends Component
{
     public $name, $address, $contact_numbers = [''], $operating_time = [[ 'time' => '']], $brands, $outlet_type;
    public $selectedBrands = [];
    public $brandsList;

   

    public function mount(){
        $this->brandsList = Brand::where('status',1)->orderBy('positions')->get();
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
        $this->operating_time[] = [ 'time' => ''];
    }

    public function removeTime($index)
    {
        unset($this->operating_time[$index]);
        $this->operating_time = array_values($this->operating_time);
    }

    public function rules(){
        return  [
             'name' => 'required|string|max:255',
            'address' => 'required|string',
            'contact_numbers.*' =>  ['required', 'regex:/^[\d+\- ]+$/'],
            'operating_time.*.time' => 'required|string',
            'selectedBrands' => 'required',
            'outlet_type' => 'required|in:Multi Brand Outlets,Exclusive Brand Outlets',
        ];
    }

    public function messages(){
        return [
            'name.required' => 'The outlet name is required.',
            'address.required' => 'The address is required.',
            'contact_numbers.*.required' => 'Each contact number is required.',
            'operating_time.*.time.required' => 'Please provide the time range for each day.',
            'selectedBrands.required' => 'Please select a brand',
            'outlet_type.required' => 'Please select an outlet type.',
            'outlet_type.in' => 'The selected outlet type is invalid.',
        ];
    }

    public function store()
    {
        $this->validate();
        // dd($this->all());

        StoreLocation::create([
            'name' => $this->name,
            'address' => $this->address,
            'contact_numbers' => json_encode($this->contact_numbers),
            'operating_time' =>  json_encode($this->operating_time),
            'brands' => implode(',', $this->selectedBrands),
            'outlet_type' => $this->outlet_type,
            'uploaded_by' => auth()->user()->name ?? 'Admin'
        ]);

        session()->flash('message', 'Store location created successfully!');
        return redirect()->route('admin.store_location.index');
    }
    
    public function render()
    {
        return view('livewire.admin.store-location-create');
    }
}
