<?php

namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\Collection;
use App\Models\Brand;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Illuminate\Pagination\Paginator;

class CollectionIndex extends Component
{
     use WithPagination;

    public $brands = [];
    public $search = '';
    public $active_tab = 1;
    public $brand, $collection_name, $slug, $description, $collection_image, $banner;
    public $manualSlug = false;

     protected $rules = [
        'brand' => 'required|exists:brands,id',
        'collection_name' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:collections,slug',
        'description' => 'required|string',
        'collection_image' => 'required|image|max:2048', // 2MB
        'banner' => 'required|image|max:2048',
    ];

    public function mount(){
        $this->brands = Brand::where('status',1)->orderBy('name','ASC')->get();
    }
   public function UpdateSearch($value){
        $this->search = $value;
        $this->resetPage();
    }
    
    public function refreshPage(){
         $this->resetPage(); // This will reset the pagination to page 1
        $this->reset(['search','name']);
    }

    public function ActiveCreateTab($value){
        $this->active_tab = $value;
    }
    public function InputCollectionName($value)
    {
        if (!$this->manualSlug) {
            $this->slug = \Str::slug($value);
        }
    }
    public function render()
    {
        $collections = Collection::when($this->search, function ($query) {
            $searchTerm = '%' . $this->search . '%';

            $query->where(function ($q) use ($searchTerm) {
                $q->where('collection_name', 'like', $searchTerm)
                ->orWhere('slug', 'like', $searchTerm)
                ->orWhere('description', 'like', $searchTerm);
            })->orWhereHas('brand', function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm);
            });
        })
        ->orderBy('collection_name', 'ASC')
        ->paginate(25);

        return view('livewire.master.collection-index', ['collections' => $collections]);
    }
}
