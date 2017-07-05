<?php

namespace Tests\Feature;

use Faker\Factory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Eloquent\Category;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    /* TEST GET BOOKS OF USER */

//    public function testGetDataBookOfUserSuccess()
//    {
//        $faker = Factory::create();
//        $userId = $this->createUser()->id;
//
//        $action = $faker->randomElement(array_merge(
//            [config('model.user_sharing_book')], array_keys(config('model.book_user.status'))
//        ));
//
//        $response = $this->call('GET', route('api.v0.users.book', ['id' => $userId, 'action' => $action]), [], [], [], $this->getFauthHeaders());
//
//        $response->assertJsonStructure([
//            'items' => [
//                'total', 'per_page', 'current_page', 'next_page', 'prev_page', 'data'
//            ],
//            'message' => [
//                'status', 'code',
//            ],
//        ])->assertJson([
//            'message' => [
//                'status' => true,
//                'code' => 200,
//            ]
//        ])->assertStatus(200);
//    }

    public function testGetDataBookOfUserWithGuest()
    {
        $headers = $this->getHeaders();
        $userId = $this->createUser()->id;

        $response = $this->call('GET', route('api.v0.users.book', ['id' => $userId, 'action' => 'action']), [], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description'
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 401,
            ]
        ])->assertStatus(401);
    }

    public function testGetDataBookOfUserWithActionException()
    {
        $userId = $this->createUser()->id;

        $response = $this->call('GET', route('api.v0.users.book', ['id' => $userId, 'action' => 'action']), [], [], [], $this->getFauthHeaders());

        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description'
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 500,
                'description' => [translate('exception.action')]
            ]
        ])->assertStatus(500);
    }

    /* TEST GET USER PROFILE */

    public function testGetUserProfileSuccess()
    {
        $headers = $this->getFauthHeaders();

        $response = $this->call('GET', route('api.v0.user.profile'), [], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code',
            ],
        ])->assertJson([
            'message' => [
                'status' => true,
                'code' => 200,
            ]
        ])->assertStatus(200);
    }

    public function testGetUserProfileWithGuest()
    {
        $headers = $this->getHeaders();

        $response = $this->call('GET', route('api.v0.user.profile'), [], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description'
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 401,
            ]
        ])->assertStatus(401);
    }

    /* TEST ADD TAGS FOR USERS */

    public function testAddTagsSuccess()
    {
        $headers = $this->getFauthHeaders();
        $categoryId = factory(Category::class)->create()->id;
        $data['tags'] = $categoryId;

        $response = $this->call('POST', route('api.v0.user.add.tags'), ['item' => $data], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code',
            ],
        ])->assertJson([
            'message' => [
                'status' => true,
                'code' => 200,
            ]
        ])->assertStatus(200);
    }

    public function testAddTagsWithGuest()
    {
        $headers = $this->getHeaders();
        $categoryId = factory(Category::class)->create()->id;
        $data['tags'] = $categoryId;

        $response = $this->call('POST', route('api.v0.user.add.tags'), ['item' => $data], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description'
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 401,
            ]
        ])->assertStatus(401);
    }

    public function testAddTagsWithFieldsNull()
    {
        $headers = $this->getFauthHeaders();

        $response = $this->call('POST', route('api.v0.user.add.tags'), [], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description'
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 422,
            ]
        ])->assertStatus(422);
    }

    /* TEST GET INTERESTED BOOKS BY CURRENT USER */

//    public function testGetInterestedBooksSuccess()
//    {
//        $headers = $this->getFauthHeaders();
//
//        $response = $this->call('GET', route('api.v0.user.interested.books'), [], [], [], $headers);
//        $response->assertJsonStructure([
//            'message' => [
//                'status', 'code',
//            ],
//        ])->assertJson([
//            'message' => [
//                'status' => true,
//                'code' => 200,
//            ]
//        ])->assertStatus(200);
//    }

    public function testGetInterestedBooksWithGuest()
    {
        $headers = $this->getHeaders();

        $response = $this->call('GET', route('api.v0.user.interested.books'), [], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description'
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 401,
            ]
        ])->assertStatus(401);
    }

    /* TEST SHOW PROFILE OTHERS USER */

    public function testShowProfileOthersUserSuccess()
    {
        $headers = $this->getFauthHeaders();
        $userId = $this->createUser()->id;

        $response = $this->call('GET', route('api.v0.users.show', $userId), [], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code',
            ],
        ])->assertJson([
            'message' => [
                'status' => true,
                'code' => 200,
            ]
        ])->assertStatus(200);
    }

    public function testShowProfileOthersUserWithGuest()
    {
        $headers = $this->getHeaders();
        $userId = $this->createUser()->id;

        $response = $this->call('GET', route('api.v0.users.show', $userId), [], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description'
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 401,
            ]
        ])->assertStatus(401);
    }

    public function testShowProfileOthersUserWithInvalidUserId()
    {
        $headers = $this->getFauthHeaders();

        $response = $this->call('GET', route('api.v0.users.show', 'xxx'), [], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description'
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 404,
            ]
        ])->assertStatus(404);
    }

    /* TEST GET OWNED OF USER */

//    public function testOwnedOfUserSuccess()
//    {
//        $headers = $this->getFauthHeaders();
//
//        $response = $this->call('GET', route('api.v0.users.books.owned'), [], [], [], $headers);
//        $response->assertJsonStructure([
//            'message' => [
//                'status', 'code',
//            ],
//        ])->assertJson([
//            'message' => [
//                'status' => true,
//                'code' => 200,
//            ]
//        ])->assertStatus(200);
//    }

    public function testOwnedOfUserWithGuest()
    {
        $headers = $this->getHeaders();

        $response = $this->call('GET', route('api.v0.users.books.owned'), [], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description'
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 401,
            ]
        ])->assertStatus(401);
    }
}
