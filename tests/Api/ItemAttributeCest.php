<?php


namespace App\Tests\Api;

use App\Tests\Support\ApiTester;

class ItemAttributeCest extends BaseCest
{
    protected $id;

    public function _before(ApiTester $I)
    {
    }

    public function tryToTestGet(ApiTester $I)
    {
        $I->sendGET('/api/attribute', []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(200);
    }

    public function tryToTestPostGetPutDelete(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendPOST('/api/attribute', [
            'name' => 'name_' . time(),
            'description' => 'desc_' . time(),
        ]);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(201);

        $id = $I->grabDataFromResponseByJsonPath('$.id')[0];
        $I->amGoingTo('see id: '. $id);

        $I->sendGET('/api/attribute/' . $id, []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(200);

        $I->sendPUT('/api/attribute/' . $id, [
            'name' => 'name_' . time(),
            'description' => 'desc_' . time(),
        ]);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(204);

        // clean up
        $I->sendDELETE('/api/attribute/' . $id, []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(204);
    }

    public function tryToTestFailPost(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendPOST('/api/attribute', []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(422);
    }

    public function tryToTestFailPut(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendPUT('/api/attribute/999999', []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(422);
    }
}
