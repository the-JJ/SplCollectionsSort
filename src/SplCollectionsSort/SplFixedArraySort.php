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
     * The number of elements for which insertion sort is used in of quicksort.
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

        $index = 0;
        foreach ($array as $item) {
            $sfa[$index++] = $item;
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
            $high = $stack->pop();
            $low = $stack->pop();
            if ($high <= $low) {
                return;
            }

            if ($high - $low < static::THRESHOLD_INSERTIONSORT) {
                // insertion sort
                static::insertionSort($sfa, $comparison, $low, $high);
                continue;
            }

            $pivotIndex = self::quickSortPartition($sfa, $comparison, $low, $high);

            if ($pivotIndex+1 < $high) {
                $stack->push($pivotIndex + 1);
                $stack->push($high);
            }
            if ($pivotIndex > $low) {
                $stack->push($low);
                $stack->push($pivotIndex);
            }
        }
    }

    private static function quickSortPartition(SplFixedArray $sfa, callable $comparison, $low, $high)
    {
        $pivot = $sfa[$low + (($high - $low) >> 1)];
        $left = $low - 1;
        $right = $high + 1;

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
        for($len = 1; $len < $count; $len <<= 1) {
            for($low = 0; $low < $count - $len; $low += $len << 1) {
                $mid = $low + $len - 1;
                $high = min(($len << 1) + $low - 1, $count - 1);
                self::mergeSortMerge($sfa, $temp, $low, $mid, $high, $comparison);
            }
        }
    }

    private static function mergeSortMerge(SplFixedArray $sfa, SplFixedArray $temp, $low, $mid, $high, callable $comparison)
    {
        // copy to $temp
        for ($k = $low; $k <= $high; $k++) {
            $temp[$k] = $sfa[$k];
        }

        // merge back to $sfa
        $left = $low; $right = $mid + 1;
        for($k = $low; $k <= $high; $k++) {
            if ($left > $mid) {
                $sfa[$k] = $temp[$right++];
                continue;
            }
            if ($right > $high) {
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
     * @param int $low Lower boundary for subarray sorting
     * @param int|null $high Upper boundary for subarray sorting. If null, will be set to count($sfa)
     */
    public static function insertionSort(SplFixedArray $sfa, callable $comparison = null, $low = 0, $high = null)
    {
        $count = count($sfa);
        if (is_null($high)) {
            $high = $count-1;
        }

        if ($high-$low < 1) {
            return;
        }

        if (is_null($comparison)) {
            $comparison = [static::class, "comparison"];
        }

        for($i = $low; $i <= $high; $i++) {
            $element = $sfa[$i];
            $j = $i;

            while($j > $low && $comparison($sfa[$j-1], $element) > 0) {
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
