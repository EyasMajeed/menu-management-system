@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Working Hours & Location for {{ $branch->name['en'] ?? $branch->name ?? 'N/A' }}</h5>
            <a href="{{ route('menus.working-hours.index', $menu->id) }}" class="btn btn-sm btn-light">← Back to Working Hours</a>
        </div>

        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>There were some problems with your input:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            

            <form method="POST" action="{{ route('menus.branches.working-hours.update', ['menu' => $menu->id, 'branch' => $branch->id]) }}">
                @csrf
                @method('PUT')

                {{-- Working Hours Section --}}
                <h5 class="mb-3">Set Daily Hours</h5>
                @php
                    // Generate time options for dropdowns (every 30 minutes)
                    $timeOptions = [];
                    for ($i = 0; $i < 24; $i++) {
                        for ($j = 0; $j < 60; $j += 30) {
                            $time = sprintf('%02d:%02d', $i, $j);
                            $timeOptions[$time] = date('h:i A', strtotime($time)); // Format for display
                        }
                    }
                @endphp
                
                @foreach($daysOfWeek as $day)
                    @php
                        $wh = $workingHours->get($day); 
                        $isClosed = old("{$day}_is_closed") !== null ? true : ($wh ? (bool)$wh['is_closed'] : false);
                        $openingTime = old("{$day}_opening_time") ?? ($wh ? \Illuminate\Support\Str::substr($wh['opening_time'], 0, 5) : null);
                        $closingTime = old("{$day}_closing_time") ?? ($wh ? \Illuminate\Support\Str::substr($wh['closing_time'], 0, 5) : null);
                    @endphp

                    <div class="row mb-3 align-items-center border-bottom pb-3 pt-2">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">{{ $day }}</label>
                        </div>
                        <div class="col-md-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="{{ $day }}_is_closed" name="{{ $day }}_is_closed" value="1" {{ $isClosed ? 'checked' : '' }} onchange="toggleTimeInputs(this, '{{ $day }}')">
                                <label class="form-check-label" for="{{ $day }}_is_closed">
                                    Closed
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="{{ $day }}_opening_time" class="form-label visually-hidden">Opening Time</label>
                            <select class="form-select" id="{{ $day }}_opening_time" name="{{ $day }}_opening_time" {{ $isClosed ? 'disabled' : '' }}>
                                <option value="">-- Select Time --</option>
                                @foreach($timeOptions as $value => $label)
                                    <option value="{{ $value }}" {{ $openingTime == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error("{$day}_opening_time") <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3 d-flex align-items-center">
                            <label for="{{ $day }}_closing_time" class="form-label visually-hidden">Closing Time</label>
                            <select class="form-select me-2" id="{{ $day }}_closing_time" name="{{ $day }}_closing_time" {{ $isClosed ? 'disabled' : '' }}>
                                <option value="">-- Select Time --</option>
                                @foreach($timeOptions as $value => $label)
                                    <option value="{{ $value }}" {{ $closingTime == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error("{$day}_closing_time") <div class="text-danger small mt-1">{{ $message }}</div> @enderror

                            {{-- Copy to all button/dropdown --}}
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton-{{ $day }}" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton-{{ $day }}">
                                    <li><a class="dropdown-item copy-to-all-btn" href="#" data-day="{{ $day }}">Copy to all</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach


                {{-- Map Location Section --}}
                <h5 class="mt-4 mb-3">Set Branch Location</h5>
                <div class="mb-3">
                    <label for="latitude" class="form-label">Latitude</label>
                    <input type="text" class="form-control" id="latitude" name="latitude" value="{{ old('latitude', $branch->latitude) }}" readonly>
                    @error('latitude') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label for="longitude" class="form-label">Longitude</label>
                    <input type="text" class="form-control" id="longitude" name="longitude" value="{{ old('longitude', $branch->longitude) }}" readonly>
                    @error('longitude') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                {{-- Map Container --}}
                <div id="map" style="height: 400px; width: 100%; border-radius: 0.5rem; position: relative; z-index: 1; background-color: #f0f0f0;">
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1000; pointer-events: none;">
                        <i class="bi bi-geo-alt-fill text-danger" style="font-size: 2rem;"></i>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-dark mt-4">Update Working Hours & Location</button>
            </form>
        </div>
    </div>
</div>

{{-- Leaflet CSS and JS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    // JavaScript for time inputs
    function toggleTimeInputs(checkbox, day) {
        const openingTimeSelect = document.getElementById(`${day}_opening_time`);
        const closingTimeSelect = document.getElementById(`${day}_closing_time`);

        if (checkbox.checked) {
            openingTimeSelect.disabled = true;
            closingTimeSelect.disabled = true;
            openingTimeSelect.value = ''; 
            closingTimeSelect.value = ''; 
        } else {
            openingTimeSelect.disabled = false;
            closingTimeSelect.disabled = false;
        }
        console.log(`[toggleTimeInputs] For ${day}: Closed=${checkbox.checked}, Inputs Disabled=${openingTimeSelect.disabled}`);
    }

    // Initialize on page load for existing values and map
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize toggle states for time inputs
        @foreach($daysOfWeek as $day)
            if (document.getElementById('{{ $day }}_is_closed')) {
                const checkbox = document.getElementById('{{ $day }}_is_closed');
                toggleTimeInputs(checkbox, '{{ $day }}');
            }
        @endforeach

        // Delegated event listener for "Copy to all" functionality
        document.addEventListener('click', function(event) {
            const copyButton = event.target.closest('.copy-to-all-btn');
            if (copyButton) {
                event.preventDefault(); 
                event.stopPropagation(); 

                const sourceDay = copyButton.dataset.day; 

                const sourceOpeningTimeSelect = document.getElementById(`${sourceDay}_opening_time`);
                const sourceClosingTimeSelect = document.getElementById(`${sourceDay}_closing_time`);
                const sourceIsClosedCheckbox = document.getElementById(`${sourceDay}_is_closed`);

                console.log(`--- [Copy to all] Initiated from ${sourceDay} ---`);

                if (!sourceOpeningTimeSelect || !sourceClosingTimeSelect || !sourceIsClosedCheckbox) {
                    console.error(`[Copy to all] Error: Could not find all elements for source day ${sourceDay}. Aborting copy operation.`);
                    return; 
                }

                const copiedOpeningTime = sourceOpeningTimeSelect.value;
                const copiedClosingTime = sourceClosingTimeSelect.value;
                const copiedIsClosed = sourceIsClosedCheckbox.checked;

                console.log(`[Copy to all] Copied Opening Time: "${copiedOpeningTime}"`);
                console.log(`[Copy to all] Copied Closing Time: "${copiedClosingTime}"`);
                console.log(`[Copy to all] Source Day Closed: ${copiedIsClosed}`);

                const allDays = @json($daysOfWeek); // Ensure daysOfWeek is available in JS

                allDays.forEach(function(day) {
                    if (day === sourceDay) {
                        console.log(`[Copy to all] Skipping source day: ${day}`);
                        return;
                    }

                    console.log(`[Copy to all] Attempting to update target day: ${day}`);
                    const targetOpeningTimeSelect = document.getElementById(`${day}_opening_time`);
                    const targetClosingTimeSelect = document.getElementById(`${day}_closing_time`);
                    const targetIsClosedCheckbox = document.getElementById(`${day}_is_closed`);

                    if (!targetOpeningTimeSelect || !targetClosingTimeSelect || !targetIsClosedCheckbox) {
                        console.error(`[Copy to all] Error: Could not find all elements for target day ${day}. Skipping this day.`);
                        return;
                    }

                    // Set the closed state for the target day
                    targetIsClosedCheckbox.checked = copiedIsClosed;
                    console.log(`[Copy to all] Set ${day} closed state to: ${targetIsClosedCheckbox.checked}`);

                    // Call toggleTimeInputs to enable/disable and clear/restore values based on the new state
                    toggleTimeInputs(targetIsClosedCheckbox, day); 

                    // Only set times if the source day was not closed AND valid times were copied
                    if (!copiedIsClosed && copiedOpeningTime && copiedClosingTime) {
                        targetOpeningTimeSelect.value = copiedOpeningTime;
                        targetClosingTimeSelect.value = copiedClosingTime;
                        console.log(`[Copy to all] Set ${day} times to: ${targetOpeningTimeSelect.value} - ${targetClosingTimeSelect.value}`);
                    } else {
                        console.log(`[Copy to all] ${day} is now closed or source times were empty/invalid, times cleared/not set.`);
                        // Ensure values are cleared if the target day becomes closed or source was invalid
                        targetOpeningTimeSelect.value = '';
                        targetClosingTimeSelect.value = '';
                    }
                });
                console.log('--- [Copy to all] Operation finished ---');
            }
        });


        // Initialize the interactive map (keeping this section EXACTLY as provided by user)
        var initialLat = {{ $branch->latitude ?? 24.7136 }}; // Default to Riyadh if not set
        var initialLng = {{ $branch->longitude ?? 46.6877 }}; // Default to Riyadh if not set
        var mapElement = document.getElementById('map');

        // Debugging: Check if map element exists and Leaflet is loaded
        console.log('Map element:', mapElement);
        console.log('Leaflet defined:', typeof L !== 'undefined');

        if (mapElement && typeof L !== 'undefined') {
            var map = L.map(mapElement).setView([initialLat, initialLng], 13); // Zoom level 13

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            var latInput = document.getElementById('latitude');
            var lngInput = document.getElementById('longitude');

            // Update inputs when map center changes
            map.on('move', function() {
                var center = map.getCenter();
                latInput.value = center.lat.toFixed(7);
                lngInput.value = center.lng.toFixed(7);
            });

            // Set initial values on map load
            latInput.value = initialLat.toFixed(7);
            lngInput.value = initialLng.toFixed(7);

            // Invalidate map size after it's fully rendered or container is visible
            // This is crucial if the map div is hidden initially or its size changes dynamically
            setTimeout(function() {
                map.invalidateSize();
            }, 100); // A small delay might be necessary
        } else {
            console.error('Map element not found or Leaflet not loaded.');
        }
    });
</script>
@endsection
