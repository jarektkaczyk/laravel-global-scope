<?php

use Mockery as m;

class GlobalScopeTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testRemove()
    {
        $nestedQuery = m::mock('Illuminate\Database\Query\Expression');
        $nestedQuery->shouldReceive('getValue')->andReturn(
            '(select count(*) from "table" where ("table"."stub_id" = "table"."id" and "flub" = ? or "flob" = ? or "plugh" is null) and "tenant_id" = ?)'
        );
        $nestedQuery->shouldReceive('getBindings')->andReturn(['f', 'g']);

        $nestedValue = m::mock('Illuminate\Database\Query\Expression');
        $nestedValue->shouldReceive('getValue')->andReturn(1);

        $expectedWheres = [
            [
                'type'      => 'Basic',
                'column'    => 'baz',
                'value'     => 3,
            ],
            [
                'type'      => 'NotNull',
                'column'    => 'quux',
            ],
            [
                'type'      => 'Basic',
                'column'    => $nestedQuery,
                'value'     => $nestedValue,
            ],
            [
                'type'      => 'in',
                'values'    => ['d', 'e'],
            ],
            [
                'type'      => 'Nested',
                'query'     => $nestedQuery,
            ],
            ['type' => 'between'],
        ];
        $bindings = ['where' => [3, 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'foo', 'bar', 2]];

        $scopedQuery = m::mock('Illuminate\Database\Query\Builder');
        $scopedQuery->wheres = [

            [
                'type'      => 'Basic',
                'column'    => 'foo',
                'value'     => 2,
            ],
        ];

        $query = m::mock('Illuminate\Database\Query\Builder');
        $query->wheres = array_merge($expectedWheres, $scopedQuery->wheres);
        $query->shouldReceive('getRawBindings')->once()->andReturn($bindings);
        $query->shouldReceive('setBindings')->once()->andReturn([3, 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'foo', 'bar'], 'where');

        $builder = m::mock('Illuminate\Database\Eloquent\Builder');
        $builder->shouldReceive('getQuery')->times(2)->andReturn($query, $scopedQuery);

        $model = m::mock('Illuminate\Database\Eloquent\Model');
        $model->shouldReceive('newQueryWithoutScopes')->once()->andReturn($builder);

        $scope = m::mock('Sofa\GlobalScope\GlobalScope[apply]');
        $scope->shouldReceive('apply')->once()->with($builder, $model);

        $scope->remove($builder, $model);

        $this->assertEquals($expectedWheres, $query->wheres);
    }
}
