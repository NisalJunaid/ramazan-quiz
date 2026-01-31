<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ text('leaderboard.title', 'Leaderboard') }}</title>
</head>
<body>
    <main>
        <h1>{{ text('leaderboard.title', 'Leaderboard') }}</h1>
        <p><a href="{{ route('home') }}">{{ text('leaderboard.back', 'Back to Home') }}</a></p>

        @if (! $quizDay)
            <p>{{ text('leaderboard.empty', 'No quiz available yet.') }}</p>
        @else
            <h2>{{ $quizDay->title }} ({{ $quizDay->quiz_date }})</h2>

            @if ($attempts->isEmpty())
                <p>{{ text('leaderboard.none', 'No submissions yet.') }}</p>
            @else
                <table border="1" cellpadding="6" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ text('leaderboard.table.rank', 'Rank') }}</th>
                            <th>{{ text('leaderboard.table.name', 'Name') }}</th>
                            <th>{{ text('leaderboard.table.score', 'Score') }}</th>
                            <th>{{ text('leaderboard.table.submitted', 'Submitted At') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($attempts as $index => $attempt)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $attempt->user->name }}</td>
                                <td>{{ $attempt->score }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($attempt->submitted_at)->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endif
    </main>
</body>
</html>
