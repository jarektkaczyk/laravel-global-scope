# Sofa/Laravel-Global-Scope

Easy way to define Eloquent [Global Scopes](http://softonsofa.com/laravel-5-eloquent-global-scope-how-to) in Laravel 5+.

## Introduction

Global scope in [Eloquent](http://laravel.com/docs/eloquent) is a neat feature. However, it doesn't fit the general idea in Laravel of things being easy to implement, in that it might be very hard to `remove` the scope from a query, unless you know ins and outs of the `Query\Builder`.

That being said, you'll find here short but useful abstract `GlobalScope` that your scopes will extend, and you only need to implement 2 methods: 

1. `apply` - apply any constraints on the `Eloquent\Builder` that your scope requires. 
2. `isScopeConstraint` - determine whether given `where` clause is the one applied by your scope. The `where` is passed as an array, like each element in the `Query\Builder::$wheres` array.


## Installation

Package requires **PHP 5.4+** and works with **Laravel 5+**.

1. Require the package in your `composer.json`:
    ```
        "require": {
            ...
            "sofa/laravel-global-scope": "0.1@dev",
        },

    ```


## Usage example

Let's compare this [basic scope](https://github.com/jarektkaczyk/laravel5-global-scope-example/blob/laravel5-global-scope-example/Sofa/Eloquent/Scopes/PublishedScope.php) with the [enhanced scope](https://github.com/jarektkaczyk/laravel-global-scope/blob/laravel-global-scope/examples/PublishedScope.php).


## Roadmap

 - [x] Abstract GlobalScope - `remove` is done for you 
 - [ ] Easier constraint verification 
 - [ ] Handle twisted edge-cases - multi-level nested subquery wheres 
 - [ ] Generators 
