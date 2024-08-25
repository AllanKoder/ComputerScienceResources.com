<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Resource;
use App\Models\ResourceEdit;
use App\Models\ResourceReview;
use App\Models\Comment;
use App\Models\User;
use Database\Seeders\ResourceSeeder;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_see_comment_for_all_types_of_posts() {
        $this->seed();

        $resource = Resource::first();
        $response = $this->get(route('comment.comments', ['id' => $resource->id, 'type'=>'resource']));
        $response->assertStatus(200);
        $response->assertDontSeeText('no comments');

        $resourceEdit = ResourceEdit::first();
        $response = $this->get(route('resource_edits.show', ['resource_edit' => $resourceEdit->id]));
        $response->assertStatus(200);
        $response->assertDontSeeText('no comments');

        $comment = Comment::first();
        $response = $this->get(route('comment.comments', ['id' => $comment->id, 'type'=>'comment']));
        $response->assertStatus(403);

        $resourceReview = ResourceReview::first();
        $response = $this->get(route("resource_reviews.index", ['resource'=>$resourceReview->id]));
        
        $response->assertStatus(200);
        $response->assertDontSeeText('no comments');
    }

    public function test_unauthorized_user_cannot_post_comment()
    {
        $this->seed();

        $resource = Resource::first();
        $response = $this->post(route('comment.store', ['type' => 'resource', 'id' => $resource->id]), [
            'comment_text' => "this is a comment"
        ]);        
        
        // redirect to login page
        $response->assertStatus(302);
    }

    public function test_can_post_comment_for_resource()
    {
        $this->seed(ResourceSeeder::class);
    
        $commentText = "this is a unique comment 123!";
        $resource = Resource::first();
        $user = User::factory()->create();
    
        $this->actingAs($user)->post(route('comment.store', ['type' => 'resource', 'id' => $resource->id]), [
            'comment_text' => $commentText
        ]);
    
        $this->get(route('comment.comments', ['type' => 'resource', 'id' => $resource->id]))->assertSee($commentText);
    
        $this->assertDatabaseHas('comments', [
            'comment_text' => $commentText,
            'user_id' => $user->id,
            'commentable_id' => $resource->id,
            'commentable_type' => get_class($resource),
        ]);
    }
        
    public function test_ignores_invalid_request_inputs_for_comment()
    {
        $this->seed(ResourceSeeder::class);

        $commentText = "this is a comment with a bad request. HAHA";
        $resource = Resource::first();
        $user = User::factory(['name' => 'super epic user'])->create();
        $notUserID = $user->id + 1;

        $response = $this->actingAs($user)->post(route('comment.store', ['type' => 'resource', 'id' => $resource->id]), [
            'comment_text' => $commentText,
            'user_id' => $notUserID,
            'commentable_type' => "Hello Goodbye!",
            'commentable_id' => "32",
        ]);


        $response = $this->get(route('comment.comments', ['type' => 'resource', 'id' => $resource->id]));
        $response->assertSeeText($commentText);
        $response->assertSeeText("super epic user");

        $this->assertDatabaseHas('comments', [
            'comment_text' => $commentText,
            'user_id' => $user->id,
            'commentable_id' => $resource->id,
            'commentable_type' => get_class($resource),
        ]);
    }

    public function test_fails_invalid_comments()
    {
        $this->seed(ResourceSeeder::class);

        $resource = Resource::first();
        $user = User::factory()->create();

        // User posts an empty comment
        $response = $this->actingAs($user)->post(route('comment.store', ['type' => 'resource', 'id' => $resource->id]), [
            'comment_text' => '',
        ]);

        // Ensure the response status is not 200 (indicating failure)
        $response->assertStatus(302); // Typically, a validation failure will redirect

        // Ensure the comment does not exist in the database
        $this->assertDatabaseMissing('comments', [
            'comment_text' => '',
            'user_id' => $user->id,
            'commentable_id' => $resource->id,
            'commentable_type' => get_class($resource),
        ]);
    }
    
    public function test_can_reply_to_comment()
    {
        $this->seed(ResourceSeeder::class);
    
        $resource = Resource::first();
    
        $user1 = User::factory(['name' => 'John'])->create();
        $user2 = User::factory(['name' => 'Ringo'])->create();
    
        // User1 posts a comment
        $response = $this->actingAs($user1)->post(route('comment.store', ['type' => 'resource', 'id' => $resource->id]), [
            'comment_text' => 'Imagine no Heaven',
        ]);
    
        $this->assertDatabaseHas('comments', [
            'comment_text' => "Imagine no Heaven",
            'user_id' => $user1->id,
            'commentable_id' => $resource->id,
            'commentable_type' => get_class($resource),
        ]);
    
        // Capture the ID of the original comment
        $originalComment = Comment::where('comment_text', 'Imagine no Heaven')->first();
        \Log::debug('Original comment', ['comment ID' => $originalComment->id]);
    
        // Ensure the original comment exists
        $this->assertNotNull($originalComment, 'Original comment not found');
    
        // User2 replies to the original comment
        $commentText = 'Liverpool, I miss youuu';
        $response = $this->actingAs($user2)->post(route('comment.reply', ['comment' => $originalComment->id]), [
            'comment_text' => $commentText,
        ]);
    
        \Log::info("This is the success " . json_encode($response));
        \Log::info("test, I was reached");
    
        // Ensure the reply is created in the database
        $this->assertDatabaseHas('comments', [
            'comment_text' => $commentText,
            'user_id' => $user2->id,
            'commentable_id' => $originalComment->id,
            'commentable_type' => Comment::class,
        ]);
    
        \Log::debug('User2 replied to the comment', ['response' => $response->getContent()]);
    
        // Ensure the reply is returned in the response
        $response = $this->get(route('comment.comments', ['type' => 'resource', 'id' => $resource->id]));
        $response->assertSee($commentText);
        $response->assertSee("Ringo");
    }
        
    // TODO: make sure that we can return 404 on all requests that are given bad data.

    public function test_returns_correct_comment_tree() {
        $this->seed(ResourceSeeder::class);
        
        // Create a chain of comments
        $resource = Resource::first();
    
        $user1 = User::factory(['name' => 'super epic user 1'])->create();
        $user2 = User::factory(['name' => 'super epic user 2'])->create();
    
        // Define comment texts
        $commentText1 = "parent: None, comment 1";
        $commentText2 = "parent: 1, comment 2";
        $commentText3 = "parent: 1, comment 3";
        $commentText4 = "parent: 2, comment 4";
        $commentText5 = "Final comment";
    
        // Post the original comment
        $this->actingAs($user1)->post(route('comment.store', ['type' => 'resource', 'id' => $resource->id]), [
            'comment_text' => $commentText1,
        ]);
        $this->assertDatabaseHas('comments', [
            'comment_text' => $commentText1,
            'user_id' => $user1->id,
            'commentable_id' => $resource->id,
            'commentable_type' => Resource::class,
        ]);
    
        // Find the original comment
        $originalComment = Comment::where('comment_text', $commentText1)->first();
    
        // Post replies to the original comment
        $this->actingAs($user2)->post(route('comment.reply', ['comment' => $originalComment->id]), [
            'comment_text' => $commentText2,
        ]);
        $this->assertDatabaseHas('comments', [
            'comment_text' => $commentText2,
            'user_id' => $user2->id,
            'commentable_id' => $originalComment->id,
            'commentable_type' => Comment::class,
        ]);
    
        $this->actingAs($user2)->post(route('comment.reply', ['comment' => $originalComment->id]), [
            'comment_text' => $commentText3,
        ]);
        $this->assertDatabaseHas('comments', [
            'comment_text' => $commentText3,
            'user_id' => $user2->id,
            'commentable_id' => $originalComment->id,
            'commentable_type' => Comment::class,
        ]);
    
        // Find the second comment
        $secondComment = Comment::where('comment_text', $commentText2)->first();
    
        // Post a reply to the second comment
        $this->actingAs($user1)->post(route('comment.reply', ['comment' => $secondComment->id]), [
            'comment_text' => $commentText4,
        ]);
        $this->assertDatabaseHas('comments', [
            'comment_text' => $commentText4,
            'user_id' => $user1->id,
            'commentable_id' => $secondComment->id,
            'commentable_type' => Comment::class,
        ]);
    
        // Find the fourth comment
        $fourthComment = Comment::where('comment_text', $commentText4)->first();
    
        // Post a reply to the fourth comment
        $this->actingAs($user2)->post(route('comment.reply', ['comment' => $fourthComment->id]), [
            'comment_text' => $commentText5,
        ]);
        $this->assertDatabaseHas('comments', [
            'comment_text' => $commentText5,
            'user_id' => $user2->id,
            'commentable_id' => $fourthComment->id,
            'commentable_type' => Comment::class,
        ]);
    
        // Expected comment tree structure:
        // A 
        // | B
        //   | D
        //     | E
        // | C
    }
    
    public function test_is_limit_to_comments_depth() {
        $this->seed([ResourceSeeder::class]);
        
        $user1 = User::factory(['name' => 'super epic user 1'])->create();
        
        $resource = Resource::first();
        // get the max comment depth and repeatedly create new comments until you hit the limit
        // expect failure as a result 
        
        $commentText = "comment number 0";
    
        // Post the original comment
        $this->actingAs($user1)->post(route('comment.store', ['type' => 'resource', 'id' => $resource->id]), [
            'comment_text' => $commentText,
        ]);
       
        $commentDepth = config("comments")['maximum_depth'];
        $replyTo = Comment::where('comment_text', $commentText)->first();
   
        for ($i = 0; $i < $commentDepth; $i++)
        {
            $this->assertDatabaseHas('comments', [
                'comment_text' => $commentText,
            ]);
 
            $commentText = 'comment number ' . ($i+1);
            $response = $this->actingAs($user1)->post(route('comment.reply', ['comment'=>$replyTo->id]), [
                'comment_text' => $commentText,
            ]);
            // get the next comment
            $replyTo = Comment::where("comment_text", $commentText)->first();
        }

        // The final comment should not be found, but the other ones should be found
        $this->assertDatabaseMissing('comments', [
            'comment_text' => $commentText,
        ]);

        $response->assertSessionHasErrors();
    }
}
