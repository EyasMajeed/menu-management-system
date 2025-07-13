{{-- This is a conceptual example. You will replace the relevant section in your actual menus/show.blade.php --}}
{{-- The current menu name will likely be displayed at the top, as in your screenshot --}}
<div class="container mt-5">
    <h1 class="mb-4">{{ $menu->name['en'] ?? $menu->name ?? 'Menu Details' }}</h1>

    {{-- Tabbed Navigation Bar --}}
    <ul class="nav nav-pills mb-3" id="menuTabs" role="tablist">
        {{-- Overview Tab (Example, assuming you have an overview section) --}}
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ Request::routeIs('menus.show') ? 'active bg-dark text-white' : 'text-dark' }}" 
               id="overview-tab" 
               href="{{ route('menus.show', $menu->id) }}" 
               role="tab" 
               aria-controls="overview" 
               aria-selected="{{ Request::routeIs('menus.show') ? 'true' : 'false' }}">
                Overview
            </a>
        </li>

        {{-- Categories Tab --}}
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ Request::routeIs('menus.categories.index') ? 'active bg-dark text-white' : 'text-dark' }}" 
               id="categories-tab" 
               href="{{ route('menus.categories.index', $menu->id) }}" 
               role="tab" 
               aria-controls="categories" 
               aria-selected="{{ Request::routeIs('menus.categories.index') ? 'true' : 'false' }}">
                Categories
            </a>
        </li>

        {{-- Items Tab --}}
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ Request::routeIs('menus.items.index') ? 'active bg-dark text-white' : 'text-dark' }}" 
               id="items-tab" 
               href="{{ route('menus.items.index', $menu->id) }}" 
               role="tab" 
               aria-controls="items" 
               aria-selected="{{ Request::routeIs('menus.items.index') ? 'true' : 'false' }}">
                Items
            </a>
        </li>

        {{-- Modifier Groups Tab --}}
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ Request::routeIs('menus.modifier-groups.index') ? 'active bg-dark text-white' : 'text-dark' }}" 
               id="modifier-groups-tab" 
               href="{{ route('menus.modifier-groups.index', $menu->id) }}" 
               role="tab" 
               aria-controls="modifier-groups" 
               aria-selected="{{ Request::routeIs('menus.modifier-groups.index') ? 'true' : 'false' }}">
                Modifier groups
            </a>
        </li>

        {{-- Modifiers Tab --}}
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ Request::routeIs('menus.modifiers.index') ? 'active bg-dark text-white' : 'text-dark' }}" 
               id="modifiers-tab" 
               href="{{ route('menus.modifiers.index', $menu->id) }}" 
               role="tab" 
               aria-controls="modifiers" 
               aria-selected="{{ Request::routeIs('menus.modifiers.index') ? 'true' : 'false' }}">
                Modifiers
            </a>
        </li>

        {{-- Working Hours Tab --}}
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ Request::routeIs('menus.working-hours.index') ? 'active bg-dark text-white' : 'text-dark' }}" 
               id="working-hours-tab" 
               href="{{ route('menus.working-hours.index', $menu->id) }}" 
               role="tab" 
               aria-controls="working-hours" 
               aria-selected="{{ Request::routeIs('menus.working-hours.index') ? 'true' : 'false' }}">
                Working hours
            </a>
        </li>
    </ul>

</div>
