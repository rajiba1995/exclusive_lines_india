<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Store Locations</h4>

        <a href="{{ route('admin.store_location.create') }}" class="btn btn-primary waves-effect waves-light">
            + Add Store Location
        </a>
    </div>
    <div class="d-flex mb-4">
        <button class="btn {{ $activeTab === 'Multi Brand Outlets' ? 'btn-primary' : 'btn-outline-primary' }} me-2"
                wire:click="setTab('Multi Brand Outlets')">
            Multi Brand Outlets
        </button>
        <button class="btn {{ $activeTab === 'Exclusive Brand Outlets' ? 'btn-primary' : 'btn-outline-primary' }}"
                wire:click="setTab('Exclusive Brand Outlets')">
            Exclusive Brand Outlets
        </button>
    </div>

    <div class="row">
        @forelse($storeLocations as $store)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="fw-bold">{{ strtoupper($store->name) }}</h5>
                            {{-- Edit Button --}}
                            <a href="{{route('admin.store_location.edit',$store->id)}}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                        </div>
                        <p>{{ $store->address }}</p>

                        @php
                            $phones = json_decode($store->contact_numbers);
                            $times = json_decode($store->operating_time, true);
                            $brands = explode(',', $store->brands ?? '');
                        @endphp

                        @if($phones)
                            <p><i class="fas fa-phone-alt me-1"></i> 
                                {{ implode(', ', $phones) }}
                            </p>
                        @endif

                        @if($times)
                            @foreach($times as $entry)
                                <p><i class="fas fa-clock me-1"></i> 
                                    {{ $entry['time'] ?? '' }}
                                </p>
                            @endforeach
                        @endif

                        @if($brands)
                            <p><strong>Brands:</strong> {{ implode(', ', $brands) }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p>No outlets found.</p>
        @endforelse
    </div>
</div>
