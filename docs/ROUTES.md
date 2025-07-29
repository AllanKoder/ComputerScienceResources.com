
# Application Routes & UI Previews

This document groups the main routes of ComputerScienceResources.com by category, with visual references for each. Screenshots or mockups should be stored in `docs/routes-images/`.

---

## Table of Contents
- [Resource-Related Routes](#resource-related-routes)
- [Comment-Related Routes](#comment-related-routes)
- [Voting Routes](#voting-routes)
- [Review Routes](#review-routes)
- [Other Routes](#other-routes)

---

## Resource-Related Routes

- `/` — Resources (redirects to resource index)
  - ![Resources](routes-images/resources.png)
  - Filter Bar
    ![Resource Filter Bar](routes-images/resources-filter-bar.png)
- `/about` — About page
  - ![About](routes-images/about-us.png)
- `/resources/create` — Create a new resource
  - ![Create Resource](routes-images/create-resource.png)
- `/resources/{slug}` — Resource details (reviews, comments, tags)
  - ![Resource Details Reviews](routes-images/resource-reviews.png)
  - ![Resource Details Comments](routes-images/resource-comments.png)
  - ![Resource Details Proposed Edits](routes-images/resource-proposed-edits.png)
- `/resource/{slug}/edit/create` — Propose edits to a resource
  - ![Edit Resource](routes-images/edits-create.png)
- `/resource/edit/{slug}` — View pending edits for a resource
  - ![Pending Edits Split Diff](routes-images/edits-show.png)
  - ![Pending Edits Unified View](routes-images/edits-show-diff.png)



## Comment-Related Routes

- `/comments/show/{commentableKey}/{commentableId}/{index}/{paginationLimit?}` — Show paginated comments for a model
  - ![Comments](routes-images/comments.png)
- `/comments` — Post a new comment (API)



## Voting Routes

- `/upvote/{typeKey}/{id}` — Upvote a resource, review, or comment (API)
  - ![Upvote Example](routes-images/votes-upvote.png)
- `/downvote/{typeKey}/{id}` — Downvote a resource, review, or comment (API)
  - ![Downvote Example](routes-images/votes-downvote.png)


## Review Routes

- `/reviews/{computerScienceResource}` — Post or update a review (API)
  - ![Write Review](routes-images/create-review.png)



## Other Routes

- `/tags/search/{query}` — API endpoint for searching tags
  - ![Tag Search](routes-images/tag-search.png)

---

> To add or update screenshots, place image files in `docs/routes-images/` and update the image links above as needed.
