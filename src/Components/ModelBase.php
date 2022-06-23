<?php

namespace SmallRuralDog\AmisAdmin\Components;

use Illuminate\Contracts\Database\Query\Builder;

trait ModelBase
{
    protected string $primaryKey = 'id';

    /**
     * @return string
     */
    public function getRouteName(): string
    {
        return $this->routeName;
    }

    /**
     *  获取批量选择key标识
     * @return string
     */
    public function getBulkSelectIds(): string
    {
        return '${ids|raw}';
    }

    /**
     * 设置模型主键
     * @param string $primaryKey
     * @return self
     */
    public function setPrimaryKey(string $primaryKey): self
    {
        $this->primaryKey = $primaryKey;
        return $this;
    }


    public function getPrimaryKey(): string
    {
        if ($this->primaryKey === 'id' && $this->isMongoDB()) {
            return "_id";
        }
        return $this->primaryKey;
    }


    public function builder(): Builder
    {
        return $this->builder;
    }

    public function getIndexUrl($parameters = []): string
    {
        return route($this->getRouteName() . '.index', $parameters, false);
    }

    public function getCreateUrl($parameters = []): string
    {
        return route($this->getRouteName() . '.create', $parameters, false);
    }


    public function getStoreUrl($parameters = []): string
    {
        return 'post:' . route($this->getRouteName() . '.store', $parameters, false);
    }

    public function getShowUrl($key, $parameters = []): string
    {
        return route($this->getRouteName() . '.index', $parameters, false) . '/' . $key;
    }

    public function getEditUrl($key, $parameters = []): string
    {
        return route($this->getRouteName() . '.index', $parameters, false) . '/${' . $key . '}/edit';
    }

    public function getUpdateUrl($key): string
    {
        return 'put:' . route($this->routeName . '.index') . '/' . $key;
    }

    public function getDestroyUrl($key): string
    {
        return 'delete:' . route($this->getRouteName() . '.index') . '/' . $key;
    }
}
