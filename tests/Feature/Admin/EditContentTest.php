<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Role;
use App\ContentType;
use App\Content;
use App\User;
use App\Revision;

class EditContentTest extends TestCase
{
    use RefreshDatabase;

    private $adminUser;
    private $blogPostContentType;

    public function setUp()
    {
        parent::setUp();

        $this->adminUser = factory(User::class)->create();
    }

    /** @test */
    public function admins_can_edit_content() {
        $this->actingAs($this->adminUser);

        $data = ['title' => 'fake content title', 'body' => 'fake content body'];
        $content = Content::create($data);

        $request = $this->put("/admin/content/{$content->id}", ['title' => 'new_title', 'body' => 'new_body']);

        $request->assertRedirect("/admin/content/{$content->id}/edit");
        $this->assertCount(1, Content::all());
        $this->assertDatabaseHas('contents', ['title' => 'new_title', 'body' => 'new_body']);

        $this->assertDatabaseHas('revisions', [
            'user_id' => $this->adminUser->id,
            'content_id' => $content->id,
            'before' => collect(['title' => 'fake content title', 'body' => 'fake content body'])->toJson(),
            'after' => collect(['title' => 'new_title', 'body' => 'new_body'])->toJson()
        ]);
    }
}