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
    @include('comments.partials.reply', array('comment'=>$review->comment))
    
    <x-spinner class="mx-auto" id="spinner-{{ $review->id }}"></x-spinner>
    <button _="on click toggle the *display of the next <div/>"
        hx-get="{{ route('reviews.replies', ['id' => $review->id]) }}" hx-target="#replies-{{ $review->id }}"
        hx-indicator="#spinner-{{ $review->id }}"
        class="bg-teal-300 p-2 mt-2">
        View Replies
    </button>
    <div id="replies-{{ $review->id }}"></div>

@endforeach

