<?php

return [
    'max_replies' => 150,
    'max_depth' => 7, // 1-indexed, 1 is the start
    'pagination_limit' => 150,
    'default_pagination_limit' => 5,
    'commentable_types_shorthand' => ['review', 'comment', 'edit', 'resource'],
    'sortable_options' => ['latest', 'top', 'bottom', 'controversial', 'mine']
];