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
        <div class="col-lg-12">
            @if (session()->has('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
        </div>
    @else
        <div class="col-lg-12 d-flex justify-content-end">
            <button type="button" class="btn btn-dark btn-sm waves-effect waves-light" wire:click="ActiveCreateTab(1)" role="button">
                <i class="ri-arrow-go-back-line"></i> Back
            </button>
        </div>
        <div class="col-lg-12">
            @if (session()->has('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
        </div>
        
    @endif
    @if($active_tab==2)
    <div class="col-lg-12 col-md-6 mb-md-0 my-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5>{{ $collectionId ? 'Update collection' : 'New collection' }}</h5>
                        <form wire:submit.prevent="{{ $collectionId ? 'updateSubmit' : 'newSubmit' }}">
                            <!-- Coupon Code -->
                            <div class="row">
                                <div class="col-4 mb-3">
                                    <label for="brand" class="form-label">Brand Name<span class="text-danger">*</span></label>
                                    <select 
                                        class="form-select @error('brand') is-invalid @enderror" 
                                        id="brand" 
                                        wire:model="brand">
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
                                        @if($manualSlug=='false')
                                            <button type="button" class="btn btn-link btn-sm" wire:click="setmanualSlug('{{$manualSlug}}')">
                                                Make Manually
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-link btn-sm" wire:click="setmanualSlug('{{$manualSlug}}')">
                                                Auto Generated
                                            </button>
                                        @endif
                                    </label>
                                    <input 
                                        type="text" 
                                        class="form-control @error('slug') is-invalid @enderror" 
                                        id="slug" 
                                        placeholder="{{ $manualSlug=='true' ? 'Enter slug manually' : 'Auto-generated slug' }}" 
                                        wire:model="slug"
                                        @if($manualSlug=='false') disabled @endif
                                    >
                                    @error('slug') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <!-- Start Date -->
                                <div class="col-6 mb-3">
                                    <label for="collection_image" class="form-label">Collection Image<span class="text-danger">*</span></label>
                                    @if($collectionId && $collection_image_path)
                                        <div class="mb-2">
                                            <img src="{{ asset($collection_image_path) }}" alt="collection Image" class="img-thumbnail" style="max-height: 120px;">
                                        </div>
                                    @endif
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
                                    @if($collectionId && $banner_path)
                                        <div class="mb-2">
                                            <img src="{{ asset($banner_path) }}" alt="Banner Image" class="img-thumbnail" style="max-height: 120px;">
                                        </div>
                                    @endif
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
                        <button wire:click="editCollection({{ $collection_item->id }})"
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
                title: "Delete Collection?",
                text: "Are you sure you want to delete the colletion?",
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
