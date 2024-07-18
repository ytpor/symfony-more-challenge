<?php


namespace App\Tests\Api;

use App\Tests\Support\ApiTester;

class CategoryCest extends BaseCest
{
    protected $id;

    public function _before(ApiTester $I)
    {
    }

    public function tryToTestGet(ApiTester $I)
    {
        $I->sendGET('/api/category', []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(200);
    }

    public function tryToTestPostGetPutDelete(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendPOST('/api/category', [
            'name' => 'name_' . time(),
            'description' => 'desc_' . time(),
        ]);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(201);

        $id = $I->grabDataFromResponseByJsonPath('$.id')[0];
        $I->amGoingTo('see id: '. $id);

        $I->sendGET('/api/category/' . $id, []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(200);

        $I->sendPUT('/api/category/' . $id, [
            'name' => 'name_' . time(),
            'description' => 'desc_' . time(),
        ]);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(204);

        // clean up
        $I->sendDELETE('/api/category/' . $id, []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(204);
    }

    public function tryToTestFailPost(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendPOST('/api/category', []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(422);
    }

    public function tryToTestFailPut(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendPUT('/api/category/999999', []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(422);
    }
}
