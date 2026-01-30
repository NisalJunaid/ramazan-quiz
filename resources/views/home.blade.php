<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ramazan Daily Quiz Portal</title>
</head>
<body>
    <main>
        <h1>Ramazan Daily Quiz Portal</h1>
        @auth
            <nav>
                <ul>
                    <li><a href="{{ route('quiz.today') }}">Today's Quiz</a></li>
                    <li><a href="{{ route('leaderboard') }}">Leaderboard</a></li>
                </ul>
            </nav>
        @else
            <nav>
                <ul>
                    <li><a href="{{ route('login') }}">Login</a></li>
                    <li><a href="{{ route('register') }}">Register</a></li>
                </ul>
            </nav>
        @endauth
    </main>
</body>
</html>
