<div>
    <div class="row mb-4">
        <div class="col-lg-12 col-md-6 mb-md-0 mb-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header pb-2">
                            <div class="row">
                                @if(session()->has('message'))
                                <div class="alert alert-success" id="flashMessage">
                                    {{ session('message') }}
                                </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-7">
                                    <h6>Page List</h6>
                                </div>
                                <div class="col-lg-6 col-5 my-auto text-end">
                                    <div class="ms-md-auto d-flex align-items-center">
                                        <input type="text" wire:model.live="search"
                                            class="form-control border border-2 p-2 custom-input-sm"
                                            placeholder="Enter Title">
                                       
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 align-middle">
                                                SL
                                            </th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 align-middle">
                                                Page
                                            </th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 align-middle">
                                                Status
                                            </th>
                                            <th
                                                class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 align-middle px-4">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($common_pages as $k => $common_page)
                                        <tr>
                                            <td class="align-middle text-center">{{ $k + 1 }}</td>

                                            {{-- Display Banner Title --}}
                                            <td class="align-middle text-center">{{
                                                strtoupper($common_page->page_heading) }}</td>

                                            {{-- Toggle Status --}}
                                            <td class="align-middle text-center">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input ms-auto" type="checkbox"
                                                        id="flexSwitchCheckDefault{{ $common_page->id }}"
                                                        wire:click="toggleStatus({{ $common_page->id }})"
                                                        @if($common_page->status) checked @endif>
                                                </div>
                                            </td>

                                            {{-- Action Buttons --}}
                                            <td class="align-middle text-end px-4">
                                                <a href="{{route('admin.common_pages.edit',$common_page->id)}}"
                                                    class="btn btn-sm btn-icon edit-record btn-text-secondary rounded-pill waves-effect btn-sm"
                                                    title="Edit">
                                                    <i class="ri-edit-box-line ri-20px text-info"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                No Page Content Available
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="loader-container" wire:loading>
        <div class="loader"></div>
    </div>
</div>