{{-- resources/views/reports/create.blade.php --}}

<div class="container">
    <h1>Create a Report</h1>
    <form action="{{ route('reports.store', ['resource' => $resource, 'id' => $id]) }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="report_text">Report</label>
            <textarea class="form-control" id="report_text" name="report_text" rows="3" required></textarea>
        </div>

        <input type="hidden" name="reportable_id" value="{{ $id }}">
        <input type="hidden" name="reportable_type" value="{{ $resource }}">

        <button type="submit" class="btn btn-primary">Submit bruh</button>
    </form>
</div>
