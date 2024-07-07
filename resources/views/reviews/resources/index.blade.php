<h1>Resource Reviews</h1>
<table class="table">
    <thead>
        <tr>
            <th>Community Size</th>
            <th>Teaching Explanation Clarity</th>
            <th>Technical Depth</th>
            <th>Practicality to Industry</th>
            <th>User Friendliness</th>
            <th>Updates and Maintenance</th>
            <th>Comment</th>
            <th>User</th>
        </tr>
    </thead>
    <tbody>
        @foreach($resourceReviews as $review)
        <tr>
            <td>{{ $review->community_size }}</td>
            <td>{{ $review->teaching_explanation_clarity }}</td>
            <td>{{ $review->technical_depth }}</td>
            <td>{{ $review->practicality_to_industry }}</td>
            <td>{{ $review->user_friendliness }}</td>
            <td>{{ $review->updates_and_maintenance }}</td>
            <td>{{ $review->comment ? $review->comment->comment_text : 'No comment' }}</td>
            <td>{{ $review->user ? $review->user->name : 'Anonymous' }}</td>
            <td>
                {{-- <a href="{{ route('resourceReviews.show', $review->id) }}" class="btn btn-info">View</a>
                <a href="{{ route('resourceReviews.edit', $review->id) }}" class="btn btn-warning">Edit</a> --}}
                {{-- <form action="{{ route('resourceReviews.destroy', $review->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form> --}}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
