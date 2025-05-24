<?php

namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;

class ProductIndex extends Component
{
    use WithPagination;
    public $search = '';
    protected $paginationTheme = 'bootstrap';

    public function confirmDelete($id){
        $this->dispatch('showConfirm', ['itemId' => $id]);
    }

    public function DestroyData($id)
    {
        $product = Product::findOrFail($id);

        DB::beginTransaction();

        try {
            // Delete main image from storage
            storageFileUnlink($product->image);

            // Delete gallery images
            foreach ($product->galleryImges as $galleryImage) {

                storageFileUnlink($galleryImage->image_path);
                $galleryImage->delete(); // Delete the DB record
            }

            // Delete related specifications if any
            $product->fixedSpecifications()->delete(); // Assuming relation
            $product->otherSpecifications()->delete(); // Assuming relation

            // Delete product itself
            $product->delete();

            DB::commit();

            session()->flash('message', 'Product deleted successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        $product = Product::findOrFail($id);
        $product->status = !$product->status;
        $product->save();

        session()->flash('message', 'product status updated successfully!');
        $this->refreshPage();
    }
    public function UpdateSearch($value){
        $this->search = $value;
        $this->resetPage();
    }
    public function refreshPage(){
         $this->resetPage(); // This will reset the pagination to page 1
         $this->reset(['search']);
    }
    public function render()
    {
         $products = Product::when($this->search, function ($query) {
                $searchTerm = '%' . $this->search . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm)
                    ->Orwhere('slug', 'like', $searchTerm)
                    ->Orwhere('subheading', 'like', $searchTerm)
                    ->Orwhere('sku', 'like', $searchTerm)
                    ->Orwhere('short_description', 'like', $searchTerm)
                    ->Orwhere('long_description', 'like', $searchTerm)
                    ->Orwhere('badge', 'like', $searchTerm)
                    ->Orwhere('brochure', 'like', $searchTerm);
                })->orWhereHas('brand', function ($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm);
                })->orWhereHas('collection', function ($q) use ($searchTerm) {
                    $q->where('collection_name', 'like', $searchTerm);
                });
            })
            ->orderBy('name', 'ASC')
            ->paginate(25);
        return view('livewire.master.product-index',[
            'products'=>$products
        ]);
    }
}
