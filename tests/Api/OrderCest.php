<?php


namespace App\Tests\Api;

use App\Tests\Support\ApiTester;

class OrderCest extends BaseCest
{
    protected $id;

    public function _before(ApiTester $I)
    {
    }

    public function tryToTestGet(ApiTester $I)
    {
        $I->sendGET('/api/order', []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(200);
    }

    public function tryToTestPostGetPutDelete(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');

        // need to create a user
        $I->sendPOST('/api/user', [
            'email' => time() . '@email.com',
            'password' => uniqid(),
            'name' => 'name_' . time(),
            'phone' => 'phone_' . time(),
            'address' => 'address_' . time(),
        ]);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(201);

        $user_id = $I->grabDataFromResponseByJsonPath('$.id')[0];
        $I->amGoingTo('see user_id: '. $user_id);

        // need to create a category
        $I->sendPOST('/api/category', [
            'name' => 'name_' . time(),
            'description' => 'desc_' . time(),
        ]);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(201);

        $category_id = $I->grabDataFromResponseByJsonPath('$.id')[0];
        $I->amGoingTo('see category_id: '. $category_id);

        // then create a product
        $I->sendPOST('/api/product', [
            'category_id' => $category_id,
            'name' => 'name_' . time(),
            'description' => 'desc_' . time(),
            'brand' => 'brand_' . time(),
            'model' => 'model_' . time(),
            'unit_price' => 88,
        ]);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(201);

        $product_id = $I->grabDataFromResponseByJsonPath('$.id')[0];
        $I->amGoingTo('see product_id: '. $product_id);

        // create record
        $I->sendPOST('/api/order', [
            'user_id' => $user_id,
            'products' => [
                [
                    'product_id' => $product_id,
                    'quantity' => 4,
                ],
                [
                    'product_id' => $product_id,
                    'quantity' => 3,
                ],
            ]
        ]);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(201);

        $id = $I->grabDataFromResponseByJsonPath('$.id')[0];
        $I->amGoingTo('see id: '. $id);

        // show record
        $I->sendGET('/api/order/' . $id, []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(200);

        // update record status
        $I->sendPUT('/api/order/status/' . $id, [
            'status' => 'status',
        ]);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(204);

        // update record rating
        $I->sendPUT('/api/order/product-rating/' . $id, [
            'product_id' => $product_id,
            'rating' => 9,
        ]);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(204);

        // clean up
        $I->sendDELETE('/api/order/' . $id, []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(204);

        $I->sendDELETE('/api/user/' . $user_id, []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(204);

        $I->sendDELETE('/api/product/' . $product_id, []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(204);
    }

    public function tryToTestFailPost(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');

        // create record
        $I->sendPOST('/api/order', []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(422);
    }

    public function tryToTestFailPut(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');

        // update record
        $I->sendPUT('/api/order/status/999999', []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(422);
    }
}
