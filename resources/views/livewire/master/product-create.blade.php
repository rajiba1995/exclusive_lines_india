<div>
    <div class="row gx-4 mb-4">
        <div class="col-auto my-auto">
        <div class="h-100">
            <h5 class="mb-0">Master Management</h5>
            <div>
            <small class="text-dark fw-medium">Product </small>
            <small class="text-light fw-medium arrow">Create</small>
            </div>
        </div>
        </div>
        <div class="col-lg-4 col-md-6 my-sm-auto ms-sm-auto me-sm-0 mx-auto mt-3">
            <div class="nav-wrapper position-relative end text-end">
                 <a href="{{route('admin.product.index')}}" class="btn btn-dark btn-sm waves-effect waves-light">
                    <i class="ri-arrow-go-back-line"></i> Back
                </a>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        @if (session()->has('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
    </div>
    <div class="col-lg-12 col-md-6 mb-md-0 my-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link text-uppercase {{$active_tab==1?"active bg-primary text-white":""}}" href="javascript:void(0)" wire:click="TabChange(1)">Basic Details</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-uppercase {{$active_tab==2?"active bg-primary text-white":""}}" href="javascript:void(0)" wire:click="validateStepOne">Specifications</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-uppercase {{$active_tab==3?"active bg-primary text-white":""}}" href="javascript:void(0)" wire:click="goToNextTab">Images</a>
                            </li>
                        </ul>

                        <div class="border border-top-0 p-4">
                            @if($active_tab==1)
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" wire:model="status">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                        @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error("name") is-invalid @enderror" wire:model="name" wire:input="InputProductName($event.target.value)">
                                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex w-100 flex-wrap justify-content-between gap-2">
                                            <label class="form-label">Slug<span class="text-danger">*</span></label>
                                                @if($manualSlug=='false')
                                                    <span class="badge bg-label-primary rounded-pill cursor-pointer" wire:click="setmanualSlug('{{$manualSlug}}')">
                                                        Make Manually
                                                    </span>
                                                @else
                                                    <span class="badge bg-label-secondary rounded-pill cursor-pointer" wire:click="setmanualSlug('{{$manualSlug}}')">
                                                        Auto Generated
                                                    </span>
                                                @endif
                                        </div>
                                        <input type="text" class="form-control @error("slug") is-invalid @enderror" placeholder="{{ $manualSlug=='true' ? 'Enter slug manually' : 'Auto-generated slug' }}" 
                                        wire:model="slug"
                                        @if($manualSlug=='false') disabled @endif>
                                        @error('slug') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">SKU<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error("sku") is-invalid @enderror" wire:model="sku">
                                        @error('sku') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Subheading</label>
                                        <input type="text" class="form-control" wire:model="subheading">
                                        @error('subheading') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="col-md-3 mb-3 d-flex align-items-center">
                                        <input type="checkbox" class="form-check-input me-2" wire:model="new_arrival" id="newArrival">
                                        <label for="newArrival" class="form-check-label">New Arrival</label>
                                    </div>
                                    <div class="col-md-3 mb-3 d-flex align-items-center">
                                        <input type="checkbox" class="form-check-input me-2" wire:model="best_seller" id="bestSeller">
                                        <label for="bestSeller" class="form-check-label">Best Seller</label>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">MRP<span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error("mrp") is-invalid @enderror" wire:model="mrp">
                                        @error('mrp') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Offer Price</label>
                                        <input type="number" class="form-control @error("offer_price") is-invalid @enderror" wire:model="offer_price">
                                        @error('offer_price') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Badge</label>
                                        <input type="text" class="form-control" wire:model="badge">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Brand<span class="text-danger">*</span></label>
                                        <select class="form-select @error("brand") is-invalid @enderror" wire:model="brand" wire:change="UpdateBrand($event.target.value)">
                                            <option value="" selected hidden>-- Select Brand --</option>
                                            @foreach ($brands as $brand_item)
                                                 <option value="{{$brand_item->id}}">{{ucwords($brand_item->name)}}</option>  
                                            @endforeach
                                        </select>
                                        @error('brand') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Collection</label>
                                        <select class="form-select" wire:model="collection">
                                            <option value="" selected hidden>-- Select Collection --</option>
                                            @foreach ($collections as $collection_item)
                                                 <option value="{{$collection_item->id}}">{{ucwords($collection_item->collection_name)}}</option>  
                                            @endforeach
                                        </select>
                                        @error('collection') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Short Description</label>
                                        <textarea class="form-control" rows="4" wire:model="short_description" id="short_description"></textarea>
                                        @error('short_description') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Long Description</label>
                                        <textarea class="form-control" rows="4" wire:model="long_description" id="long_description"></textarea>
                                        @error('long_description') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button wire:click="validateStepOne" class="btn btn-primary btn-sm">
                                        Next <i class="ri-arrow-right-line"></i>
                                    </button>
                                </div>
                            @endif
                            @if($active_tab == 2)
                                <!-- Fixed Specifications -->
                                <div class="d-flex align-items-center border-top py-3">
                                    <span class="text-danger me-2">
                                        <i class="icon-base ri ri-circle-fill icon-16px"></i>
                                    </span>
                                    <h6 class="fw-normal mb-0">Fixed Specifications</h6>
                                </div>

                                <div class="row g-3 align-items-center mb-3">
                                    <div class="col-2 fw-semibold">Gender</div>
                                    <div class="col">
                                        <input type="text" class="form-control @error('fixedSpecifications.gender.spec_value') is-invalid @enderror" placeholder="Spec Value" wire:model="fixedSpecifications.gender.spec_value">
                                         @error('fixedSpecifications.gender.spec_value') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control @error('fixedSpecifications.gender.spec_category') is-invalid @enderror" placeholder="Spec Category" wire:model="fixedSpecifications.gender.spec_category">
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control @error('fixedSpecifications.gender.sequence') is-invalid @enderror" placeholder="Sequence" wire:model="fixedSpecifications.gender.sequence">
                                        @error('fixedSpecifications.gender.sequence') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="row g-3 align-items-center mb-3">
                                    <div class="col-2 fw-semibold">Collection</div>
                                    <div class="col">
                                        <input type="text" class="form-control @error('fixedSpecifications.collection.spec_value') is-invalid @enderror" placeholder="Spec Value" wire:model="fixedSpecifications.collection.spec_value">
                                        @error('fixedSpecifications.collection.spec_value') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control @error('fixedSpecifications.collection.spec_category') is-invalid @enderror" placeholder="Spec Category" wire:model="fixedSpecifications.collection.spec_category">
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control @error('fixedSpecifications.collection.sequence') is-invalid @enderror" placeholder="Sequence" wire:model="fixedSpecifications.collection.sequence">
                                        @error('fixedSpecifications.collection.sequence') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="row g-3 align-items-center mb-4">
                                    <div class="col-2 fw-semibold">Model</div>
                                    <div class="col">
                                        <input type="text" class="form-control @error('fixedSpecifications.model.spec_value') is-invalid @enderror" placeholder="Spec Value" wire:model="fixedSpecifications.model.spec_value">
                                        @error('fixedSpecifications.model.spec_value') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control @error('fixedSpecifications.model.spec_category') is-invalid @enderror" placeholder="Spec Category" wire:model="fixedSpecifications.model.spec_category">
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control @error('fixedSpecifications.model.sequence') is-invalid @enderror" placeholder="Sequence" wire:model="fixedSpecifications.model.sequence">
                                        @error('fixedSpecifications.model.sequence') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <!-- Other Specifications -->
                                <div class="d-flex align-items-center border-top py-3">
                                    <span class="text-danger me-2">
                                        <i class="icon-base ri ri-circle-fill icon-16px"></i>
                                    </span>
                                    <h6 class="fw-normal mb-0">Other Specifications</h6>
                                </div>

                                @foreach($otherSpecifications as $index => $spec)
                                <div class="row g-3 align-items-center mb-3">
                                    <div class="col">
                                        <input type="text" class="form-control @error("otherSpecifications.$index.spec_name") is-invalid @enderror" placeholder="Spec Name" wire:model="otherSpecifications.{{ $index }}.spec_name">
                                        @error("otherSpecifications.$index.spec_name")
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control @error("otherSpecifications.$index.spec_value") is-invalid @enderror" placeholder="Spec Value" wire:model="otherSpecifications.{{ $index }}.spec_value">
                                        @error("otherSpecifications.$index.spec_value")
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control @error("otherSpecifications.$index.spec_category") is-invalid @enderror" placeholder="Spec Category" wire:model="otherSpecifications.{{ $index }}.spec_category">
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control @error("otherSpecifications.$index.sequence") is-invalid @enderror" placeholder="Sequence" wire:model="otherSpecifications.{{ $index }}.sequence">
                                        @error("otherSpecifications.$index.sequence") <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-outline-danger" wire:click.prevent="removeOtherSpecification({{ $index }})">
                                            <i class="ri-delete-bin-6-line"></i>
                                        </button>
                                    </div>
                                </div>
                                @endforeach

                                <div class="mb-3 text-end">
                                    <button class="btn btn-outline-primary btn-sm" wire:click.prevent="addOtherSpecification">
                                        <i class="ri-add-line"></i> Add New Specification
                                    </button>
                                </div>

                                <div class="text-end">
                                    <button wire:click="TabChange(1)" class="btn btn-outline-secondary btn-sm">
                                        <i class="ri-arrow-left-line"></i> Previous
                                    </button>
                                    <button class="btn btn-primary btn-sm" wire:click="goToNextTab">Next <i class="ri-arrow-right-line"></i></button>
                                </div>
                            @endif


                            @if($active_tab == 3)
                                <div class="row g-4 mb-4">
                                    <!-- Main Image -->
                                    <div class="col-md-6">
                                        <label class="fw-bold text-success mb-2">Main Image *</label>
                                        <input type="file" class="form-control" wire:model="main_image" accept="image/*">
                                        @error('main_image') <small class="text-danger">{{ $message }}</small> @enderror

                                        @if ($main_image)
                                            <div class="mt-2">
                                                <img src="{{ $main_image->temporaryUrl() }}" class="img-thumbnail" style="max-height: 200px;" />
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Gallery Images -->
                                    <div class="col-md-6">
                                        <label class="fw-bold text-success mb-2">Gallery Images</label>
                                        <input type="file" class="form-control" wire:model="gallery_images" accept="image/*" multiple>
                                        @error('gallery_images.*') <small class="text-danger">{{ $message }}</small> @enderror

                                        @if ($gallery_images)
                                            <div class="mt-2 d-flex flex-wrap gap-2">
                                                @foreach ($gallery_images as $image)
                                                    <img src="{{ $image->temporaryUrl() }}" class="img-thumbnail" style="max-height: 100px;">
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button wire:click="goToPreviousStep" class="btn btn-outline-secondary btn-sm">
                                        <i class="ri-arrow-left-line"></i> Previous
                                    </button>
                                    <button wire:click="submitForm" class="btn btn-primary btn-sm">Submit</button>
                                </div>
                            @endif

                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
     {{-- Global Loader --}}
    <div wire:loading class="loader-container" wire:target="validateStepOne,TabChange,UpdateBrand,setmanualSlug,addOtherSpecification,removeOtherSpecification,goToNextTab,submitForm">
        <div class="loader"></div>
    </div>
</div>
@section('page-script')
  <script type="text/javascript" src="{{ asset('build/ckeditor/ckeditor.js') }}"></script>
<script>
    window.addEventListener('ck_editor_load', function(event) { 
        // Handle short_desc_editor
        var shortDescTextArea = document.getElementById('short_description');
        if (shortDescTextArea) {
            // Check if CKEditor instance already exists and destroy it
            if (CKEDITOR.instances['short_description']) {
                CKEDITOR.instances['short_description'].destroy(true);
            }
            
            // Initialize CKEditor for short_desc_editor
            if (typeof CKEDITOR !== 'undefined') {
                CKEDITOR.replace('short_description');

                // Sync CKEditor data to Livewire
                CKEDITOR.instances['short_description'].on('change', function() {
                    @this.set('short_description', CKEDITOR.instances['short_description'].getData());
                });
            } else {
                console.error('CKEditor is not defined!');
            }
        }
        var longDescTextArea = document.getElementById('long_description');
        if (longDescTextArea) {
            // Check if CKEditor instance already exists and destroy it
            if (CKEDITOR.instances['long_description']) {
                CKEDITOR.instances['long_description'].destroy(true);
            }
            
            // Initialize CKEditor for short_desc_editor
            if (typeof CKEDITOR !== 'undefined') {
                CKEDITOR.replace('long_description');

                // Sync CKEditor data to Livewire
                CKEDITOR.instances['long_description'].on('change', function() {
                    @this.set('long_description', CKEDITOR.instances['long_description'].getData());
                });
            } else {
                console.error('CKEditor is not defined!');
            }
        }
    });
</script>
@endsection
