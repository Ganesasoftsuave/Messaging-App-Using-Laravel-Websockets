<?php

namespace Tests\Feature;

use App\Events\AllUsersMessageEvent;
use App\Events\GroupMessageEvent;
use App\Events\OneToOneMessageEvent;
use Tests\TestCase;
use App\Models\User;
use App\Models\Message;
use App\Models\UserGroup;
use App\Jobs\SendMessageJob;
use App\Models\UserGroupMember;
use App\Models\MessageRecipient;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SendMessageTest extends TestCase
{

    use RefreshDatabase;
    protected $faker;

    public function setUp(): void
    {
        parent::setUp();
        $this->faker = \Faker\Factory::create();
    }

    public function testSendMessagesWithOneToOneUsers_withValidData_returnMessageSentSuccesfully()
    {
        // Arrange
        Queue::fake();
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $payload = [
            'receiver_id' => $receiver->id,
            'content' => $this->faker->text,
            'sender_id' => $sender->id,
            'sender_name' => $sender->name,
            'message_type' => 'individual',
        ];
        $expectedResponse = ['success' => 'Message sent successfully.'];

        // Act
        $response = $this->postJson(route('send.message'), $payload);

        // Assert
        $response->assertStatus(200)
            ->assertJson($expectedResponse);
        Queue::assertPushed(SendMessageJob::class);

    }


    public function testSendMessagesWithOneToOneUsers_withInvalidData_returnInvalidDataError()
    {
        // Arrange
        $sender = User::factory()->create();
        $message = $this->faker->text;
        $payload = [
            'receiver_id' => $this->faker->randomNumber(3),
            'content' => $message,
            'sender_id' => $sender->id,
            'sender_name' => $sender->name,
            'message_type' => 'individual',
        ];
        $expectedResponse = ['error' => 'The selected receiver id is invalid.'];

        // Act
        $response = $this->postJson(route('send.message'), $payload);

        // Assert
        $response->assertStatus(422)
            ->assertJson($expectedResponse);


    }
    public function testSendMessagesWithOneToOneUsers_withInvalidData_returnInternalServerError()
    {
        // Arrange
        Queue::fake();
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        $message = $this->faker->text;
        $payload = [
            'receiver_id' => $receiver->id,
            'content' => $message,
            'sender_id' => $sender->id,
            'sender_name' => $sender->name,
            'message_type' => 'individual',
        ];
        $expectedResponse = ['error' => 'Something Went Wrong'];

        // Act
        Queue::shouldReceive('push')->andThrow(new \Exception('Error sending message'));
        $response = $this->postJson(route('send.message'), $payload);

        // Assert
        $response->assertStatus(500)
            ->assertJson($expectedResponse);



    }
    public function testSendMessagesWithGroupUsers_withValidData_returnMessageSentSuccesfully()
    {
        // Arrange
        Queue::fake();
        $userGroup = UserGroup::factory()->create();
        $userGroupMembers = UserGroupMember::factory(10)->create(['group_id' => $userGroup->id]);
        $userGroupMember = $userGroupMembers->random();
        $message = $this->faker->text;
        $groupName = UserGroup::where('id', $userGroupMember->group_id)->value('name');
        $payload = [
            'group_id' => $userGroupMember->group_id,
            'content' => $message,
            'sender_id' => $userGroupMember->user_id,
            'sender_name' => $userGroupMember->user_name,
            'message_type' => 'Group',
            'group_name' => $groupName,
        ];
        $expectedResponse = ['success' => 'Message sent successfully.'];

        // Act
        $response = $this->postJson(route('send.groupmessage'), $payload);

        // Assert
        $response->assertStatus(200)
            ->assertJson($expectedResponse);
        Queue::assertPushed(SendMessageJob::class);

    }

    public function testSendMessagesWithGroupUsers_withInvalidData_returnInvalidDataError()
    {
        // Arrange
        $userGroup = UserGroup::factory()->create();
        $userGroupMembers = UserGroupMember::factory(10)->create(['group_id' => $userGroup->id]);
        $userGroupMember = $userGroupMembers->random();
        $message = $this->faker->text;
        $groupName = UserGroup::where('id', $userGroupMember->group_id)->value('name');
        $payload = [
            'group_id' => "Test",
            'content' => $message,
            'sender_id' => $userGroupMember->user_id,
            'sender_name' => $userGroupMember->user_name,
            'message_type' => 'Group',
            'group_name' => $groupName,
        ];
        $expectedResponse = ['error' => 'The group id must be an integer.'];

        // Act
        $response = $this->postJson(route('send.groupmessage'), $payload);

        // Assert
        $response->assertStatus(422)
            ->assertJson($expectedResponse);

    }
    public function testSendMessagesWithGroupUsers_withInvalidData_returnInternalServerError()
    {
        // Arrange
        $userGroup = UserGroup::factory()->create();
        $userGroupMembers = UserGroupMember::factory(10)->create(['group_id' => $userGroup->id]);
        $userGroupMember = $userGroupMembers->random();
        $message = $this->faker->text;
        $groupName = UserGroup::where('id', $userGroupMember->group_id)->value('name');
        $payload = [
            'group_id' => $userGroupMember->group_id,
            'content' => $message,
            'sender_id' => $userGroupMember->user_id,
            'sender_name' => $userGroupMember->user_name,
            'message_type' => 'Group',
            'group_name' => $groupName,
        ];
        $expectedResponse = ['error' => 'Something Went Wrong'];

        // Act
        Queue::shouldReceive('push')->andThrow(new \Exception('Error sending message'));
        $response = $this->postJson(route('send.groupmessage'), $payload);

        // Assert
        $response->assertStatus(500)
            ->assertJson($expectedResponse);

    }

    public function testSendMessagesWithAllUsers_withValidData_returnMessageSentSuccesfully()
    {
        // Arrange
        Queue::fake();
        $users = User::factory(10)->create();
        $sender = $users->random();
        $message = $this->faker->text;
        $payload = [
            'content' => $message,
            'sender_id' => $sender->id,
            'sender_name' => $sender->name,
            'message_type' => 'all',
        ];
        $expectedResponse = ['success' => 'Message sent successfully.'];

        // Act
        $response = $this->postJson(route('send.message.to.all'), $payload);

        // Assert
        $response->assertStatus(200)
            ->assertJson($expectedResponse);
        Queue::assertPushed(SendMessageJob::class);
    }
    public function testSendMessagesWithAllUsers_withInvalidData_returnInvalidDataError()
    {
        // Arrange
        $users = User::factory(10)->create();
        $sender = $users->random();
        $message = $this->faker->text;
        $payload = [

            'content' => $message,
            'sender_id' => $this->faker->randomNumber(3),
            'sender_name' => $sender->name,
            'message_type' => 'all',

        ];
        $expectedResponse = ['error' => 'The selected sender id is invalid.'];

        // Act
        $response = $this->postJson(route('send.message.to.all'), $payload);

        // Assert
        $response->assertStatus(422)
            ->assertJson($expectedResponse);

    }
    public function testSendMessagesWithAllUsers_withInvalidData_returnInternalServerError()
    {
        // Arrange
        Queue::fake();
        $users = User::factory(10)->create();
        $sender = $users->random();
        $message = $this->faker->text;
        $payload = [

            'content' => $message,
            'sender_id' => $sender->id,
            'sender_name' => $sender->name,
            'message_type' => 'all',

        ];
        $expectedResponse = ['error' => 'Something Went Wrong'];

        // Act
        Queue::shouldReceive('push')->andThrow(new \Exception('Error sending message'));
        $response = $this->postJson(route('send.message.to.all'), $payload);

        // Assert
        $response->assertStatus(500)
            ->assertJson($expectedResponse);

    }




    public function testSendMessage_withValidMessageContent_checkingMessageEncryptingAndDecryptedSuccesfullyOrNot()
    {
        // Arrange
        Event::fake();
        $users = User::factory(10)->create();
        $sender = $users->random();
        $message = $this->faker->text;
        $payload = [
            'content' => $message,
            'sender_id' => $sender->id,
            'sender_name' => $sender->name,
            'message_type' => 'all',
        ];

        $expectedResponse = ['success' => 'Message sent successfully.'];
        $response = $this->postJson(route('send.message.to.all'), $payload);
        $lastMessage = Message::latest()->first();
        $encryptedMessage = $lastMessage->content;

        // Act
        $decrptedResponse = $this->postJson(route('decrypt.message'), ['encryptedMessage' => $encryptedMessage]);
        $decryptedMessage = $decrptedResponse->json('decryptedMessage');

        // Assert
        $response->assertStatus(200)
            ->assertJson($expectedResponse);
        $this->assertNotEquals($payload['content'], $lastMessage->content);
        $this->assertEquals($payload['content'], $decryptedMessage);
        Event::assertDispatched(AllUsersMessageEvent::class);


    }
    public function testSendMessage_withInvalidMessageContent_checkingMessageDecryptedSuccesfullyOrNot()
    {
        // Arrange
        $users = User::factory(10)->create();
        $sender = $users->random();
        $message = $this->faker->text;
        $payload = [
            'content' => $message,
            'sender_id' => $sender->id,
            'sender_name' => $sender->name,
            'message_type' => 'all',
        ];

        $expectedResponse = ['success' => 'Message sent successfully.'];
        $expecteddecrptedResponse = ['error' => 'Failed to decrypt the message.'];

        // Act
        $response = $this->postJson(route('send.message.to.all'), $payload);
        $decrptedResponse = $this->postJson(route('decrypt.message'), ['encryptedMessage' => $this->faker->text]);

        // Assert
        $response->assertStatus(200)
            ->assertJson($expectedResponse);
        $decrptedResponse->assertStatus(500)
            ->assertJson($expecteddecrptedResponse);
    }
    public function testSendMessagesWithSpecificUsers_withValidData_verifyOnlyTheIntendedUserReceivesTheMessage()
    {
        // Arrange 
        Event::fake();
        $users = User::factory(10)->create();
        $sender = $users->random();
        $receiver = $users->random();
        $message = $this->faker->text;
        $payload = [
            'receiver_id' => $receiver->id,
            'content' => $message,
            'sender_id' => $sender->id,
            'sender_name' => $sender->name,
            'message_type' => 'individual',
        ];

        $expectedResponse = ['success' => 'Message sent successfully.'];

        // Act
        $response = $this->postJson(route('send.message'), $payload);
        $messageCount = MessageRecipient::count();

        // Assert
        $response->assertStatus(200)
            ->assertJson($expectedResponse);
        $this->assertEquals(1, $messageCount);
        Event::assertDispatched(OneToOneMessageEvent::class);

    }

    public function testSendMessagesWithGroupUsers_withValidData_verifyAllTheUsersWithinTheGroupReceiveMessage()
    {
        // Arrange
        Event::fake();
        $userGroup = UserGroup::factory()->create();
        $message = $this->faker->text;
        $userGroupMembers = UserGroupMember::factory(10)->create(['group_id' => $userGroup->id]);
        $userGroupMember = $userGroupMembers->where('is_subscribe', true)->random();
        $subscribedCount = UserGroupMember::where('is_subscribe', true)
            ->where('id', '!=', $userGroupMember->id)
            ->count();
        $groupName = UserGroup::where('id', $userGroupMember->group_id)->value('name');
        $payload = [
            'group_id' => $userGroupMember->group_id,
            'content' => $message,
            'sender_id' => $userGroupMember->user_id,
            'sender_name' => $userGroupMember->user_name,
            'message_type' => 'group',
            'group_name' => $groupName,
        ];
        $expectedResponse = ['success' => 'Message sent successfully.'];

        // Act
        $response = $this->postJson(route('send.groupmessage'), $payload);
        $messageCount = MessageRecipient::count();

        // Assert
        $response->assertStatus(200)
            ->assertJson($expectedResponse);
        $this->assertEquals($subscribedCount, $messageCount);
        Event::assertDispatched(GroupMessageEvent::class);


    }
    public function testSendMessagesWithAllUsers_withValidData_verifyAllTheUsersReceiveMessage()
    {
        // Arrange
        Event::fake();
        $users = User::factory(10)->create();
        $sender = $users->random();
        $message = $this->faker->text;
        $usersCount = User::where('id', '!=', $sender->id)
            ->count();
        $payload = [
            'content' => $message,
            'sender_id' => $sender->id,
            'sender_name' => $sender->name,
            'message_type' => 'all',
        ];
        $expectedResponse = ['success' => 'Message sent successfully.'];

        // Act
        $response = $this->postJson(route('send.message.to.all'), $payload);
        $messageCount = MessageRecipient::count();

        // Assert
        $response->assertStatus(200)
            ->assertJson($expectedResponse);
        $this->assertEquals($usersCount, $messageCount);
        Event::assertDispatched(AllUsersMessageEvent::class);

    }

    public function testSendMessagesTo1000Users_withValidData_verifyAllTheUsersReceiveMessage()
    {
        // Arrange
        Event::fake();
        $users = User::factory(1000)->create();
        $sender = $users->random();
        $usersCount = User::where('id', '!=', $sender->id)
            ->count();
        $message = $this->faker->text;
        $payload = [
            'content' => $message,
            'sender_id' => $sender->id,
            'sender_name' => $sender->name,
            'message_type' => 'all',
        ];
        $expectedResponse = ['success' => 'Message sent successfully.'];

        // Act
        $response = $this->postJson(route('send.message.to.all'), $payload);
        $messageCount = MessageRecipient::count();

        // Assert
        $response->assertStatus(200)
            ->assertJson($expectedResponse);
        $this->assertEquals($usersCount, $messageCount);
        Event::assertDispatched(AllUsersMessageEvent::class);

    }

    public function testGetMessageList_withValidData_returnSuccessReponse()
    {
        // Arrange
        Event::fake();
        $users = User::factory(10)->create();
        $sender = $users->random();
        $receiver = $users->random();
        $message = $this->faker->text;
        $payload = [
            'receiver_id' => $receiver->id,
            'content' => $message,
            'sender_id' => $sender->id,
            'sender_name' => $sender->name,
            'message_type' => 'individual',
        ];

        $expectedResponse = ['success' => 'Message sent successfully.'];

        // Act
        $response = $this->postJson(route('send.message'), $payload);
        $messageListResponse = $this->getJson(route('get.message.list', ['userId' => $receiver->id]));

        // Assert
        $response->assertStatus(200)
            ->assertJson($expectedResponse);
        $messageListResponse->assertStatus(200);
        Event::assertDispatched(OneToOneMessageEvent::class);
    }

    public function testGetMessageList_withInValidData_returnFailureReponse()
    {
        // Arrange
        Queue::fake();
        $users = User::factory(10)->create();
        $sender = $users->random();
        $receiver = $users->random();
        $message = $this->faker->text;
        $payload = [
            'receiver_id' => $receiver->id,
            'content' => $message,
            'sender_id' => $sender->id,
            'sender_name' => $sender->name,
            'message_type' => 'individual',
        ];

        $expectedResponse = ['success' => 'Message sent successfully.'];

        // Act
        $response = $this->postJson(route('send.message'), $payload);
        $messageListResponse = $this->getJson(route('get.message.list', ['userId' => 'Test']));

        // Assert
        $response->assertStatus(200)
            ->assertJson($expectedResponse);
        $messageListResponse->assertStatus(500)
            ->assertJson(['error' => 'Failed to fetch notifications messages.']);
        Queue::assertPushed(SendMessageJob::class);
    }
    public function testUpdateNotificationCount_withValidData_returnSuccessReponse()
    {
        // Arrange
        Event::fake();
        $users = User::factory(10)->create();
        $sender = $users->random();
        $receiver = $users->random();
        $message = $this->faker->text;
        $payload = [
            'receiver_id' => $receiver->id,
            'content' => $message,
            'sender_id' => $sender->id,
            'sender_name' => $sender->name,
            'message_type' => 'individual',
        ];

        $expectedResponse = ['success' => 'Message sent successfully.'];

        // Act
        $response = $this->postJson(route('send.message'), $payload);
        $notificationResponse = $this->postJson(route('update.notification.count', ['userId' => $receiver->id]));

        // Assert
        $response->assertStatus(200)
            ->assertJson($expectedResponse);
        $notificationResponse->assertStatus(200)
            ->assertJson(['success' => 'true']);
        Event::assertDispatched(OneToOneMessageEvent::class);
    }

    public function testUpdateNotificationCount_withInValidData_returnErrorReponse()
    {
        // Arrange
        Event::fake();
        $users = User::factory(10)->create();
        $sender = $users->random();
        $receiver = $users->random();
        $message = $this->faker->text;
        $payload = [
            'receiver_id' => $receiver->id,
            'content' => $message,
            'sender_id' => $sender->id,
            'sender_name' => $sender->name,
            'message_type' => 'individual',
        ];

        $expectedResponse = ['success' => 'Message sent successfully.'];
        // Act
        $response = $this->postJson(route('send.message'), $payload);
        $notificationResponse = $this->postJson(route('update.notification.count', ['userId' => $this->faker->randomNumber(3)]));
        // Assert
        $response->assertStatus(200)
            ->assertJson($expectedResponse);
        $notificationResponse->assertStatus(500)
            ->assertJson(['error' => 'Failed to update notification count.']);
        Event::assertDispatched(OneToOneMessageEvent::class);
    }

    public function testUpdateUserSubsribeButtonValueToTrue_withValidData_returnSuccessReponse()
    {
        // Arrange
        $user = User::factory()->create();
        $userGroup = UserGroup::factory()->create();
        $payload = [
            'user_id' => $user->id,
            'group_id' => $userGroup->id,
            'user_name' => $user->name,
        ];
        $expectedResponse = ['is_subscribed' => true];

        // Act
        $response = $this->postJson(route('subscribe'), $payload);

        // Assert
        $response->assertStatus(200)
            ->assertJson($expectedResponse);
       


    }
    public function testUpdateUserSubsribeButtonValueToFalse_withValidData_returnSuccessReponse()
    {
        // Arrange
        $user = User::factory()->create();
        $userGroup = UserGroup::factory()->create();
        $payload = [
            'user_id' => $user->id,
            'group_id' => $userGroup->id,
            'user_name' => $user->name,
        ];
        $expectedResponse = ['is_subscribed' => false];

        // Act
        $this->postJson(route('subscribe'), $payload);
        $falseResponse = $this->postJson(route('subscribe'), $payload);

        // Assert
        $falseResponse->assertStatus(200)
            ->assertJson($expectedResponse);
        


    }
    public function testUpdateUserSubsribeButtonValue_withInValidData_returnFailureReponse()
    {
        // Arrange
        $user = User::factory()->create();
        $userGroup = UserGroup::factory()->create();
        $payload = [
            'user_id' => $this->faker->randomNumber(3),
            'group_id' => $userGroup->id,
            'user_name' => $user->name,
        ];

        // Act
        $response = $this->postJson(route('subscribe'), $payload);

        // Assert
        $response->assertStatus(500)
            ->assertJson(["error" => "Failed to Subscribe the Group."]);


    }

    public function testDashboard_withValidData_returnSuccessReponse()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);
        User::factory(3)->create();
        MessageRecipient::factory()->count(2)->create([
            'recipient_id' => $user->id,
            'seen' => 0,
        ]);
        $userGroups = UserGroup::factory(2)->create();
        UserGroupMember::factory()->create([
            'user_id' => $user->id,
            'group_id' => $userGroups->first()->id,
            'is_subscribe' => true,
        ]);

        // Act
        $response = $this->get(route('user.dashboard'));

        // Assert
        $response->assertStatus(200);

    }
}
