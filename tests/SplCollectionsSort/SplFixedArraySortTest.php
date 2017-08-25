<?php

use SplCollectionsSort\SplFixedArraySort;

class SplFixedArraySortTest extends \PHPUnit\Framework\TestCase
{
    const ALGORITHMS = ["insertionSort", "quickSort", "mergeSort", "arraySort"];

    const TESTCASES = [
        // empty array
        [],
        // array with only one element
        [100],
        // array with two elements
        [1, 0],
        // already sorted array
        [1, 2, 3, 4, 5],
        // edge test cases:
        [1, 2, 1, 1, 1],
        [2, 2, 1, 1, 2],
        // random test cases:
        [2, 4, 6, 3, 8, 1],
        [2, -4, -3, 6, -8, 1],
        [2, -4, -3, -3, -3, 6, 1, 1, 1, 1, 1, 1, 1, 1, -5, -5, -3, -3, -3, -5, -5, -5, -5, -5, 1],
        [5, 3, 8, 6, 1, 0, 4],
        [-801, 922, -259, -479, 65, -575, -338, -18, -977, -464, -836],
        [18, -64, 94, 72, -77, 11, 62, 38, -46, -8],
        [-89, 44, 27, -28, -62, -90, 8, 29, -45, 95, 85, 6, 27, 24, 86, 78, -28, 27, 94, -61, -2, -89, -32, -86, -98, 35, 59, 41, -25, -12, -50, -69, 32, 20, -49, -77, 84, 43, -83]
    ];

    public function testPartialInsertionSort()
    {
        // create SplFixedArray
        $splArray = \SplFixedArray::fromArray([18, -64, 94, 72, -77, 11, 62, 38, -46, -8]);

        // tested method
        SplFixedArraySort::insertionSort($splArray, null, 2, 6);

        // should be equal
        $this->assertEquals([18, -64, -77, 11, 62, 72, 94, 38, -46, -8], $splArray->toArray());
    }

    private function helperTestSplFixedArraySort(array $array, callable $method)
    {
        // create SplFixedArray
        $splArray = \SplFixedArray::fromArray($array);

        // sort original array (for comparison)
        sort($array);

        // tested method
        $method($splArray);

        // should be equal
        $this->assertEquals($array, $splArray->toArray(), $method[1] . PHP_EOL . var_export($array, true));
    }

    public function testAllSorts()
    {
        foreach (self::ALGORITHMS as $algorithm) {
            foreach (self::TESTCASES as $testArray) {
                $this->helperTestSplFixedArraySort($testArray, [SplFixedArraySort::class, $algorithm]);
            }
        }
    }

    private function helperTestSplFixedArraySortComparable(array $array, callable $method, callable $comparable)
    {
        // create SplFixedArray
        $splArray = \SplFixedArray::fromArray($array);

        // sort original array (for comparison)
        usort($array, $comparable);

        // tested method
        $method($splArray, $comparable);

        // should be equal
        $this->assertEquals($array, $splArray->toArray(), $method[1]);
    }

    public function testAllSortsComparable()
    {
        $comparables = [
            function($a, $b) {
                if ($a > $b) {
                    return -1;
                }
                if ($a < $b) {
                    return 1;
                }
                return 0;
            },

        ];

        foreach (self::ALGORITHMS as $algorithm) {
            foreach (self::TESTCASES as $testArray) {
                foreach ($comparables as $comparable) {
                    $this->helperTestSplFixedArraySortComparable(
                        $testArray,
                        [SplFixedArraySort::class, $algorithm],
                        $comparable
                    );
                }
            }
        }
    }
}
