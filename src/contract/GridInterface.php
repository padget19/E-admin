<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-01-12
 * Time: 23:44
 */

namespace Eadmin\contract;


interface GridInterface
{
    public function __construct($data);

    public function getPk();

    //数据总条数
    public function getTotal(): int;

    //快捷搜索
    public function quickFilter($keyword, $columns);

    public function db();

    public function model();

    //更新
    public function update(array $ids, array $data);

    //删除
    public function destroy($ids);

    //设置排序字段
    public function sortField(string $field);

    public function trashed();
    public function realiton($relation);
    /**
     * 获取数据
     * @param bool $hidePage 是否分页
     * @param int $page 第几页
     * @param int $size 每页总数
     * @return mixed
     */
    public function getData(bool $hidePage, int $page, int $size);
}
