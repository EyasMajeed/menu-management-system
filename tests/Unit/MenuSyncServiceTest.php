<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\MenuSyncService;
use App\Models\Menu;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Item;
use App\Models\ModifierGroup;
use App\Models\Modifier;
use App\Models\BranchWorkingHour;


class MenuSyncServiceTest extends TestCase
{
    // use RefreshDatabase;

    public function test_menu_sync_service_successful_response()
    {
        // Mock the HTTP response
        Http::fake([
            config('services.delivery_app.base_url') . '/api/mock-menu-sync' => Http::response([
                'status' => 'success',
                'data' => []
            ], 200),
        ]);

        

        // Create Branch
        $branch = new Branch();
        $branch->name = 'Test Branch';
        $branch->brand_id = 1;
        $branch->save();


        // Add working hours
        foreach (['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day) {
            BranchWorkingHour::create([
                'branch_id' => $branch->id,
                'day_of_week' => $day,
                'opening_time' => '08:00',
                'closing_time' => '17:00',
                'is_closed' => false,
            ]);
        }

        // Create Menu
        $menu = new Menu();
        $menu->name = 'Lunch Menu';
        $menu->brand_id = 1;
        $menu->save();
        $menu->branches()->sync([$branch->id]);
   


        // Create Category
        $category = new Category();
        $category->name = ['en' => 'Main Dishes', 'ar' => 'الأطباق الرئيسية'];
        $category->menu_id = $menu->id;
        $category->save();

        // Create Item
        $item = new Item();
        $item->name = ['en' => 'Burger', 'ar' => 'برجر'];
        $item->price = 9.99;
        $item->status = 'Active';
        $item->menu_id = $menu->id;
        $item->save();

        // Attach item to category 
        $item->categories()->attach($category->id);



        // Create ModifierGroup
        $modifierGroup = new ModifierGroup();
        $modifierGroup->name = ['en' => 'Sauces', 'ar' => 'صلصات'];
        $modifierGroup->menu_id = $menu->id; 
        $modifierGroup->type = 'required'; 
        $modifierGroup->save();
        $modifierGroup->items()->attach($item->id);

        // Create Modifier
        $modifier = new Modifier();
        $modifier->menu_id = $menu->id;

        $modifier->name = ['en' => 'Burger', 'ar' => 'برجر'];
        $modifier->price = 0.50;
        $modifier->modifier_group_id = $modifierGroup->id;
        $modifier->save();
        $modifierGroup->type = 'required'; 

        // Call the service
        $service = new MenuSyncService();
        $result = $service->sync($menu->fresh(), $branch->fresh());
        

        // Assertions
        $this->assertEquals('success', $result['status']);
        $this->assertArrayHasKey('message', $result);

        Http::assertSent(fn($request) =>
            $request->url() === config('services.delivery_app.base_url') . '/api/mock-menu-sync' &&
            $request->method() === 'POST'
        );

        


    }
}
