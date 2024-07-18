<?php


namespace App\Tests\Api;

use App\Tests\Support\ApiTester;

class UserCest extends BaseCest
{
    protected $id;

    public function _before(ApiTester $I)
    {
    }

    public function tryToTestGet(ApiTester $I)
    {
        $I->sendGET('/api/user', []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(200);
    }

    public function tryToTestPostGetPutDelete(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');

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

        $id = $I->grabDataFromResponseByJsonPath('$.id')[0];
        $I->amGoingTo('see id: '. $id);

        $I->sendGET('/api/user/' . $id, []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(200);

        // clean up
        $I->sendDELETE('/api/user/' . $id, []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(204);
    }

    public function tryToTestfailPost(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendPOST('/api/user', []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(422);
    }

    public function tryToTestFailPut(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendPUT('/api/user/999999', []);
        $response = $I->grabResponse();
        $I->amGoingTo('see response: '. $response);
        $I->canSeeResponseCodeIs(422);
    }
}
