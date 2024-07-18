<?php


namespace App\Tests\Api;

use App\Tests\Support\ApiTester;

class ProductCest extends BaseCest
{
    protected $id;

    public function _before(ApiTester $I)
    {
    }

    public function tryToTestGet(ApiTester $I)
    {
        $I->sendGET('/api/product', []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(200);
    }

    public function tryToTestPostGetPutDelete(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');

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

        $id = $I->grabDataFromResponseByJsonPath('$.id')[0];
        $I->amGoingTo('see id: '. $id);

        $I->sendGET('/api/product/' . $id, []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(200);

        $I->sendPUT('/api/product/' . $id, [
            'category_id' => $category_id,
            'name' => 'name_' . time(),
            'description' => 'desc_' . time(),
            'brand' => 'brand_' . time(),
            'model' => 'model_' . time(),
            'unit_price' => 88,
        ]);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(204);

        // clean up
        $I->sendDELETE('/api/product/' . $id, []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(204);

        $I->sendDELETE('/api/category/' . $category_id, []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(204);
    }

    public function tryToTestFailPost(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendPOST('/api/product', []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(422);
    }

    public function tryToTestFailPut(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendPUT('/api/product/999999', []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(422);
    }
}
