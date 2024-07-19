<?php


namespace App\Tests\Api;

use App\Tests\Support\ApiTester;

class ProductAttributeCest extends BaseCest
{
    protected $id;

    public function _before(ApiTester $I)
    {
    }

    public function tryToTestGet(ApiTester $I)
    {
        $I->sendGET('/api/product-attribute', []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(200);
    }

    public function tryToTestPostGetPutDelete(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');

        // need to create a category
        $I->sendPOST('/api/category', [
            'name' => 'attribute_name_' . time(),
            'description' => 'attribute_desc_' . time(),
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

        // and also an attribute
        $I->sendPOST('/api/attribute', [
            'name' => 'attribute_name_' . time(),
            'description' => 'attribute_desc_' . time(),
        ]);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(201);

        $attribute_id = $I->grabDataFromResponseByJsonPath('$.id')[0];
        $I->amGoingTo('see attribute_id: '. $attribute_id);

        // create record
        $I->sendPOST('/api/product-attribute', [
            'product_id' => $product_id,
            'attribute_id' => $attribute_id,
        ]);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(201);

        $id = $I->grabDataFromResponseByJsonPath('$.id')[0];
        $I->amGoingTo('see id: '. $id);

        // show record
        $I->sendGET('/api/product-attribute/' . $id, []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(200);

        // update record
        $I->sendPUT('/api/product-attribute/' . $id, [
            'product_id' => $product_id,
            'attribute_id' => $attribute_id,
        ]);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(204);

        // clean up
        $I->sendDELETE('/api/product-attribute/' . $id, []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(204);

        $I->sendDELETE('/api/attribute/' . $attribute_id, []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(204);

        $I->sendDELETE('/api/product/' . $product_id, []);
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

        // create record
        $I->sendPOST('/api/product-attribute', []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(422);
    }

    public function tryToTestFailPut(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');

        // update record
        $I->sendPUT('/api/product-attribute/999999', []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(422);
    }
}
