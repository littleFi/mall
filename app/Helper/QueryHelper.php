<?php
namespace App\Helper;

use Illuminate\Database\Query\Expression;
use InvalidArgumentException;

class QueryHelper
{
    /**
     * 选择处理
     *
     * 支持简单的主表和关联子表字段选择过滤
     * 选择字段示例：['id', 'type', 'status', 'sku.id', 'sku.status']
     *
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $qb
     * @param array $fields
     *
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    public static function select($qb, array $fields)
    {
        $groups = [];

        foreach ($fields as $field) {
            if (($pos = strrpos($field, '.')) !== false) {
                $relation = substr($field, 0, $pos);
                $column = substr($field, $pos + 1);
            } else {
                $relation = '+';
                $column = $field;
            }

            $groups[$relation][] = $column;
        }

        if (!isset($groups['+'])) {
            throw new InvalidArgumentException('没有选择主表字段');
        }

        $qb->select(array_pull($groups, '+'));

        foreach ($groups as $relation => $columns) {
            $qb->with([$relation => function ($qb) use ($columns) {
                $qb->select($columns);
            }]);
        }

        return $qb;
    }

    /**
     * 过滤处理
     *
     * 支持简单的字段查询条件过滤
     * 可用操作符： '=', '<', '>', '<=', '>=', '<>', '!=', '<=>',
     *             'like', 'like binary', 'not like', 'ilike',
     *             '&', '|', '^', '<<', '>>',
     *             'rlike', 'regexp', 'not regexp',
     *             '~', '~*', '!~', '!~*', 'similar to',
     *             'not similar to', 'not ilike', '~~*', '!~~*',
     *             'in', 'not in'
     *
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $qb
     * @param array $filters
     *
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    public static function filter($qb, array $filters)
    {
        foreach ($filters as $key => $value) {
            [$column, $operator] = strrpos($key, '|') !== false ? explode('|', $key, 2) : [$key, '='];

            if (!isset($filterables[$column])) {
                continue;
            }

            if (($pos = strrpos($column, '.')) !== false) {
                $relation = substr($column, 0, $pos);
                $column = substr($column, $pos + 1);

                $qb->whereHas($relation, function ($qb) use ($column, $operator, $value) {
                    static::applyFilterToQueryBuilder($qb, $column, $operator, $value);
                });

                continue;
            }

            static::applyFilterToQueryBuilder($qb, $column, $operator, $value);
        }

        return $qb;
    }

    /**
     * 应用排序
     *
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $qb
     * @param string $column
     * @param string $operator
     * @param mixed $value
     *
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    protected static function applyFilterToQueryBuilder($qb, $column, $operator, $value)
    {
        if (in_array($operator, ['in', 'not in'])) {
            $qb->whereIn($column, $value, 'and', $operator == 'not in');
        } else {
            $qb->where($column, $operator, $value);
        }

        return $qb;
    }

    /**
     * 排序处理
     *
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $qb
     * @param array $orderBy
     *
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    public static function orderBy($qb, array $orderBy)
    {
        foreach ($orderBy as $column => $direction) {

            $qb->orderBy($column, $direction);
        }

        return $qb;
    }

    /**
     * 限制处理
     *
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $qb
     * @param int $skip
     * @param int $limit
     * @param int $maxLimit
     * @param bool $forceLimit
     *
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    public static function skipLimit($qb, $skip, $limit, $maxLimit = 100, $forceLimit = true)
    {
        if ($maxLimit > 0) {
            $limit = min($limit, $maxLimit);

            if ($forceLimit) {
                $limit = max($limit, 1);
            }
        }

        $qb->skip($skip)->limit($limit);

        return $qb;
    }

    /**
     * 分页处理
     *
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $qb
     * @param int $page
     * @param int $perPage
     * @param int $maxLimit
     * @param bool $forceLimit
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public static function forPage($qb, $page, $perPage, $maxLimit = 100, $forceLimit = true)
    {
        $page = max($page, 1);

        return static::skipLimit($qb, ($page - 1) * $perPage, $perPage, $maxLimit, $forceLimit);
    }

    /**
     * 生成 SQL 占位符
     *
     * @param array $values
     *
     * @return string
     */
    public static function parameterize(array $values)
    {
        return implode(', ', array_map(function ($value) {
            return $value instanceof Expression ? $value->getValue() : '?';
        }, $values));
    }
}
