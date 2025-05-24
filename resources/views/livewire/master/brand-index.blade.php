<div>
  <div class="row mb-4">
    {{-- Brands Table Section --}}
    <div class="col-lg-8 col-md-6 mb-md-0 mb-4">
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
            <div class="col-lg-6 col-7">
              <h6>Brands</h6>
            </div>
            <div class="col-lg-6 col-5 my-auto text-end">
              <div class="ms-md-auto d-flex align-items-center">
                <input type="text" wire:model.debounce.500ms="search"
                  class="form-control border border-2 p-2 custom-input-sm"
                  placeholder="Enter brand name" wire:keyup="UpdateSearch($event.target.value)">
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
                    Name
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
                @forelse($brands as $k => $brand_item)
                  <tr>
                    <td class="align-middle text-center">{{ $k + 1 }}</td>
                    <td class="align-middle text-center">{{ ucwords($brand_item->name) }}</td>
                    <td class="align-middle text-center">
                      <div class="form-check form-switch">
                        <input class="form-check-input ms-auto" type="checkbox"
                          id="flexSwitchCheckDefault{{ $brand_item->id }}"
                          wire:click="toggleStatus({{ $brand_item->id }})"
                          @if($brand_item->status) checked @endif>
                      </div>
                    </td>
                    <td class="align-middle text-end px-4">
                      <button wire:click="edit({{ $brand_item->id }})"
                        class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect"
                        title="Edit">
                        <i class="ri-edit-box-line ri-20px text-info"></i>
                      </button>
                      <button wire:click="confirmDelete({{ $brand_item->id }})"
                        class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect"
                        title="Delete">
                        <i class="ri-delete-bin-7-line ri-20px text-danger"></i>
                      </button>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center py-4">No brands found.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>

            {{-- Pagination --}}
            <div class="d-flex justify-content-end mt-2">
              {{ $brands->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Brand Form Section --}}
    <div class="col-lg-4 col-md-6 mb-md-0 mb-4">
      <div class="card my-4">
        <div class="card-body px-0 pb-2 mx-4">
          <div class="d-flex justify-content-between mb-3">
            <h5>{{ $brandId ? 'Update Brand' : 'Create Brand' }}</h5>
          </div>

          <form wire:submit.prevent="save">
            <div class="row">
              {{-- Brand Name --}}
              <div class="form-floating form-floating-outline mb-4">
                <input type="text" wire:model="name"
                  class="form-control border border-2 p-2"
                  placeholder="Enter Brand Name">
                <label>Name <span class="text-danger">*</span></label>
                @error('name')
                  <p class="text-danger inputerror">{{ $message }}</p>
                @enderror
              </div>

              {{-- Action Buttons --}}
              <div class="mb-2 text-end mt-4">
                <button type="submit"
                  class="btn btn-secondary btn-sm add-new btn-primary waves-effect waves-light">
                  {{ $brandId ? 'Update Brand' : 'Create Brand' }}
                </button>
              </div>
            </div>
          </form>
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
