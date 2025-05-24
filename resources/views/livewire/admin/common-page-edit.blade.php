<div>
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-body px-0 pb-2 mx-4">
                    <div class="d-flex justify-content-between mb-3">
                        <h5>Update {{ucwords($pageName)}}</h5>
                        <a href="{{route('admin.common_pages.index')}}"
                            class="btn btn-dark btn-sm waves-effect waves-light">
                            <i class="ri-arrow-go-back-line"></i> Back </a>
                    </div>
                    <form wire:submit.prevent="update">
                        <div class="row">
                            <div class="form-floating form-floating-outline mb-5" wire:ignore>
                                <textarea id="content" class="form-control border border-2 p-2" rows="6"
                                    placeholder="Enter Content"></textarea>

                            </div>
                            @error('content')
                            <p class="text-danger inputerror">{{ $message }}</p>
                            @enderror
                            <div class="mb-2 mt-4">
                                <button type="submit"
                                    class="btn btn-secondary btn-sm add-new btn-primary waves-effect waves-light">
                                    Update
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- script for the ckeditor --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/38.1.0/classic/ckeditor.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
        ClassicEditor
            .create(document.querySelector('#content'))
            .then(editor => {
                // Set initial value from Livewire
                editor.setData(@json($content ?? ''));

                // Sync editor content with Livewire
                editor.model.document.on('change:data', () => {
                    @this.set('content', editor.getData() || '');
                });
            })
            .catch(error => {
                console.error(error);
            });
    });
    </script>

</div>