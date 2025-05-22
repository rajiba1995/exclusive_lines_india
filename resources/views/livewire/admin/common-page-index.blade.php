<div>
    <div class="row mb-4">
        <div class="col-lg-8 col-md-6 mb-md-0 mb-4">
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
                                        {{-- <button type="button" wire:target="search"
                                            class="btn btn-dark text-white mb-0 custom-input-sm">
                                            <span class="material-icons">search</span>
                                        </button> --}}
                                        <!-- Optionally, add a search icon button -->
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
                                                <button wire:click="edit({{ $common_page->id }})"
                                                    class="btn btn-sm btn-icon edit-record btn-text-secondary rounded-pill waves-effect btn-sm"
                                                    title="Edit">
                                                    <i class="ri-edit-box-line ri-20px text-info"></i>
                                                </button>
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



        <div class="col-lg-4 col-md-6 mb-md-0 mb-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-body px-0 pb-2 mx-4">
                            <div class="d-flex justify-content-between mb-3">
                                <h5>Update Page Content</h5>
                            </div>
                            <form wire:submit.prevent="update">
                                <div class="row">

                                    <div class="form-floating form-floating-outline mb-5 fv-plugins-icon-container">
                                        <input type="text" wire:model="title" class="form-control border border-2 p-2"
                                            placeholder="Enter Page Title">
                                        <label> Title <span class="text-danger">*</span></label>
                                    </div>
                                    @error('title')
                                    <p class='text-danger inputerror'>{{ $message }}</p>
                                    @enderror

                                    <div class="form-floating form-floating-outline mb-5" wire:ignore>
                                        <textarea id="content" wire:model="content"
                                            class="form-control border border-2 p-2" rows="6"
                                            placeholder="Enter Content"></textarea>
                                    </div>
                                    @error('content')
                                    <p class="text-danger inputerror">{{ $message }}</p>
                                    @enderror

                                    <div class="mb-2 text-end mt-4">
                                        <a href="{{route('admin.common_pages.index')}}"
                                            class="btn btn-danger text-white mb-0 custom-input-sm ms-2">
                                            <i class="ri-restart-line"></i>
                                        </a>
                                        <button type="submit"
                                            class="btn btn-secondary btn-sm add-new btn-primary waves-effect waves-light">
                                            <span>Update</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="loader-container" wire:loading>
        <div class="loader"></div>
    </div>
    <script src="https://cdn.ckeditor.com/ckeditor5/38.1.0/classic/ckeditor.js"></script>
    <script>
        let ckEditorInstance;

        document.addEventListener('DOMContentLoaded', function () {
                
                ClassicEditor
                    .create(document.querySelector('#content'))
                    .then(editor => {
                        ckEditorInstance = editor;
                        editor.model.document.on('change:data', () => {
                            @this.set('content', editor.getData());
                        });
                    })
                    .catch(error => {
                        console.error(error);
                    });
                });
                window.addEventListener('reset-ckeditor', function () {
        if (ckEditorInstance) {
            ckEditorInstance.setData('');
        }
    });
            
    </script>
</div>