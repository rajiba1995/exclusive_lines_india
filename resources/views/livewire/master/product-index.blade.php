<div>
  <div class="row mb-4">
    <div class="col-lg-12 d-flex justify-content-end">
        <button class="btn buttons-collection btn-outline-secondary btn-sm  mx-4 waves-effect" type="button">
          <span>
              <span class="d-flex align-items-center">
                <i class="icon-base ri ri-upload-2-line icon-16px me-sm-2"></i>
              <span class="d-none d-sm-inline-block">Import Product</span>
            </span>
          </span>
        </button>
        <button class="btn buttons-collection btn-outline-secondary btn-sm mx-4 waves-effect" type="button">
          <span>
              <span class="d-flex align-items-center">
                <i class="icon-base ri ri-upload-2-line icon-16px me-sm-2"></i>
              <span class="d-none d-sm-inline-block">Import Specifications</span>
            </span>
          </span>
        </button>
        <a href="{{route('admin.product.create')}}" class="btn btn-primary">
            <i class="ri-add-line ri-16px me-0 me-sm-2 align-baseline"></i>
            New Product
        </a>
    </div>
    {{-- Product Table Section --}}
    <div class="col-lg-12 col-md-12 mb-md-0 mb-4">
      <div class="card my-4">
        <div class="card-header pb-2">
          {{-- Flash Message --}}
          @if(session()->has('message'))
            <div class="alert alert-success">
              {{ session('message') }}
            </div>
          @endif
          @if(session()->has('error'))
            <div class="alert alert-danger">
              {{ session('error') }}
            </div>
          @endif

          {{-- Header and Search --}}
          <div class="row">
            <div class="col-lg-6 col-7">
              <h6>Products</h6>
            </div>
            <div class="col-lg-6 col-5 my-auto text-end">
              <div class="ms-md-auto d-flex align-items-center">
                <input type="text" wire:model.debounce.500ms="search"
                  class="form-control border border-2 p-2 custom-input-sm"
                  placeholder="Search here..." wire:keyup="UpdateSearch($event.target.value)">
                  <button type="button" wire:click="refreshPage"
                  class="btn btn-danger text-white mb-0 custom-input-sm ms-2">
                  <i class="ri-restart-line"></i>
                </button>
                <div class="btn-group mx-4">
                  <button class="btn buttons-collection btn-outline-secondary btn-sm dropdown-toggle waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="d-flex align-items-center">
                      <i class="icon-base ri ri-download-line icon-16px me-sm-2"></i> 
                      <span class="d-none d-sm-inline-block">Export</span>
                    </span>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Export Products</a></li>
                    <li><a class="dropdown-item" href="#">Export Product Specifications</a></li>
                  </ul>
                </div>

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
                  <th class=" text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                    Name
                  </th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                    SKU
                  </th>
                  {{-- <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                    Price
                  </th> --}}
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                    Brand
                  </th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                    Status
                  </th>
                  <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 px-4">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody>
                @forelse($products as $k => $product)
                  <tr>
                    <td class="align-middle text-center">{{ $k + 1 }}</td>
                    <td class="align-middle text-center">
                      <div class="d-flex justify-content-start align-items-center product-name">
                        <div class="avatar-wrapper">
                          <div class="avatar avatar me-2 me-sm-4 rounded-2 bg-label-secondary">
                           <img src="{{ $product->image ? asset($product->image) : asset('assets/img/default-product.png') }}" alt="Product-9" class="rounded">

                          </div>
                        </div>
                        <div class="d-flex flex-column">
                          <h6 class="text-nowrap mb-0">{{ ucwords($product->name) }}</h6>
                          <small class="text-truncate d-none d-sm-block">{{ $product->subheading }}</small>
                        </div>
                      </div>
                    </td>
                    <td class="align-middle text-center">{{ ucwords($product->sku) }}</td>
                    {{-- <td class="align-middle">
                      <div class="order-calculations">
                        <div class="d-flex justify-content-start gap-4 mb-2">
                          <span class="w-px-100 text-heading">MRP</span>
                          <h6 class="mb-0">{{env('APP_CURRENCY')}}{{ number_format($product->mrp) }}</h6>
                        </div>
                        <div class="d-flex justify-content-start gap-4 mb-2">
                          <span class="w-px-100 text-heading">Offer Price</span>
                          <h6 class="mb-0">{{env('APP_CURRENCY')}}{{ number_format($product->offer_price)}}</h6>
                        </div>
                      </div>
                    </td> --}}
                    <td class="align-middle text-center">
                      <span class="text-truncate d-flex align-items-center text-heading">
                        <span class="w-px-30 h-px-30 rounded-circle d-flex justify-content-center align-items-center bg-label-info me-4"><i class="icon-base ri ri-home-6-line icon-18px"></i></span>{{$product->brand?$product->brand->name:"N/A"}}
                      </span>
                    </td>
                    <td class="align-middle text-center">
                      <div class="form-check form-switch">
                        <input class="form-check-input ms-auto" type="checkbox"
                          id="flexSwitchCheckDefault{{ $product->id }}"
                          wire:click="toggleStatus({{ $product->id }})"
                          @if($product->status) checked @endif>
                      </div>
                    </td>
                    <td class="align-middle text-end px-4">
                      <a href="{{route('admin.product.edit',$product->id)}}"
                        class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect"
                        title="Edit">
                        <i class="ri-edit-box-line ri-20px text-info"></i>
                      </a>
                      <button wire:click="confirmDelete({{ $product->id }})"
                        class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect"
                        title="Delete">
                        <i class="ri-delete-bin-7-line ri-20px text-danger"></i>
                      </button>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="text-center py-4">No products found.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>

            {{-- Pagination --}}
            <div class="d-flex justify-content-end mt-2">
              {{ $products->links() }}
            </div>
          </div>
        </div>
      </div>
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
                title: "Delete Product?",
                text: "Are you sure you want to delete the product?",
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
