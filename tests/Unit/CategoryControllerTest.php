<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_can_be_created()
    {
        $categoryData = ['name' => 'Test Category'];

        $response = $this->postJson('/api/categories', $categoryData);

        $response->assertStatus(201)
            ->assertJsonFragment($categoryData);

        $this->assertDatabaseHas('categories', $categoryData);
    }

    public function test_category_can_have_products()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->assertTrue($category->products->contains($product));
    }

    public function test_category_can_have_subcategories()
    {
        $parentCategory = Category::factory()->create(['name' => 'Parent Category']);
        $subcategory = Category::factory()->create(['parent_id' => $parentCategory->id]);

        $this->assertEquals($parentCategory->id, $subcategory->parent_id);
        $this->assertTrue($parentCategory->subcategories->contains($subcategory));
    }

    public function test_category_name_must_be_unique()
    {
        Category::factory()->create(['name' => 'Unique Category']);

        $response = $this->postJson('/api/categories', ['name' => 'Unique Category']);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    public function test_deleting_category_deletes_associated_products()
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id]);

        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('products', ['category_id' => $category->id]);
    }

    public function test_category_can_have_parent()
    {
        $parentCategory = Category::factory()->create(['name' => 'Parent Category']);
        $childCategory = Category::factory()->create(['parent_id' => $parentCategory->id]);

        $this->assertEquals($parentCategory->id, $childCategory->parent_id);
    }

    public function test_only_fillable_attributes_are_mass_assignable()
    {
        $categoryData = [
            'name' => 'Test Category',
            'extra_field' => 'Not Allowed' // This should not be mass-assignable
        ];

        $category = Category::create($categoryData);

        $this->assertEquals('Test Category', $category->name);
        $this->assertNull($category->extra_field ?? null);
    }

    public function test_category_has_default_values()
    {
        $category = Category::factory()->create();

        $this->assertEquals('default_value', $category->default_field); // O'zingizning default qiymatingizni qo'shing
    }

    public function test_category_soft_deletes()
    {
        $category = Category::factory()->create();

        $category->delete();

        $this->assertSoftDeleted('categories', ['id' => $category->id]);
    }
}
