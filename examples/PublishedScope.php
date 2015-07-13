<?php namespace Sofa\Eloquent\Scopes;

use Sofa\GlobalScope\GlobalScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Example definition of a Global Scope based on
 * @link https://github.com/jarektkaczyk/laravel5-global-scope-example/blob/laravel5-global-scope-example/Sofa/Eloquent/Scopes/PublishedScope.php
 */
class PublishedScope extends GlobalScope
{
    // This method remains unchanged
    public function apply(Builder $builder, Model $model)
    {
        $column = $model->getQualifiedPublishedAtColumn();

        $builder->where($column, '=', 1);

        $this->addWithDrafts($builder);
    }

    // This method is just renamed from isPublishedConstraint
    public function isScopeConstraint(array $where, Model $model)
    {
        $column = $model->getQualifiedPublishedAtColumn();

        return $where['type'] == 'Basic'&& $where['column'] == $column;
    }

    // Remains unchanged
    protected function addWithDrafts(Builder $builder)
    {
        $builder->macro('withDrafts', function (Builder $builder) {
            $this->remove($builder, $builder->getModel());

            return $builder;
        });
    }
}
