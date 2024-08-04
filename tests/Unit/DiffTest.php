<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\DiffService;

class DiffTest extends TestCase
{
    public function test_text_diff_works_on_same_strings()
    {
        $diffService = new DiffService();
        
        $oldText = "The quick brown fox jumps over the lazy dog";
        $newText = "The quick brown fox jumps over the lazy dog";
        
        $expectedDiff = json_encode(
            explode(" ", "The quick brown fox jumps over the lazy dog")
        );
        
        $this->assertEquals($expectedDiff, $diffService->text_diff_strings($oldText, $newText));
    }

    public function test_text_diff_works_on_different_strings()
    {
        $diffService = new DiffService();
        
        $oldText = "The quick brown fox jumps over the lazy dog";
        $newText = "The quick brown fox leaps over the lazy cat";
        
        $expectedDiff = json_encode(array_merge(
            explode(" ", "The quick brown fox"),
            [["d" => ["jumps"], "i" => ["leaps"]]],
            explode(" ", "over the lazy"),
            [["d" => ["dog"], "i" => ["cat"]]]
        ));
        
        $this->assertEquals($expectedDiff, $diffService->text_diff_strings($oldText, $newText));
    }

    public function test_set_diff_works_on_same_sets()
    {
        $diffService = new DiffService();
        
        $oldSet = ["apple", "banana", "cherry"];
        $newSet = ["apple", "banana", "cherry"];
        
        $expectedDiff = json_encode([
            's' => ["apple", "banana", "cherry"],
            'i' => [],
            'd' => []
        ]);
        
        $this->assertEquals($expectedDiff, $diffService->set_diff($oldSet, $newSet));
    }


    public function test_set_diff_works_on_different_sets()
    {
        $diffService = new DiffService();
        
        $oldSet = ["apple", "banana", "cherry"];
        $newSet = ["banana", "cherry", "date", "fig"];
        
        $expectedDiff = json_encode([
            's' => ["banana", "cherry"],
            'i' => ["date", "fig"],
            'd' => ["apple"]
        ]);
        
        $this->assertEquals($expectedDiff, $diffService->set_diff($oldSet, $newSet));
    }

}
