<?php

namespace SplCollectionsSort;


use SplFixedArray;
use SplStack;

/**
 * The SplFixedArray sort methods. Allow for sorting of SplFixedArray,
 * which should have been introduced in SPL anyway.
 *
 * @package SplCollectionsSort
 */
class SplFixedArraySort
{
    /**
     * The number of elements for which insertion sort is used in of quicksort and merge sort.
     */
    const THRESHOLD_INSERTIONSORT = 6;

    /**
     * A simple sorting method that uses builtin array sort functions.
     * @param SplFixedArray $sfa Array to sort
     * @param callable|null $comparison Callable comparison function. See
     *      https://secure.php.net/manual/en/function.usort.php and `value_compare_func` for more details.
     */
    public static function arraySort(SplFixedArray $sfa, callable $comparison = null)
    {
        $array = $sfa->toArray();
        if (is_null($comparison)) {
            sort($array);
        } else {
            usort($array, $comparison);
        }

        $i = 0;
        foreach ($array as $item) {
            $sfa[$i++] = $item;
        }
    }

    private static function swap(SplFixedArray $sfa, $i, $j)
    {
        $tmp = $sfa[$i];
        $sfa[$i] = $sfa[$j];
        $sfa[$j] = $tmp;
    }

    /**
     * Perform a non-recursive in-place quicksort.
     * Great for sorting big arrays. Implementation of quicksort that uses using SPL stack.
     * Falls back to insertion sort for subarrays of less than THRESHOLD_INSERTIONSORT elements.
     *
     * @param SplFixedArray $sfa Array to sort
     * @param callable|null $comparison Callable comparison function. See
     *      https://secure.php.net/manual/en/function.usort.php and `value_compare_func` for more details.
     */
    public static function quickSort(SplFixedArray $sfa, callable $comparison = null) {
        if (is_null($comparison)) {
            $comparison = [static::class, "comparison"];
        }

        $stack = new SplStack();
        $stack->push(0);
        $stack->push(count($sfa) - 1);

        while(!$stack->isEmpty()) {
            $hi = $stack->pop();
            $lo = $stack->pop();
            if ($hi <= $lo) {
                return;
            }

            if ($hi - $lo < static::THRESHOLD_INSERTIONSORT) {
                // insertion sort
                static::insertionSort($sfa, $comparison, $lo, $hi);
                continue;
            }

            $pivotIndex = self::quickSortPartition($sfa, $comparison, $lo, $hi);

            if ($pivotIndex+1 < $hi) {
                $stack->push($pivotIndex + 1);
                $stack->push($hi);
            }
            if ($pivotIndex > $lo) {
                $stack->push($lo);
                $stack->push($pivotIndex);
            }
        }
    }

    private static function quickSortPartition(SplFixedArray $sfa, callable $comparison, $lo, $hi)
    {
        $pivot = $sfa[$lo + (($hi - $lo) >> 1)];
        $left = $lo - 1;
        $right = $hi + 1;

        while(true) {
            while($comparison($sfa[++$left], $pivot) < 0);
            while($comparison($sfa[--$right], $pivot) > 0);
            if ($left >= $right) break;
            self::swap($sfa, $left, $right);
        }

        return $right;
    }

    /**
     * Perform a bottom-up, non-recursive in-place mergesort.
     *
     * @param SplFixedArray $sfa Array to sort
     * @param callable|null $comparison Callable comparison function. See
     *      https://secure.php.net/manual/en/function.usort.php and `value_compare_func` for more details.
     */
    public static function mergeSort(SplFixedArray $sfa, callable $comparison = null)
    {
        $count = count($sfa);
        if ($count <= 1) {
            return;
        }

        if (is_null($comparison)) {
            $comparison = [static::class, "comparison"];
        }

        $temp = new SplFixedArray($count);
        for($len = 1; $len < $count; $len *= 2) {
            for($lo = 0; $lo < $count - $len; $lo += $len +$len) {
                $mid = $lo + $len - 1;
                $hi = min($lo + $len + $len - 1, $count - 1);
                self::mergeSortMerge($sfa, $temp, $lo, $mid, $hi, $comparison);
            }
        }
    }

    private static function mergeSortMerge(SplFixedArray $sfa, SplFixedArray $temp, $lo, $mid, $hi, callable $comparison)
    {
        // copy to $temp
        for ($k = $lo; $k <= $hi; $k++) {
            $temp[$k] = $sfa[$k];
        }

        // merge back to $sfa
        $left = $lo; $right = $mid + 1;
        for($k = $lo; $k <= $hi; $k++) {
            if ($left > $mid) {
                $sfa[$k] = $temp[$right++];
                continue;
            }
            if ($right > $hi) {
                $sfa[$k] = $temp[$left++];
                continue;
            }
            if ($comparison($temp[$right], $temp[$left]) > 0) {
                $sfa[$k] = $temp[$left++];
                continue;
            }
            $sfa[$k] = $temp[$right++];
        }
    }

    /**
     * Perform an insertion sort.
     * Usually efficient for small arrays. Quicksort implementation falls back to insertion sort
     * when subarrays' sizes reach THRESHOLD_INSERTIONSORT or less elements.
     *
     * @param SplFixedArray $sfa Array to sort
     * @param callable|null $comparison Callable comparison function. See
     *      https://secure.php.net/manual/en/function.usort.php and `value_compare_func` for more details.
     * @param int $lo Lower boundary for subarray sorting
     * @param int|null $hi Upper boundary for subarray sorting. If null, will be set to count($sfa)
     */
    public static function insertionSort(SplFixedArray $sfa, callable $comparison = null, $lo = 0, $hi = null)
    {
        $count = count($sfa);
        if (is_null($hi)) {
            $hi = $count-1;
        }

        if ($hi-$lo < 1) {
            return;
        }

        if (is_null($comparison)) {
            $comparison = [static::class, "comparison"];
        }

        for($i = $lo; $i <= $hi; $i++) {
            $element = $sfa[$i];
            $j = $i;

            while($j > $lo && $comparison($sfa[$j-1], $element) > 0) {
                $sfa[$j] = $sfa[$j-1];
                $j--;
            }

            $sfa[$j] = $element;
        }
    }

    protected static function comparison($a, $b)
    {
        if ($a < $b) {
            return -1;
        }
        if ($a > $b) {
            return 1;
        }

        return 0;
    }
}
