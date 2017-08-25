# SPL collections sort

[![Latest Stable Version](https://poser.pugx.org/the-jj/spl-collections-sort/version)](https://packagist.org/packages/the-jj/spl-collections-sort)
[![Latest Unstable Version](https://poser.pugx.org/the-jj/spl-collections-sort/v/unstable)](//packagist.org/packages/the-jj/spl-collections-sort)
[![Build Status](https://travis-ci.org/the-JJ/SplCollectionsSort.svg?branch=master)](https://travis-ci.org/the-JJ/SplCollectionsSort)
[![Coverage Status](https://coveralls.io/repos/github/the-JJ/SplCollectionsSort/badge.svg?branch=master)](https://coveralls.io/github/the-JJ/SplCollectionsSort?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/07801e2a-4980-459f-a6a9-e2bd17d76df8/mini.png)](https://insight.sensiolabs.com/projects/07801e2a-4980-459f-a6a9-e2bd17d76df8)
[![License](https://poser.pugx.org/the-jj/spl-collections-sort/license)](https://packagist.org/packages/the-jj/spl-collections-sort)

Several methods for sorting SPL datastructures. Currently only `SplFixedArray` objects are supported. Should be fast with big arrays as well.

# Basic usage
All sorting algorithms accept `SplFixedArray` object as first argument and optional comparison callback function as second argument. Sorting is done in place.

* [Insertion sort](#insertion-sort)
* [Quicksort](#quicksort)
* [Mergesort](#mergesort)
* [Fallback to standard PHP array `sort()`](#array-sort)

## Insertion sort

```php
$splFixedArray = SplFixedArray::fromArray([5, 3, 8, 6, 0, 4]);

SplFixedArraySort::insertionSort($splFixedArray);

var_dump($splFixedArray->toArray()); // [0, 3, 4, 5, 6, 8]
```

#### Custom comparison function

```php
$splFixedArray = SplFixedArray::fromArray([5, 3, 8, 6, 0, 4]);

SplFixedArraySort::insertionSort($splFixedArray, function($a, $b) {
    if ($a < $b) return 1;
    if ($a > $b) return -1;
    return 0;
});

var_dump($splFixedArray->toArray()); // [8, 6, 5, 4, 3, 0]
```

#### Boundaries
Additionally, insertion sort accepts two more arguments - boundaries `$low` and `$high`:

```php
$splFixedArray = SplFixedArray::fromArray([5, 3, 8, 6, 0, 4]);

SplFixedArraySort::insertionSort($splFixedArray, null, 2, 4);

var_dump($splFixedArray->toArray()); // [5, 3, 0, 6, 8, 4]
```

## Quicksort
Non-recursive implementation is used, which makes it viable for sorting big arrays. Upon reaching subsets of 5 elements or less, falls back to insertion sort.

```php
$splFixedArray = SplFixedArray::fromArray([5, 3, 8, 6, 0, 4]);

SplFixedArraySort::quickSort($splFixedArray);

var_dump($splFixedArray->toArray()); // [0, 3, 4, 5, 6, 8]
```

#### Custom comparison function

```php
$splFixedArray = SplFixedArray::fromArray([5, 3, 8, 6, 0, 4]);

SplFixedArraySort::quickSort($splFixedArray, function($a, $b) {
    if ($a < $b) return 1;
    if ($a > $b) return -1;
    return 0;
});

var_dump($splFixedArray->toArray()); // [8, 6, 5, 4, 3, 0]
```

## Mergesort
A bottom-up (non-recursive) implementation is used.

Usage? You guessed it:

```php
$splFixedArray = SplFixedArray::fromArray([5, 3, 8, 6, 0, 4]);

SplFixedArraySort::mergeSort($splFixedArray);

var_dump($splFixedArray->toArray()); // [0, 3, 4, 5, 6, 8]
```

#### Custom comparison function

```php
$splFixedArray = SplFixedArray::fromArray([5, 3, 8, 6, 0, 4]);

SplFixedArraySort::mergeSort($splFixedArray, function($a, $b) {
    if ($a < $b) return 1;
    if ($a > $b) return -1;
    return 0;
});

var_dump($splFixedArray->toArray()); // [8, 6, 5, 4, 3, 0]
```

## Array sort

There is one more sorting method - _array sort_ that uses PHP's `sort()` (or `usort()`) method. Usage is same as with the above algorithms:

```php
$splFixedArray = SplFixedArray::fromArray([5, 3, 8, 6, 0, 4]);

SplFixedArraySort::arraySort($splFixedArray);

var_dump($splFixedArray->toArray()); // [0, 3, 4, 5, 6, 8]
```

#### Custom comparison function

```php
$splFixedArray = SplFixedArray::fromArray([5, 3, 8, 6, 0, 4]);

SplFixedArraySort::arraySort($splFixedArray, function($a, $b) {
    if ($a < $b) return 1;
    if ($a > $b) return -1;
    return 0;
});

var_dump($splFixedArray->toArray()); // [8, 6, 5, 4, 3, 0]
```

# Why?
When using a regular PHP array, we are offered a vast array _(no pun intended)_ of [sorting functions](https://secure.php.net/manual/en/array.sorting.php). However, no such thing is offered for `SplFixedArray` objects in vanilla PHP.

This _library_ is to change that. Currently it offers several methods for sorting `SplFixedArray` objects. In the future, I might add support for other structures (that make sense to be sorted, like doubly-linked list?).

# Installation

```
composer require the-jj/spl-collections-sort
```
