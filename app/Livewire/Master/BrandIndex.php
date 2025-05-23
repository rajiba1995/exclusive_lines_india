<?php

namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\Brand;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Illuminate\Pagination\Paginator;


class BrandIndex extends Component
{
    use WithPagination;

    public $brandId, $name, $status;
    public $search = '';

    protected $rules = [
        'name' => 'required|string|max:255',
    ];

   public function UpdateSearch($value)
    {
        $this->search = $value;
        $this->resetPage();
    }
    public function save()
    {
        // Dynamically add the unique validation rule when saving
        $rules = $this->rules;
        
        // Add the unique validation rule for title, if updating
        if ($this->brandId) {
            $rules['name'] .= '|unique:brands,name,' . $this->brandId;
        } else {
            $rules['name'] .= '|unique:brands,name';
        }

        // Validate with the dynamically created rules
        $this->validate($rules);

        // Create or update logic
        if ($this->brandId) {
            $brand = Brand::findOrFail($this->brandId);
            $brand->name = $this->name;
            $brand->save();
            session()->flash('message', 'Brand updated successfully!');
        } else {
            
            $brand = new Brand([
                'name' => $this->name,
                'status' => true,
            ]);
            
            $brand->save();
            session()->flash('message', 'Brand created successfully!');
            $this->reset(['name']);
            $this->refreshPage();
        }
    }

    public function edit($id)
    {
        $brand = Brand::findOrFail($id);

        $this->brandId = $brand->id;
        $this->name = $brand->name;
        $this->status = $brand->status;
    }

    public function confirmDelete($id){
        $this->dispatch('showConfirm', ['itemId' => $id]);
    }
    public function DestroyData($id)
    {
        Brand::findOrFail($id)->delete();

        session()->flash('message', 'Brand deleted successfully!');
        $this->refreshPage();
    }

    // Toggle brand status
    public function toggleStatus($id)
    {
        $brand = Brand::findOrFail($id);
        $brand->status = !$brand->status;
        $brand->save();

        session()->flash('message', 'brand status updated successfully!');
        $this->refreshPage();
    }

    public function refreshPage(){
         $this->resetPage(); // This will reset the pagination to page 1
        $this->reset(['search','name']);
    }
    public function render()
    {
        $brands = Brand::when($this->search, function ($query) {
                $searchTerm = '%' . $this->search . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm);
                });
            })
            ->orderBy('name', 'ASC')
            ->paginate(25);

        return view('livewire.master.brand-index',['brands'=>$brands]);
    }
}
