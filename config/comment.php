<?php

return [
    'max_replies' => 150, // Max replies for a comment
    'max_depth' => 7, // Count of comment + replies.
    'pagination_limit' => 150, // Should be larger than or equal to max_replies. Max amount of comments (including replies) allowed to be returned to the page each 'view comments' button press.
    'default_pagination_limit' => 10, // ideal amount of comments to load (can be over) each 'view comments' button press
    'commentable_keys' => ['review', 'comment', 'edit', 'resource'],
    'sortable_options' => ['latest', 'top', 'bottom', 'controversial', 'mine'],
];
