<?php

namespace Demo\UserBundle\Tests\Controller;

use Demo\BackendBundle\Test\DemoTestCase;
use Demo\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Demo\BackendBundle\Test\ResponseAsserter;

class UserControllerTest extends DemoTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testCreate()
    {
        $helper = $this->getService('demo.test.user_helper');
        $helper->delete('test_create');
        $data = [
            'fullname' => 'Hello World',
            'username' => 'test_create',
            'email' => 'foo@bar.com',
            'plainPassword' => [
                'first' => 'foo',
                'second' => 'foo'
            ]
        ];
        $data = json_encode($data);
        $client = static::createClient();
        $client->request('POST','/api/user',[],[],[
            'Accept'=>'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ],$data);
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED,$response->getStatusCode());
        ResponseAsserter::assertResponsePropertiesExist($response,['username']);
    }

    public function testCreateError()
    {
        $data = [
            'fullname' => 'Hello World',
            'username' => 'test_create',
            'email' => 'foo@bar.com',
            'plainPassword' => [
                'first' => 'foo',
                'second' => 'bar'
            ]
        ];
        $data = json_encode($data);
        $client = static::createClient();
        $client->request('POST','/api/user',[],[],[
            'Accept'=>'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ],$data);
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_BAD_REQUEST,$response->getStatusCode());
        ResponseAsserter::assertResponsePropertiesExist($response,['data.username']);
    }

    public function testGet()
    {
        $helper = $this->getService('demo.test.user_helper');
        $user = $helper->create('toni','foo');

        $client = static::createClient();
        $client->request('GET', '/api/user/toni',[],[],[
            'Accept'=>'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);
        $response = $client->getResponse();


        $this->assertEquals(200,$response->getStatusCode());
        ResponseAsserter::assertResponsePropertiesExist($response,['username']);
    }
}
