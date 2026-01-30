<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <main>
        <h1>Admin</h1>
        <ul>
            <li><a href="{{ route('admin.quizzes.index') }}">Manage Quizzes</a></li>
        </ul>
        <p><a href="{{ route('home') }}">Back to Home</a></p>
    </main>
</body>
</html>
