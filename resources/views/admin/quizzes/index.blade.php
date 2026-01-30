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
            <h2>Add Question</h2>
            <form method="POST" action="{{ route('admin.questions.store') }}">
                @csrf
                <div>
                    <label for="question_quiz_day_id">Quiz Day</label>
                    <select id="question_quiz_day_id" name="quiz_day_id" required>
                        <option value="">Select quiz day</option>
                        @foreach ($quizDays as $quizDay)
                            <option value="{{ $quizDay->id }}" {{ old('quiz_day_id') == $quizDay->id ? 'selected' : '' }}>
                                {{ $quizDay->quiz_date }} - {{ $quizDay->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="question_text">Question Text</label>
                    <textarea id="question_text" name="question_text" rows="3" required>{{ old('question_text') }}</textarea>
                </div>
                <div>
                    <label for="question_points">Points</label>
                    <input
                        type="number"
                        id="question_points"
                        name="points"
                        min="1"
                        value="{{ old('points', 1) }}"
                        required
                    >
                </div>
                <div>
                    <label for="question_order_index">Order Index</label>
                    <input
                        type="number"
                        id="question_order_index"
                        name="order_index"
                        min="1"
                        value="{{ old('order_index') }}"
                        required
                    >
                </div>
                <button type="submit">Add Question</button>
            </form>
        </section>

        <section>
            <h2>Add Choice</h2>
            <form method="POST" action="{{ route('admin.choices.store') }}">
                @csrf
                <div>
                    <label for="choice_question_id">Question</label>
                    <select id="choice_question_id" name="question_id" required>
                        <option value="">Select question</option>
                        @foreach ($questions as $question)
                            <option value="{{ $question->id }}" {{ old('question_id') == $question->id ? 'selected' : '' }}>
                                {{ $question->quizDay?->quiz_date }} - Q{{ $question->order_index }}:
                                {{ \Illuminate\Support\Str::limit($question->question_text, 80) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="choice_text">Choice Text</label>
                    <input type="text" id="choice_text" name="choice_text" value="{{ old('choice_text') }}" required>
                </div>
                <div>
                    <label for="choice_order_index">Order Index</label>
                    <input
                        type="number"
                        id="choice_order_index"
                        name="order_index"
                        min="1"
                        value="{{ old('order_index') }}"
                        required
                    >
                </div>
                <div>
                    <label>
                        <input type="checkbox" name="is_correct" value="1" {{ old('is_correct') ? 'checked' : '' }}>
                        Mark as correct
                    </label>
                </div>
                <button type="submit">Add Choice</button>
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
