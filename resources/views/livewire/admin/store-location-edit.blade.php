<div>
    <div class="row gx-4 mb-4">
        <div class="col-auto my-auto">
            <div class="h-100">
                <h5 class="mb-0">Store Location</h5>
                <div>
                    <small class="text-dark fw-medium">Location </small>
                    <small class="text-light fw-medium arrow">Edit</small>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 my-sm-auto ms-sm-auto me-sm-0 mx-auto mt-3">
            <div class="nav-wrapper position-relative end text-end">
                <a href="{{ route('admin.store_location.index') }}" class="btn btn-dark btn-sm waves-effect waves-light">
                    <i class="ri-arrow-go-back-line"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        @if (session()->has('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if (session()->has('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
    </div>

    <div class="col-lg-12 col-md-6 mb-md-0 my-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form wire:submit.prevent="update">
                            <div class="row">
                                <!-- Outlet Name -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Outlet Name</label>
                                    <input type="text" wire:model="name" placeholder="Outlet Name" class="form-control">
                                    @error('name') <p class="text-danger inputerror">{{ $message }}</p> @enderror
                                </div>

                                <!-- Address -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea wire:model="address" placeholder="Address" class="form-control"></textarea>
                                    @error('address') <p class="text-danger inputerror">{{ $message }}</p> @enderror
                                </div>

                                <!-- Phone Numbers -->
                                <div class="col-md-12 mb-2">
                                    <label class="form-label">Phone Numbers</label>
                                    @foreach ($contact_numbers as $index => $phone)
                                        <div class="row mb-2">
                                            <div class="col-md-10">
                                                <input type="text" wire:model="contact_numbers.{{ $index }}" class="form-control" placeholder="Phone Number">
                                                @error('contact_numbers.' . $index) <p class="text-danger inputerror">{{ $message }}</p> @enderror
                                            </div>
                                            <div class="col-md-2 d-flex align-items-start gap-2">
                                                <button type="button" wire:click="removePhone({{ $index }})" class="btn btn-danger btn-sm">
                                                    <i class="ri-subtract-line"></i>
                                                </button>
                                                @if ($loop->last)
                                                    <button type="button" wire:click="addPhone" class="btn btn-success btn-sm">
                                                        <i class="ri-add-line"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Operating Times -->
                                <div class="col-md-12 mb-2">
                                    <label class="form-label">Operating Times</label>
                                    @foreach ($operating_time as $index => $time)
                                        <div class="row mb-2">
                                            <div class="col-md-10">
                                                <input type="text" wire:model="operating_time.{{ $index }}.time" placeholder="10:30am – 9pm, 7 days a week" class="form-control">
                                                @error("operating_time.$index.time") <p class="text-danger inputerror">{{ $message }}</p> @enderror
                                            </div>
                                            <div class="col-md-2 d-flex align-items-start gap-2">
                                                <button type="button" wire:click="removeTime({{ $index }})" class="btn btn-danger btn-sm">
                                                    <i class="ri-subtract-line"></i>
                                                </button>
                                                @if ($loop->last)
                                                    <button type="button" wire:click="addTime" class="btn btn-success btn-sm">
                                                        <i class="ri-add-line"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Brand Select -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Select Brands</label>
                                    <select id="selectBrands" multiple class="form-control" wire:model="selectedBrands">
                                        @foreach($brandsList as $brand)
                                            <option value="{{ $brand->name }}" {{ in_array($brand->name, $selectedBrands) ? 'selected' : '' }}>
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('selectedBrands') <p class="text-danger inputerror">{{ $message }}</p> @enderror
                                </div>

                                <!-- Outlet Type -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Outlet Type</label>
                                    <select wire:model="outlet_type" class="form-select">
                                        <option value="" selected hidden>-- Select Outlet Type --</option>
                                        <option value="Multi Brand Outlets">Multi Brand Outlets</option>
                                        <option value="Exclusive Brand Outlets">Exclusive Brand Outlets</option>
                                    </select>
                                    @error('outlet_type') <p class="text-danger inputerror">{{ $message }}</p> @enderror
                                </div>

                                <!-- Submit -->
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
