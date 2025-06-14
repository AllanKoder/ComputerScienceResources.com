<?php

return [
    'max_replies' => 150, // Max replies for a comment
    'max_depth' => 7, // Count of comment + replies.
    'pagination_limit' => 150, // Max amount of comments (including replies) allowed to be returned to the page each 'view comments' button press
    'default_pagination_limit' => 5, // Max amount of comments to reply (can be over)
    'commentable_keys' => ['review', 'comment', 'edit', 'resource'],
    'sortable_options' => ['latest', 'top', 'bottom', 'controversial', 'mine']
];
