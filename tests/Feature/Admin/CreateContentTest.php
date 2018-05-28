<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Role;
use App\ContentType;
use App\Content;
use App\User;

class CreateContentTest extends TestCase
{
    use RefreshDatabase;

    private $adminUser;
    private $blogPostContentType;

    public function setUp()
    {
        parent::setUp();

        $this->adminUser = factory(User::class)->create(['role_id' => Role::create(['role' => 'admin'])->id]);
        $this->blogPostContentType = ContentType::create(['name' => 'blog post', 'user_id' => $this->adminUser->id]);
    }

    /** @test */
    public function admins_can_create_content() {
        $this->actingAs($this->adminUser);

        $data = ['title' => 'fake content title', 'body' => 'fake content body', 'content_type_id' => $this->blogPostContentType->id];
        $request = $this->post('/admin/content', $data);
        
        $this->assertCount(1, Content::all());
        $this->assertDatabaseHas('contents', $data);
        $content = Content::first();
        $request->assertRedirect("/admin/content/{$content->id}/edit");
    }

    /** @test */
    public function must_be_an_admin() {
        $regularUser = factory(User::class)->create(['role_id' => Role::create(['role' => 'guest'])->id]);
        $this->actingAs($regularUser);

        $data = ['title' => 'fake content title', 'body' => 'fake content body', 'content_type_id' => $this->blogPostContentType->id];
        $request = $this->post('/admin/content', $data);

        $request->assertStatus(401);
        $this->assertCount(0, Content::all());
    }
}