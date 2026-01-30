<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Quiz</title>
</head>
<body>
    <main>
        <h1>Today's Quiz</h1>

        @if (!$quizDay)
            <p>No active quiz right now.</p>
        @else
            <section>
                <h2>{{ $quizDay->title }}</h2>
                <p>Window: {{ $quizDay->start_at }} - {{ $quizDay->end_at }}</p>
                <p>Duration: {{ $quizDay->duration_seconds }} seconds</p>
            </section>

            @if (!$attempt)
                <form method="POST" action="{{ route('quiz.start', $quizDay) }}">
                    @csrf
                    <button type="submit">Start Quiz</button>
                </form>
            @elseif ($attempt->status === 'in_progress' && $remainingSeconds !== null)
                <p>Quiz in progress.</p>
                <p>Remaining time: {{ $remainingSeconds }} seconds</p>
            @elseif ($attempt->status === 'submitted')
                <p>Already submitted.</p>
                <p>Score: {{ $attempt->score }}</p>
            @else
                <p>Attempt expired.</p>
            @endif
        @endif
    </main>
</body>
</html>
