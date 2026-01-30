<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Quizzes</title>
</head>
<body>
    <main>
        <h1>Manage Quizzes</h1>
        <p><a href="{{ route('admin.dashboard') }}">Back to Admin</a></p>

        @if (session('status'))
            <p>{{ session('status') }}</p>
        @endif

        @if ($errors->any())
            <div>
                <p>There were problems with your submission:</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section>
            <h2>Create Quiz Day</h2>
            <form method="POST" action="{{ route('admin.quizzes.store') }}">
                @csrf
                <div>
                    <label for="quiz_date">Quiz Date</label>
                    <input type="date" id="quiz_date" name="quiz_date" value="{{ old('quiz_date') }}" required>
                </div>
                <div>
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required>
                </div>
                <div>
                    <label for="start_at">Start At</label>
                    <input type="datetime-local" id="start_at" name="start_at" value="{{ old('start_at') }}" required>
                </div>
                <div>
                    <label for="end_at">End At</label>
                    <input type="datetime-local" id="end_at" name="end_at" value="{{ old('end_at') }}" required>
                </div>
                <div>
                    <label for="duration_seconds">Duration (seconds)</label>
                    <input type="number" id="duration_seconds" name="duration_seconds" min="1" value="{{ old('duration_seconds') }}" required>
                </div>
                <div>
                    <label>
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}>
                        Published
                    </label>
                </div>
                <button type="submit">Create Quiz Day</button>
            </form>
        </section>

        <section>
            <h2>Existing Quiz Days</h2>
            @if ($quizDays->isEmpty())
                <p>No quiz days created yet.</p>
            @else
                <table border="1" cellpadding="6" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Title</th>
                            <th>Window</th>
                            <th>Duration</th>
                            <th>Published</th>
                            <th>Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($quizDays as $quizDay)
                            <tr>
                                <td>{{ $quizDay->quiz_date }}</td>
                                <td>{{ $quizDay->title }}</td>
                                <td>{{ $quizDay->start_at }} - {{ $quizDay->end_at }}</td>
                                <td>{{ $quizDay->duration_seconds }} sec</td>
                                <td>{{ $quizDay->is_published ? 'Yes' : 'No' }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.quizzes.update', $quizDay->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <label for="quiz_date_{{ $quizDay->id }}">Quiz Date</label>
                                            <input
                                                type="date"
                                                id="quiz_date_{{ $quizDay->id }}"
                                                name="quiz_date"
                                                value="{{ $quizDay->quiz_date }}"
                                                required
                                            >
                                        </div>
                                        <div>
                                            <label for="title_{{ $quizDay->id }}">Title</label>
                                            <input
                                                type="text"
                                                id="title_{{ $quizDay->id }}"
                                                name="title"
                                                value="{{ $quizDay->title }}"
                                                required
                                            >
                                        </div>
                                        <div>
                                            <label for="start_at_{{ $quizDay->id }}">Start At</label>
                                            <input
                                                type="datetime-local"
                                                id="start_at_{{ $quizDay->id }}"
                                                name="start_at"
                                                value="{{ \Illuminate\Support\Carbon::parse($quizDay->start_at)->format('Y-m-d\TH:i') }}"
                                                required
                                            >
                                        </div>
                                        <div>
                                            <label for="end_at_{{ $quizDay->id }}">End At</label>
                                            <input
                                                type="datetime-local"
                                                id="end_at_{{ $quizDay->id }}"
                                                name="end_at"
                                                value="{{ \Illuminate\Support\Carbon::parse($quizDay->end_at)->format('Y-m-d\TH:i') }}"
                                                required
                                            >
                                        </div>
                                        <div>
                                            <label for="duration_seconds_{{ $quizDay->id }}">Duration (seconds)</label>
                                            <input
                                                type="number"
                                                id="duration_seconds_{{ $quizDay->id }}"
                                                name="duration_seconds"
                                                min="1"
                                                value="{{ $quizDay->duration_seconds }}"
                                                required
                                            >
                                        </div>
                                        <div>
                                            <label>
                                                <input
                                                    type="checkbox"
                                                    name="is_published"
                                                    value="1"
                                                    {{ $quizDay->is_published ? 'checked' : '' }}
                                                >
                                                Published
                                            </label>
                                        </div>
                                        <button type="submit">Update</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>
    </main>
</body>
</html>
