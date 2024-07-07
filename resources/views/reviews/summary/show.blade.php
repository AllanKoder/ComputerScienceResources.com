<!-- Display review summary -->
<div class="mb-4">
    <h3>Review Summary</h3>
    <ul>
        <li>Average Community Size: {{ $reviewSummaryData['average_community_size'] }}</li>
        <li>Average Teaching Explanation Clarity: {{ $reviewSummaryData['average_teaching_explanation_clarity'] }}</li>
        <li>Average Technical Depth: {{ $reviewSummaryData['average_technical_depth'] }}</li>
        <li>Average Practicality to Industry: {{ $reviewSummaryData['average_practicality_to_industry'] }}</li>
        <li>Average User Friendliness: {{ $reviewSummaryData['average_user_friendliness'] }}</li>
        <li>Average Updates and Maintenance: {{ $reviewSummaryData['average_updates_and_maintenance'] }}</li>
        <li>Overall Average Score: {{ $reviewSummaryData['average_score'] }}</li>
    </ul>
</div>