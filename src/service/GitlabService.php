<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2020-12-17
 * Time: 23:39
 */

namespace Eadmin\service;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Eadmin\Service;

class GitlabService extends Service
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://gitlab.my8m.com/api/v4/',
            'verify'   => false,
        ]);
    }

    /**
     * 返回群组下的项目
     * @param int $groupId
     * @param string $search 搜索关键字
     * @param int $page 第几页
     * @param int $size 每页大小
     * @return mixed
     */
    public function getGroupProject($groupId, $search = '', $page = 1, $size = 20)
    {
        $response = $this->client->get("groups/{$groupId}/projects", [
            'query' => [
                'simple'   => true,
                'page'     => $page,
                'per_page' => $size,
                'search'   => $search
            ]
        ]);
        $content  = $response->getBody()->getContents();
        return json_decode($content, true);
    }

    /**
     * 获取文件内容
     * @param int $projectId 项目id
     * @param string $file 文件路径
     * @return mixed
     */
    public function getFile($projectId, $file)
    {
        try {
            $response = $this->client->get("projects/{$projectId}/repository/files/{$file}?ref=eadmin");
            $res      = $response->getBody()->getContents();
            $res      = json_decode($res, true);
            return base64_decode($res['content']);
        } catch (RequestException $e) {
            return '';
        }
    }
}
