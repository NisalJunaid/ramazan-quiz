<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ text('home.page_title', 'Ramazan Daily Quiz Portal') }}</title>
</head>
<body>
    <main>
        <h1>{{ text('home.page_title', 'Ramazan Daily Quiz Portal') }}</h1>
        @auth
            <nav>
                <ul>
                    <li><a href="{{ route('quiz.today') }}">{{ text('home.nav.today_quiz', "Today's Quiz") }}</a></li>
                    @if ($canViewLeaderboard)
                        <li><a href="{{ route('leaderboard') }}">{{ text('leaderboard.title', 'Leaderboard') }}</a></li>
                    @endif
                </ul>
            </nav>
        @else
            <nav>
                <ul>
                    <li><a href="{{ route('login') }}">{{ text('navigation.login', 'Login') }}</a></li>
                    <li><a href="{{ route('register') }}">{{ text('navigation.register', 'Register') }}</a></li>
                </ul>
            </nav>
        @endauth
    </main>
</body>
</html>
