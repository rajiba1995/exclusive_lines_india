<?php

namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\Collection;
use App\Models\Brand;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Pagination\Paginator;

class CollectionIndex extends Component
{
     use WithPagination,WithFileUploads;

    public $brands = [];
    public $search = '';
    public $active_tab = 1;
    protected $paginationTheme = 'bootstrap';
    public $collectionId,$brand, $collection_name, $slug, $description, $collection_image, $banner,$collection_image_path,$banner_path;
    public $manualSlug = 'false';

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
        $this->ResetAllField();
    }

    public function ActiveCreateTab($value){
        $this->active_tab = $value;
    }
    public function InputCollectionName($value)
    {
        if ($this->manualSlug=='false') {
            $this->slug = \Str::slug($value);
        }
    }
    public function setmanualSlug($status){
        if($status=='false'){
            $this->manualSlug = 'true';
            $this->slug = "";
        }else{
            $this->slug = \Str::slug($this->collection_name);
             $this->manualSlug = 'false';
        }
    }

    public function ResetAllField(){
        $this->reset(['brand', 'collection_name', 'slug', 'collection_image', 'banner', 'description', 'manualSlug','collection_image_path','banner_path','collectionId','search']);
    }
    public function editCollection($id)
    {
        $collection = Collection::findOrFail($id);

        $this->collectionId = $collection->id;
        $this->brand = $collection->brand_id;
        $this->collection_name = $collection->collection_name;
        $this->slug = $collection->slug;
        $this->description = $collection->description;
        $this->collection_image_path = $collection->collection_image;
        $this->banner_path = $collection->banner;
        $this->manualSlug = 'true';
         $this->active_tab = 2;
        $this->resetErrorBag();
        $this->resetValidation();
    }
    public function newSubmit(){
        $this->validate([
            'brand' => 'required|exists:brands,id',
            'collection_name' => 'required|string|max:255',
            'slug' => 'required|string|unique:collections,slug',
            'collection_image' => 'required|image|max:5048',
            'banner' => 'required|image|max:5048',
            'description' => 'required|string',
        ]);
        
        DB::beginTransaction();

        try {
            if ($this->collection_image) {
                $collection_image = storeFileWithCustomName($this->collection_image, 'uploads/collections');
            }
            if ($this->banner) {
                $banner = storeFileWithCustomName($this->banner, 'uploads/collections');
            }

            Collection::create([
                'brand_id' => $this->brand,
                'collection_name' => $this->collection_name,
                'slug' => $this->slug,
                'collection_image' => $collection_image,
                'banner' => $banner,
                'description' => $this->description,
            ]);

            DB::commit();
            $this->active_tab = 1;
            session()->flash('success', 'Collection created successfully!');

            // Reset form fields
            $this->ResetAllField();
         } catch (\Throwable $e) {
            DB::rollBack();

            // Optional: Log error or handle it
            session()->flash('error', $e->getMessage());

            // You can also rethrow the exception if needed
            // throw $e;
        }
    }

    public function updateSubmit()
    {
        $this->validate([
            'brand' => 'required|exists:brands,id',
            'collection_name' => 'required|string|max:255',
            'slug' => 'required|string|unique:collections,slug,' . $this->collectionId,
            'collection_image' => 'nullable|image|max:5048',
            'banner' => 'nullable|image|max:5048',
            'description' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $collection = Collection::findOrFail($this->collectionId);

            if ($this->collection_image) {
                $collection_image = storeFileWithCustomName($this->collection_image, 'uploads/collections');
                $collection->collection_image = $collection_image;
            }

            if ($this->banner) {
                $banner = storeFileWithCustomName($this->banner, 'uploads/collections');
                $collection->banner = $banner;
            }

            $collection->brand_id = $this->brand;
            $collection->collection_name = $this->collection_name;
            $collection->slug = $this->slug;
            $collection->description = $this->description;

            $collection->save();

            $this->active_tab = 1;
            DB::commit();

            session()->flash('success', 'Collection updated successfully!');

            $this->ResetAllField();
        } catch (\Throwable $e) {
            DB::rollBack();
            session()->flash('error', $e->getMessage());
            // Log the error if needed: Log::error($e->getMessage());
        }
    }
    public function confirmDelete($id){
        $this->dispatch('showConfirm', ['itemId' => $id]);
    }

    public function DestroyData($id)
    {
        Collection::findOrFail($id)->delete();

        session()->flash('message', 'Collection deleted successfully!');
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
