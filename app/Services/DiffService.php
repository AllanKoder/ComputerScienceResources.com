<?php

// Thanks to these sources:
// https://github.com/paulgb/simplediff
// https://en.wikipedia.org/wiki/Longest_common_subsequence

namespace App\Services;

class DiffService {
    private function removing_same_prefix_suffix($old, $new) {
        $traverse_length = min(count($old), count($new));
        $same_prefix = 0;
        for ($i = 0; $i < floor($traverse_length/2); $i++) {
            if ($old[$i] != $new[$i]) {
                break;
            }
            $same_prefix = $i;
        }
    
        $same_suffix = 0;
        for ($i = 0; $i < floor($traverse_length/2); $i++) {
            if ($old[count($old) - $i - 1] != $new[count($new) - $i - 1]) {
                break;
            }
            $same_suffix = $i;
        }
    
        return [
            array_slice($old, 0, $same_prefix),
            array_slice($old, $same_prefix, count($old) - $same_suffix - $same_prefix),
            array_slice($new, $same_prefix, count($new) - $same_suffix - $same_prefix),
            array_slice($old, count($old) - $same_suffix)
        ];
    }
    
    private function text_diff_helper($old, $new) {
        $max_length = 0;
        $substring_map = [];
    
        $value_to_index = [];
        foreach ($new as $nindex => $nvalue) {
            if (!isset($value_to_index[$nvalue])) {
                $value_to_index[$nvalue] = [];
            }
            $value_to_index[$nvalue][] = $nindex;
        }
    
        foreach ($old as $oindex => $ovalue) {
            if (isset($value_to_index[$ovalue])) {
                foreach ($value_to_index[$ovalue] as $nindex) {
                    $substring_length = isset($substring_map[($oindex - 1) . ',' . ($nindex - 1)]) ? $substring_map[($oindex - 1) . ',' . ($nindex - 1)] + 1 : 1;
                    $substring_map[$oindex . ',' . $nindex] = $substring_length;
                    if ($substring_length > $max_length) {
                        $max_length = $substring_length;
                        $max_oindex = $oindex + 1 - $max_length;
                        $max_nindex = $nindex + 1 - $max_length;
                    }
                }
            }
        }
    
        if (empty($old) && empty($new)) {
            return [];
        }
        if ($max_length == 0) {
            return [['d' => $old, 'i' => $new]];
        } else {
            return array_merge(
                $this->text_diff_helper(array_slice($old, 0, $max_oindex), array_slice($new, 0, $max_nindex)),
                array_slice($new, $max_nindex, $max_length),
                $this->text_diff_helper(array_slice($old, $max_oindex + $max_length), array_slice($new, $max_nindex + $max_length))
            );
        }
    }
    
    private function text_diff($old, $new) {
        list($same_prefix, $old_middle, $new_middle, $same_suffix) = $this->removing_same_prefix_suffix($old, $new);
        return array_merge(
            $same_prefix,
            $this->text_diff_helper($old_middle, $new_middle),
            $same_suffix
        );
    }

    public function text_diff_strings($old_str, $new_str) {
        $old = explode(' ', $old_str);
        $new = explode(' ', $new_str);
        $diff = $this->text_diff($old, $new);
        return json_encode($diff);
    }
    
    public function set_diff($old, $new) {
        $old_set = array_flip($old);
        $new_set = array_flip($new);
    
        $same = array_intersect_key($old_set, $new_set);
        $insertions = array_diff_key($new_set, $old_set);
        $deletions = array_diff_key($old_set, $new_set);
    
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