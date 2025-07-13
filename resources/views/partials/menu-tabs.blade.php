<ul class="nav nav-tabs mb-2">
    <li class="nav-item">
        <a class="nav-link {{ Route::currentRouteName() === 'menus.show' ? 'active' : '' }}"
           href="{{ route('menus.show', $menu->id) }}">Overview</a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ Route::currentRouteName() === 'menus.categories.index' ||
         Str::startsWith(Route::currentRouteName(), 'categories.') ? 'active' : '' }}"
           href="{{ route('menus.categories.index', $menu->id) }}">Categories</a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ Route::currentRouteName() === 'menus.items.index' ||
         Str::startsWith(Route::currentRouteName(), 'items.') ? 'active' : '' }}"
           href="{{ route('menus.items.index', $menu->id) }}">Items</a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ Route::currentRouteName() === 'menus.modifier-groups.index' ||
         Str::startsWith(Route::currentRouteName(), 'menus.modifier-groups.') ? 'active' : '' }}"
           href="{{ route('menus.modifier-groups.index', $menu->id) }}">Modifier Groups</a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ Route::currentRouteName() === 'menus.modifiers.index' ||
         Str::startsWith(Route::currentRouteName(), 'modifiers.') ? 'active' : '' }}"
           href="{{ route('menus.modifiers.index', $menu->id),  }}">Modifier</a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ Route::currentRouteName() === 'menus.working-hours.index' ||
         Str::startsWith(Route::currentRouteName(), 'working-hours.') ? 'active' : '' }}"
           href="{{ route('menus.working-hours.index', $menu->id),  }}">Working Hours</a>
    </li>
</ul>
