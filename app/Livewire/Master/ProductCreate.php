<?php

namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Collection;
use App\Models\ProductSpecification;
use App\Models\ProductGalleryImage;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;

class ProductCreate extends Component
{
    use WithFileUploads;
    public $active_tab = 1;
    public $brands = [];
    public $collections = [];
    public $fixedSpecifications = [
        'gender' => ['spec_value' => '', 'spec_category' => '', 'sequence' => '1'],
        'collection' => ['spec_value' => '', 'spec_category' => '', 'sequence' => '2'],
        'model' => ['spec_value' => '', 'spec_category' => '', 'sequence' => '3'],
    ];
    public $otherSpecifications = [];

    public $status = 1, $name, $slug, $sku, $subheading, $new_arrival = true, $best_seller = false;
    public $mrp, $offer_price, $badge, $brand, $collection, $short_description, $long_description;
    public $main_image;
    public $gallery_images = [];
    public $manualSlug = 'false';

    public function mount(){
        $this->brands = Brand::where('status', 1)->orderBy('name','ASC')->get();
    }
    public function InputProductName($value)
    {
        if ($this->manualSlug=='false') {
            $this->slug = \Str::slug($value);
        }
        $this->resetErrorBag();
        $this->resetValidation();
    }
    public function setmanualSlug($status){
        if($status=='false'){
            $this->manualSlug = 'true';
            $this->slug = "";
        }else{
            $this->slug = \Str::slug($this->name);
             $this->manualSlug = 'false';
        }
        $this->resetErrorBag();
        $this->resetValidation();
    }
    public function UpdateBrand($value){
        $this->collections = Collection::where('brand_id',$value)->orderBy('collection_name','ASC')->get();
    }
    public function TabChange($value){
        $this->active_tab = $value;
    }
    public function addOtherSpecification()
    {
      $nextSequence = count($this->otherSpecifications) + 1;

        $this->otherSpecifications[] = [
            'spec_name' => '',
            'spec_value' => '',
            'spec_category' => '',
            'sequence' => $nextSequence,
        ];
    }
    public function removeOtherSpecification($index)
    {
         unset($this->otherSpecifications[$index]);
        $this->otherSpecifications = array_values($this->otherSpecifications); // Re-index array

        // Recalculate sequence numbers
        foreach ($this->otherSpecifications as $i => $spec) {
            $this->otherSpecifications[$i]['sequence'] = $i + 1;
        }
    }

    public function validateStepOne()
    {
        $this->validate([
            'status' => 'required',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|alpha_dash|unique:products,slug',
            'sku' => 'required|string|max:100|unique:products,sku',
            'subheading' => 'nullable|string|max:255',
            'mrp' => 'required|numeric',
            'offer_price' => 'nullable|numeric|lte:mrp',
            'brand' => 'required|string',
            'collection' => 'nullable|string|max:255',
            'short_description' => 'nullable|string|max:1000',
        ]);

        $this->active_tab = 2; // move to next tab after validation passes
    }

        public function goToNextTab()
    {
        $this->validate([
            'fixedSpecifications.*.spec_value' => 'required|string',
            'fixedSpecifications.*.spec_category' => 'nullable|string',
            'fixedSpecifications.*.sequence' => 'nullable|integer',

            'otherSpecifications.*.spec_value' => 'required|string',
            'otherSpecifications.*.spec_category' => 'nullable|string',
            'otherSpecifications.*.sequence' => 'nullable|integer',
        ], [
            'fixedSpecifications.*.spec_name.required' => 'Fixed spec value is required',
            'fixedSpecifications.*.spec_name.string'   => 'Fixed spec must be text',
            'fixedSpecifications.*.spec_value.required' => 'Fixed spec value is required',
            'fixedSpecifications.*.spec_value.string'   => 'Fixed spec must be text',
            'fixedSpecifications.*.spec_category.string' => 'Fixed category must be text',
            'fixedSpecifications.*.sequence.integer'     => 'Fixed sequence must be number',

            'otherSpecifications.*.spec_name.required' => 'Other spec value is required',
            'otherSpecifications.*.spec_name.string'   => 'Other spec must be text',
            'otherSpecifications.*.spec_value.required' => 'Other spec value is required',
            'otherSpecifications.*.spec_value.string'   => 'Other spec must be text',
            'otherSpecifications.*.spec_category.string' => 'Other category must be text',
            'otherSpecifications.*.sequence.integer'     => 'Other sequence must be number',
        ]);
        $this->active_tab = 3;
    }

    public function submitForm(){
        // dd($this->all());
         $this->validate([
            'main_image' => 'required|image|max:5048',
            'gallery_images.*' => 'nullable|image|max:5048',
        ]);

        DB::beginTransaction();

        try {
            $image = null;
            if ($this->main_image) {
                $image = storeFileWithCustomName($this->main_image, 'uploads/products');
            }
            // Example:
            $product = Product::create([
                'status' => $this->status,
                'name' => $this->name,
                'slug' => $this->slug,
                'sku' => $this->sku,
                'subheading' => $this->subheading,
                'new_arrival' => $this->new_arrival==true?1:0,
                'best_seller' => $this->best_seller==true?1:0,
                'mrp' => $this->mrp,
                'offer_price' => $this->offer_price,
                'badge' => $this->badge,
                'brand_id' => $this->brand,
                'collection_id' => $this->collection,
                'short_description' => $this->short_description,
                'long_description' => $this->long_description,
                'image' => $image,
            ]);

            // Specifications
            foreach($this->fixedSpecifications as $f_index=>$f_spec){
                ProductSpecification::create([
                    'product_id'=>$product->id,
                    'type'=>'fixed',
                    'spec_name'=>$f_index,
                    'spec_value'=>$f_spec['spec_value'],
                ]);
            }
            // others Specifications
            foreach($this->otherSpecifications as $o_index=>$o_spec){
                ProductSpecification::create([
                    'product_id'=>$product->id,
                    'type'=>'fixed',
                    'spec_name'=>$o_spec['spec_name'],
                    'spec_value'=>$o_spec['spec_value'],
                    'spec_category'=>$o_spec['spec_category'],
                ]);
            }

            // Image Gallery
            foreach ($this->gallery_images as $g_image) {
               $image_item = storeFileWithCustomName($g_image, 'uploads/products');
               ProductGalleryImage::create([
                    'product_id'=>$product->id,
                    'image'=>$image_item,
               ]);
            }

            DB::commit();

            session()->flash('message', 'Product created successfully!');
            return redirect()->route('admin.product.index');
        } catch (\Throwable $e) {
            DB::rollBack();
            session()->flash('error', $e->getMessage());
            // Log the error if needed: Log::error($e->getMessage());
        }
    }
    public function render()
    {
        $this->dispatch('ck_editor_load');
        return view('livewire.master.product-create');
    }
}
