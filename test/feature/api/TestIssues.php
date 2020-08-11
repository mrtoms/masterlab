<?php


namespace main\test\featrue\api;


use main\test\BaseApiTestCase;

class TestIssues extends BaseApiTestCase
{
    public static $clean = [];

    /**
     * @throws \Exception
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
    }

    /**
     * @throws \Exception
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
    }

    /**
     * @param string $name
     * @return array
     */
    public function addIssue($name = '这个事项')
    {
        $accessToken = '';
        $projectId = self::$projectId;
        $client = new \GuzzleHttp\Client();
        $url = ROOT_URL . 'api/issue/issues/v1/?access_token=' . $accessToken;

        $response = $client->post($url, [
            'form_params' => [
                'params' => [
                    'project_id' => $projectId,
                    'summary' => $name . quickRandomStr(50),
                    'issue_type' => 1,
                    'priority' => 1,
                    'description' => '描述1：' . quickRandomStr(),
                    'assignee' => 1,
                    'reporter' => 1,
                ]
            ]
        ]);
        $rawResponse = $response->getBody()->getContents();
        $respArr = json_decode($rawResponse, true);
        $newId = $respArr['data']['body']['id'];
        return [
            'id' => $newId,
            'body' => $respArr['data']['body'],
            'resp' => $respArr,
        ];
    }

    /**
     * 添加事项
     * @throws \Exception
     */
    public function testPost()
    {
        $ret = $this->addIssue();
        $respArr = $ret['resp'];
        $this->assertNotEmpty($respArr, '接口请求失败');
        $this->assertTrue(isset($respArr['data']), '不包含data属性');
        $this->assertEquals('200', $respArr['ret']);
        $respData = $respArr['data'];
        $this->assertNotEmpty($respData);

    }

    /**
     * @throws \Exception
     */
    public function testGet()
    {
        $accessToken = '';
        $projectId = self::$projectId;

        $ret = $this->addIssue();
        $newId = $ret['id'];

        $url = ROOT_URL.'/api/issue/issues/v1/'.$newId.'?access_token=' . $accessToken;
        $client = new \GuzzleHttp\Client();
        $response = $client->get($url);
        $rawResponse = $response->getBody()->getContents();
        $respArr = json_decode($rawResponse, true);
        $this->assertNotEmpty($respArr, '接口请求失败');
        $this->assertTrue(isset($respArr['data']), '不包含data属性');
        $this->assertEquals('200', $respArr['ret']);
        $respData = $respArr['data'];
        $this->assertNotEmpty($respData);
        if (strpos($respData['body']['issue']['summary'],'这个事项') !== false) {
            $this->assertTrue(true); //包含
        } else {
            $this->assertTrue(false);
        }

        $url = ROOT_URL . 'api/issue/issues/v1/?access_token=' . $accessToken . '&project=' . $projectId;
        $response = $client->get($url);
        $rawResponse = $response->getBody()->getContents();
        $respArr = json_decode($rawResponse, true);
        $this->assertNotEmpty($respArr, '接口请求失败');
        $this->assertTrue(isset($respArr['data']), '不包含data属性');
        $this->assertEquals('200', $respArr['ret']);
        $respData = $respArr['data'];
        $this->assertNotEmpty($respData);
    }

    /**
     *
     * @throws \Exception
     */
    public function testDelete()
    {
        $projectId = self::$projectId;
        $accessToken = '';

        $ret = $this->addModule();
        $newLabelId = $ret['id'];

        $client = new \GuzzleHttp\Client();
        $url = ROOT_URL . 'api/modules/v1/' . $newLabelId . '?access_token=' . $accessToken. '&project_id=' . $projectId;
        $response = $client->delete($url);
        $rawResponse = $response->getBody()->getContents();
        $respArr = json_decode($rawResponse, true);
        $this->assertNotEmpty($respArr, '接口请求失败');
    }

    /**
     * @throws \Exception
     */
    public function testPatch()
    {
        $projectId = self::$projectId;
        $accessToken = '';

        $ret = $this->addModule();
        $newLabelId = $ret['id'];

        $client = new \GuzzleHttp\Client();
        $url = ROOT_URL . 'api/modules/v1/' . $newLabelId . '?access_token=' . $accessToken. '&project_id=' . $projectId;
        $response = $client->patch($url, [
            'form_params' => [
                'module_name' => 'new' . quickRandomStr(50),
                'description' => 'new描述1：' . quickRandomStr(),
            ]
        ]);
        //$response = $client->request('PUT', $url, ['body' => 'foo']);
        $rawResponse = $response->getBody()->getContents();
        $respArr = json_decode($rawResponse, true);
        $this->assertNotEmpty($respArr, '接口请求失败');
    }


}