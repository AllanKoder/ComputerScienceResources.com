<?php

// Thanks to these sources:
// https://github.com/paulgb/simplediff
// https://en.wikipedia.org/wiki/Longest_common_subsequence

namespace App\Services;

class DiffService {
    private function removing_same_prefix_suffix($old, $new) {
        $traverseLength = min(count($old), count($new));
        $samePrefix = 0;
        for ($i = 0; $i < floor($traverseLength/2); $i++) {
            if ($old[$i] != $new[$i]) {
                break;
            }
            $samePrefix = $i;
        }
    
        $sameSuffix = 0;
        for ($i = 0; $i < floor($traverseLength/2); $i++) {
            if ($old[count($old) - $i - 1] != $new[count($new) - $i - 1]) {
                break;
            }
            $sameSuffix = $i;
        }
    
        return [
            array_slice($old, 0, $samePrefix),
            array_slice($old, $samePrefix, count($old) - $sameSuffix - $samePrefix),
            array_slice($new, $samePrefix, count($new) - $sameSuffix - $samePrefix),
            array_slice($old, count($old) - $sameSuffix)
        ];
    }
    
    private function text_diff_helper($old, $new) {
        $maxLength = 0;
        $substringMap = [];
    
        $valueToIndex = [];
        foreach ($new as $nindex => $nvalue) {
            if (!isset($valueToIndex[$nvalue])) {
                $valueToIndex[$nvalue] = [];
            }
            $valueToIndex[$nvalue][] = $nindex;
        }
    
        foreach ($old as $oindex => $ovalue) {
            if (isset($valueToIndex[$ovalue])) {
                foreach ($valueToIndex[$ovalue] as $nindex) {
                    $substringLength = isset($substringMap[($oindex - 1) . ',' . ($nindex - 1)]) ? $substringMap[($oindex - 1) . ',' . ($nindex - 1)] + 1 : 1;
                    $substringMap[$oindex . ',' . $nindex] = $substringLength;
                    if ($substringLength > $maxLength) {
                        $maxLength = $substringLength;
                        $maxOldIndex = $oindex + 1 - $maxLength;
                        $maxNewIndex = $nindex + 1 - $maxLength;
                    }
                }
            }
        }
    
        if (empty($old) && empty($new)) {
            return [];
        }
        if ($maxLength == 0) {
            return [['d' => $old, 'i' => $new]];
        } else {
            return array_merge(
                $this->text_diff_helper(array_slice($old, 0, $maxOldIndex), array_slice($new, 0, $maxNewIndex)),
                array_slice($new, $maxNewIndex, $maxLength),
                $this->text_diff_helper(array_slice($old, $maxOldIndex + $maxLength), array_slice($new, $maxNewIndex + $maxLength))
            );
        }
    }
    
    private function text_diff($old, $new) {
        list($samePrefix, $oldMiddle, $newMiddle, $sameSuffix) = $this->removing_same_prefix_suffix($old, $new);
        return array_merge(
            $samePrefix,
            $this->text_diff_helper($oldMiddle, $newMiddle),
            $sameSuffix
        );
    }

    public function text_diff_strings($oldStr, $newStr) {
        $old = explode(' ', $oldStr);
        $new = explode(' ', $newStr);
        $diff = $this->text_diff($old, $new);
        return json_encode($diff);
    }
    
    public function set_diff($old, $new) {
        $oldSet = array_flip($old);
        $newSet = array_flip($new);
    
        $same = array_intersect_key($oldSet, $newSet);
        $insertions = array_diff_key($newSet, $oldSet);
        $deletions = array_diff_key($oldSet, $newSet);
    
        return json_encode([
            's' => array_keys($same),
            'i' => array_keys($insertions),
            'd' => array_keys($deletions)
        ]);
    }

    public function getModelDiff(object $originalModel, array $edits) {
        $diffs = [];
        foreach ($edits as $attribute => $editedValue) {
            $originalValue = $originalModel->$attribute;
        
            if (is_array($originalValue) && is_array($editedValue)) {
                // Get the array diff
                $diffs[$attribute] = $this->set_diff($originalValue, $editedValue);
            } elseif (is_string($originalValue) && is_string($editedValue)) {
                // Get the text diff
                $diffs[$attribute] = $this->text_diff_strings($originalValue, $editedValue);
            }
        }
        
        return $diffs;
    }
}