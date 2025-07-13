@extends('layouts.app')

@section('content')
<div class="container mt-5">
    @include('partials.menu-tabs', ['menu' => $menu])

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mt-4">
        @forelse($branches as $branch)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow h-100 d-flex flex-column"> {{-- Added d-flex flex-column for consistent height --}}
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $branch->name['en'] ?? $branch->name ?? 'N/A' }}</h5>
                        <a href="{{ route('menus.branches.working-hours.edit', ['menu' => $menu->id, 'branch' => $branch->id]) }}"
                           class="btn btn-sm btn-light rounded-circle p-0"
                           style="width: 30px; height: 30px; display: flex; justify-content: center; align-items: center;"
                           title="Edit Working Hours & Location">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </div>
                    <div class="card-body flex-grow-1"> {{-- flex-grow-1 to make card body take available space --}}
                        <ul class="list-group list-group-flush mb-3">
                            @foreach($daysOfWeek as $day)
                                @php
                                    $wh = $branch->workingHours->where('day_of_week', $day)->first();
                                @endphp
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    <strong>{{ $day }}</strong>
                                    <span>
                                        @if($wh && $wh->is_closed)
                                            <span class="badge bg-danger">Closed</span>
                                        @elseif($wh && $wh->opening_time && $wh->closing_time)
                                            {{ \Carbon\Carbon::parse($wh->opening_time)->format('h:i A') }} - 
                                            {{ \Carbon\Carbon::parse($wh->closing_time)->format('h:i A') }}
                                            @if(\Carbon\Carbon::parse($wh->closing_time)->lessThan(\Carbon\Carbon::parse($wh->opening_time)))
                                                (Next Day)
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    {{-- Map Section --}}
                    <div class="card-footer bg-white pt-0 pb-0"> {{-- Added pt-0 pb-0 for tight fit --}}
                        @if($branch->latitude && $branch->longitude)
                            <div id="map-{{ $branch->id }}" class="leaflet-map my-3" style="height: 200px; width: 100%; border-radius: 0.5rem;"></div>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    var mapId = 'map-{{ $branch->id }}';
                                    var latitude = {{ $branch->latitude }};
                                    var longitude = {{ $branch->longitude }};
                                    var zoom = 13; // Default zoom level for static maps

                                    var mapElement = document.getElementById(mapId);

                                    // Check if the map container exists and has not been initialized
                                   if (mapElement && !mapElement._leaflet_id) { // Check if map container exists and not already initialized
                                        var map = L.map(mapId).setView([latitude, longitude], zoom);

                                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                                        }).addTo(map);

                                        var marker = L.marker([latitude, longitude]).addTo(map)
                                            .bindPopup('<b>{{ $branch->name['en'] ?? $branch->name ?? 'N/A' }}</b><br>Location.')
                                            .openPopup();

                                        // Re-enable scrollWheelZoom and doubleClickZoom
                                        map.scrollWheelZoom.enable();
                                        map.doubleClickZoom.enable();

                                        // Disable other interactions (dragging, touch zoom, etc.) if still desired for a "static-ish" map
                                        map.dragging.disable();
                                        map.touchZoom.disable();
                                        map.boxZoom.disable();
                                        map.keyboard.disable();
                                        if (map.tap) map.tap.disable();
                                        // mapElement.style.cursor = 'default'; // Keep default cursor unless dragging is enabled

                                        // On zoom end, re-center the map on the marker
                                        map.on('zoomend', function() {
                                            map.setView(marker.getLatLng(), map.getZoom());
                                        });

                                        // Invalidate map size after it's fully rendered or container is visible
                                        setTimeout(function() {
                                            map.invalidateSize();
                                        }, 100);
                                    }
                                });
                            </script>
                        @else
                            <div class="text-muted text-center py-3">Location not set.</div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center" role="alert">
                    No branches found for this menu or no working hours set yet.
                </div>
            </div>
        @endforelse
    </div>
</div>

{{-- Leaflet CSS and JS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
@endsection
