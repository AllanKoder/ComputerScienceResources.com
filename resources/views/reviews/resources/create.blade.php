<h1>Create a Review</h1>
<form action="{{ route('reviews.store', array('resource'=>$resource)) }}" method="POST">
    @csrf
    <div class="form-group">
        <label for="community_size">Community Size</label>
        <input type="number" name="community_size" id="community_size" class="form-control" value="{{ old('community_size') }}" min="0" max="5" required>
    </div>

    <div class="form-group">
        <label for="teaching_explanation_clarity">Teaching/Explanation Clarity</label>
        <input type="number" name="teaching_explanation_clarity" id="teaching_explanation_clarity" class="form-control" value="{{ old('teaching_explanation_clarity') }}" min="0" max="5" required>
    </div>

    <div class="form-group">
        <label for="practicality_to_industry">Practicality to Industry</label>
        <input type="number" name="practicality_to_industry" id="practicality_to_industry" class="form-control" value="{{ old('practicality_to_industry') }}" min="0" max="5" required>
    </div>

    <div class="form-group">
        <label for="technical_depth">Technical Depth</label>
        <input type="number" name="technical_depth" id="technical_depth" class="form-control" value="{{ old('technical_depth') }}" min="0" max="5" required>
    </div>

    <div class="form-group">
        <label for="user_friendliness">User Friendliness</label>
        <input type="number" name="user_friendliness" id="user_friendliness" class="form-control" value="{{ old('user_friendliness') }}" min="0" max="5" required>
    </div>

    <div class="form-group">
        <label for="updates_and_maintenance">Updates and Maintenance</label>
        <input type="number" name="updates_and_maintenance" id="updates_and_maintenance" class="form-control" value="{{ old('updates_and_maintenance') }}" min="0" max="5" required>
    </div>

    <div class="form-group">
        <label for="comment_title">Title</label>
        <input type="text" id="comment_title" name="comment_title" required></input>
    </div>
    
    <div class="form-group">
        <label for="comment_text">Description</label>
        <textarea class="form-control" id="comment_text" name="comment_text" rows="3" required></textarea>
    </div>

    

    <button type="submit" class="btn btn-primary">Submit Review</button>
</form>