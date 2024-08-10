<h1>Resource Reviews</h1>

@if(Auth::user())
    @include('reviews.resources.create', array('resource'=>$resource))
@endif

@foreach($resourceReviews as $review)
    <table class="table">
        <thead>
            <tr>
                <th>Community Size</th>
                <th>Teaching Explanation Clarity</th>
                <th>Technical Depth</th>
                <th>Practicality to Industry</th>
                <th>User Friendliness</th>
                <th>Updates and Maintenance</th>
                <th>User</th>
                <th>Title</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $review->community_size }}</td>
                <td>{{ $review->teaching_explanation_clarity }}</td>
                <td>{{ $review->technical_depth }}</td>
                <td>{{ $review->practicality_to_industry }}</td>
                <td>{{ $review->user_friendliness }}</td>
                <td>{{ $review->updates_and_maintenance }}</td>
                <td>{{ $review->user ? $review->user->name : 'Anonymous' }}</td>
                <td>{{ $review->review_title ? $review->review_title : 'No title' }}</td>
                <td>{{ $review->review_description ? $review->review_description : 'No description' }}</td>
            </tr>
        </tbody>
    </table>    
    <x-comments.show-comments :id="$review->id" type='resourceReview'></x-comments.show-comments>

@endforeach

