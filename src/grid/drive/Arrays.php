<?php
/**
 * Created by PhpStorm.
 * User: rocky
 * Date: 2021-01-12
 * Time: 23:43
 */

namespace Eadmin\grid\drive;


use Eadmin\contract\GridInterface;
use think\Collection;


class Arrays implements GridInterface
{

    protected $data = [];

    public function __construct($data)
    {
        if (is_array($data)) {
            $data = Collection::make($data);
        }
        $this->data = $data;
    }

    public function getData(bool $hidePage, int $page, int $size)
    {
        if ($hidePage) {
            return $this->data;
        } else {
            $page = ($page - 1) * $size;
            $data = $this->data->slice($page, $size);
            return $data;
        }
    }

    public function getTotal(): int
    {
        return $this->data->count();
    }

    /**
     * 是否有回收站
     * @return bool
     */
    public function trashed()
    {
        return false;
    }

    public function db()
    {
        return null;
    }

    public function model()
    {
        return null;
    }

    public function getPk()
    {
        return 'id';
    }

    public function destroy($ids)
    {
        return true;
    }

    /**
     * @param string $sortField
     */
    public function sortField(string $sortField): void
    {

    }

    public function update(array $ids, array $data)
    {
        // TODO: Implement update() method.
        return true;
    }

    public function quickFilter($keyword, $columns)
    {

    }
    public function realiton($relation)
    {
        // TODO: Implement realiton() method.
    }
}
