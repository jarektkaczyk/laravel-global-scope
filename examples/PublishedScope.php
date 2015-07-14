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
    // This remains unchanged - it's the only method that needs to be implemented
    public function apply(Builder $builder, Model $model)
    {
        $column = $model->getQualifiedPublishedAtColumn();

        $builder->where($column, '=', 1);

        $this->addWithDrafts($builder);
    }

    // Remains unchanged - it's just a helper method
    protected function addWithDrafts(Builder $builder)
    {
        $builder->macro('withDrafts', function (Builder $builder) {
            $this->remove($builder, $builder->getModel());

            return $builder;
        });
    }
}
