<div>
  <div class="row mb-4">
    {{-- Brands Table Section --}}
     @if($active_tab==1)
        <div class="col-lg-12 d-flex justify-content-end">
            <button type="button"  class="btn btn-primary" wire:click="ActiveCreateTab(2)">
                <i class="ri-add-line ri-16px me-0 me-sm-2 align-baseline"></i>
                New Collection
            </button>
        </div>
    @else
        <div class="col-lg-12 d-flex justify-content-end">
            <button type="button" class="btn btn-dark btn-sm waves-effect waves-light" wire:click="ActiveCreateTab(1)" role="button">
                <i class="ri-arrow-go-back-line"></i> Back
            </button>
        </div>
    @endif
    @if($active_tab==2)
    <div class="col-lg-12 col-md-6 mb-md-0 my-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5>New collection</h5>
                        <form wire:submit.prevent="newSubmit">
                            <!-- Coupon Code -->
                            <div class="row">
                                <div class="col-4 mb-3">
                                    <label for="brand" class="form-label">Brand Name<span class="text-danger">*</span></label>
                                    <select 
                                        class="form-select @error('brand') is-invalid @enderror" 
                                        id="brand" 
                                        wire:model="brand" wire:change="ChangeDiscountType($event.target.value)">
                                        <option value="" hidden>Select brand</option>
                                        @foreach ($brands as $brand_item)
                                            <option value="{{$brand_item->id}}">{{ucwords($brand_item->name)}}</option>
                                        @endforeach
                                    </select>
                                    @error('brand') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-4 mb-3">
                                    <label for="collection_name" class="form-label">Collection Name <span class="text-danger">*</span></label>
                                    <input 
                                        type="text" 
                                        class="form-control @error('collection_name') is-invalid @enderror" 
                                        id="collection_name" 
                                        placeholder="Enter collection name" 
                                        wire:model="collection_name"
                                        wire:keyup="InputCollectionName($event.target.value)"
                                        >
                                    @error('collection_name') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-4 mb-3">
                                    <label for="slug" class="form-label d-flex justify-content-between">
                                        <span>Slug <span class="text-danger">*</span></span>
                                        <button type="button" class="btn btn-link btn-sm" wire:click="$set('manualSlug', true)">
                                            Make Manually
                                        </button>
                                    </label>
                                    <input 
                                        type="text" 
                                        class="form-control @error('slug') is-invalid @enderror" 
                                        id="slug" 
                                        placeholder="Auto-generated slug" 
                                        wire:model="slug"
                                        @if(!$manualSlug) disabled @endif
                                    >
                                    @error('slug') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <!-- Start Date -->
                                <div class="col-6 mb-3">
                                    <label for="collection_image" class="form-label">Collection Image<span class="text-danger">*</span></label>
                                    <input 
                                        type="file" 
                                        class="form-control @error('collection_image') is-invalid @enderror" 
                                        id="collection_image" 
                                        wire:model="collection_image"
                                    >
                                    @error('collection_image') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="banner" class="form-label">Banner Image<span class="text-danger">*</span></label>
                                    <input 
                                        type="file" 
                                        class="form-control @error('banner') is-invalid @enderror" 
                                        id="banner" 
                                        wire:model="banner"
                                    >
                                    @error('banner') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                    <textarea wire:model="description" class="form-control @error('description') is-invalid @enderror" rows="5" placeholder="Enter description"></textarea>
                                    @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        
                            <!-- Submit Button -->
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @elseif($active_tab==3)
        <div class="col-lg-12 col-md-6 mb-md-0 my-4">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5>Edit Offer</h5>
                            <form wire:submit.prevent="updateOffer">
                                <!-- Coupon Code -->
                                <div class="row">
                                    <div class="col-3 mb-3">
                                        <label for="couponCode" class="form-label">Coupon Code<span class="text-danger">*</span></label>
                                        <input 
                                            type="text" 
                                            class="form-control @error('couponCode') is-invalid @enderror" 
                                            id="couponCode" 
                                            placeholder="Enter coupon code" 
                                            wire:model="couponCode"
                                        >
                                        @error('couponCode') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <!-- Start Date -->
                                    <div class="col-3 mb-3">
                                        <label for="startDate" class="form-label">Start Date<span class="text-danger">*</span></label>
                                        <input 
                                            type="datetime-local" 
                                            class="form-control @error('startDate') is-invalid @enderror" 
                                            id="startDate" 
                                            wire:model="startDate"
                                        >
                                        @error('startDate') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                            
                                    <!-- End Date -->
                                    <div class="col-3 mb-3">
                                        <label for="endDate" class="form-label">End Date<span class="text-danger">*</span></label>
                                        <input 
                                            type="datetime-local" 
                                            class="form-control @error('endDate') is-invalid @enderror" 
                                            id="endDate" 
                                            wire:model="endDate"
                                        >
                                        @error('endDate') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <!-- Discount Type -->
                                    <div class="col-3 mb-3">
                                        <label for="editDiscountType" class="form-label">Discount Type<span class="text-danger">*</span></label>
                                        <select 
                                            class="form-select @error('discountType') is-invalid @enderror" 
                                            id="editDiscountType" 
                                            wire:model="discountType" wire:change="ChangeDiscountType($event.target.value)">
                                            <option value="" hidden>Select discount type</option>
                                            <option value="flat">Flat Discount</option>
                                            <option value="percentage">Percentage Discount</option>
                                        </select>
                                        @error('discountType') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <!-- Discount Value -->
                                    <div class="col-3 mb-3">
                                        <label for="discountValue" class="form-label">Discount Value 
                                        <span class="text-danger">  
                                                @if ($active_dis_value === 'flat')
                                                    (Flat)
                                                @elseif ($active_dis_value === 'percentage')
                                                    (%)
                                                @endif
                                            </span><span class="text-danger">*</span>
                                        </label>
                                        <input 
                                            type="number" 
                                            class="form-control @error('discountValue') is-invalid @enderror" 
                                            id="discountValue" 
                                            placeholder="Enter discount value" 
                                            wire:model="discountValue"
                                        >
                                        @error('discountValue') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <!-- Minimum Order Amount -->
                                    <div class="col-3 mb-3">
                                        <label for="minOrderAmount" class="form-label">Minimum Order Amount<span class="text-danger">*</span></label>
                                        <input 
                                            type="number" 
                                            class="form-control @error('minOrderAmount') is-invalid @enderror" 
                                            id="minOrderAmount" 
                                            placeholder="Enter minimum order amount" 
                                            wire:model="minOrderAmount"
                                        >
                                        @error('minOrderAmount') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <!-- Minimum Order Amount -->
                                    <div class="col-3 mb-3">
                                        <label for="maximum_discount" class="form-label">Maximum Discount Amount</label>
                                        <input 
                                            type="number" 
                                            class="form-control @error('maximum_discount') is-invalid @enderror" 
                                            id="maximum_discount" 
                                            placeholder="Enter maximum discount" 
                                            wire:model="maximum_discount"
                                        >
                                        @error('maximum_discount') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <!-- Usage Limit -->
                                    <div class="col-3 mb-3">
                                        <label for="usageLimit" class="form-label">Global Usage Limit (optional)</label>
                                        <input 
                                            type="number" 
                                            class="form-control @error('usageLimit') is-invalid @enderror" 
                                            id="usageLimit" 
                                            placeholder="Enter global usage limit" 
                                            wire:model="usageLimit"
                                        >
                                        @error('usageLimit') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                            
                                    <!-- Usage Per User -->
                                    <div class="col-3 mb-3">
                                        <label for="usagePerUser" class="form-label">Usage Per User (optional)</label>
                                        <input 
                                            type="number" 
                                            class="form-control @error('usagePerUser') is-invalid @enderror" 
                                            id="usagePerUser" 
                                            placeholder="Enter usage limit per user" 
                                            wire:model="usagePerUser"
                                        >
                                        @error('usagePerUser') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            
                                <!-- Submit Button -->
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                    <button type="button" class="btn btn-dark btn-sm waves-effect waves-light" wire:click="resetForm">Cancel</button>
                                </div>
                            </form>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="col-lg-12 col-md-12 mb-md-0 mb-4">
        <div class="card my-4">
            <div class="card-header pb-2">
            {{-- Flash Message --}}
            @if(session()->has('message'))
                <div class="alert alert-success" id="flashMessage">
                {{ session('message') }}
                </div>
            @endif

            {{-- Header and Search --}}
            <div class="row">
                <div class="col-lg-8 col-8">
                <h6>Collections</h6>
                </div>
                <div class="col-lg-4 col-4 my-auto text-end">
                <div class="ms-md-auto d-flex align-items-center">
                    <input type="text" wire:model.debounce.500ms="search"
                    class="form-control border border-2 p-2 custom-input-sm"
                    placeholder="search here..." wire:keyup="UpdateSearch($event.target.value)">
                    <button type="button" wire:click="refreshPage"
                    class="btn btn-danger text-white mb-0 custom-input-sm ms-2">
                    <i class="ri-restart-line"></i>
                    </button>
                </div>
                </div>
            </div>
            </div>

            {{-- Table --}}
            <div class="card-body px-0 pb-2">
            <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                <thead>
                    <tr>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                        SL
                    </th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                        Collection name
                    </th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                        Brand
                    </th>
                    <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 px-4">
                        Actions
                    </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($collections as $k => $collection_item)
                    <tr>
                        <td class="align-middle text-center">{{ $k + 1 }}</td>
                        <td class="align-middle text-center">{{ ucwords($collection_item->collection_name) }}</td>
                        <td class="align-middle text-center">{{ ucwords($collection_item->brand?$collection_item->brand->name:"N/A") }}</td>
                        <td class="align-middle text-end px-4">
                        <button wire:click="edit({{ $collection_item->id }})"
                            class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect"
                            title="Edit">
                            <i class="ri-edit-box-line ri-20px text-info"></i>
                        </button>
                        <button wire:click="confirmDelete({{ $collection_item->id }})"
                            class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect"
                            title="Delete">
                            <i class="ri-delete-bin-7-line ri-20px text-danger"></i>
                        </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4">No collections found.</td>
                    </tr>
                    @endforelse
                </tbody>
                </table>

                {{-- Pagination --}}
                <div class="d-flex justify-content-end mt-2">
                {{ $collections->links() }}
                </div>
            </div>
            </div>
        </div>
        </div>
    @endif
  </div>

  {{-- Global Loader --}}
  <div class="loader-container" wire:loading>
    <div class="loader"></div>
  </div>
</div>
@section('page-script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
         window.addEventListener('showConfirm', function (event) {
            let itemId = event.detail[0].itemId;
            Swal.fire({
                title: "Delete Brand?",
                text: "Are you sure you want to delete the brand?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('DestroyData', itemId); // Livewire method
                }
            });
        });
    </script>
@endsection
