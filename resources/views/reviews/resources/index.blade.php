<h1>Resource Reviews</h1>
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
                <th>Comment</th>
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
                <td>{{ $review->comment ? $review->comment->comment_text : 'No comment' }}</td>
            </tr>
        </tbody>
    </table>
    @if ($review->comments)
        @include('comments.index', ['comments' => $review->comments])
    @endif
@endforeach

