<?php namespace Sofa\GlobalScope;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as Query;
use Illuminate\Database\Eloquent\ScopeInterface;

abstract class GlobalScope implements ScopeInterface
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    abstract public function apply(Builder $builder, Model $model);

    /**
     * Determine whether where clause is the contraint applied by this scope.
     *
     * @param  array  $where Single element from the Query\Builder::$wheres array.
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return boolean
     */
    abstract public function isScopeConstraint(array $where, Model $model);

    /**
     * Remove the scope from the given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function remove(Builder $builder, Model $model)
    {
        $query = $builder->getQuery();

        $bindingKey = 0;

        foreach ((array) $query->wheres as $key => $where) {
            $bindingsCount = $this->countBindings($where);

            if ($this->isScopeConstraint($where, $model)) {
                $this->removeWhere($query, $key);

                $this->removeBindings($query, $bindingKey, $bindingsCount);
            } else {
                $bindingKey += $bindingsCount;
            }
        }
    }

    /**
     * Remove where clause from the query builder.
     *
     * @param  \Illuminate\Database\Query\Builder $query
     * @param  integer $key
     * @return void
     */
    protected function removeWhere(Query $query, $key)
    {
        array_splice($query->wheres, $key, 1);
    }

    /**
     * Remove bindings from the query builder.
     *
     * @param  \Illuminate\Database\Query\Builder $query
     * @param  integer $key
     * @param  integer $count
     * @return void
     */
    protected function removeBindings(Query $query, $key, $count)
    {
        $bindings = $query->getRawBindings()['where'];

        array_splice($bindings, $key, $count);

        $query->setBindings($bindings, 'where');
    }

    /**
     * Get number of bindings provided for a where clause.
     *
     * @param  array  $where
     * @return integer
     */
    protected function countBindings(array $where)
    {
        $type = strtolower($where['type']);

        if ($this->isHasWhere($where, $type)) {
            return substr_count($where['column'].$where['value'], '?');

        } elseif (in_array($type, ['basic', 'date', 'year', 'month', 'day'])) {
            return (int) !($where['value'] instanceof Expression);

        } elseif (in_array($type, ['null', 'notnull'])) {
            return 0;

        } elseif ($type === 'between') {
            return 2;

        } elseif (in_array($type, ['in', 'notin'])) {
            return count($where['values']);

        } elseif ($type === 'raw') {
            return substr_count($where['sql'], '?');

        } elseif (in_array($type, ['nested', 'sub', 'exists', 'notexists', 'insub', 'notinsub'])) {
            return count($where['query']->getBindings());
        }
    }

    /**
     * Determine whether where clause is an eloquent 'has' subquery.
     *
     * @param  array  $where
     * @param  string $type
     * @return boolean
     */
    protected function isHasWhere($where, $type)
    {
        return $type === 'basic'
                && $where['column'] instanceof Expression
                && $where['value'] instanceof Expression;
    }
}
