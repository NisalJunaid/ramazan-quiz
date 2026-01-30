@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold">Today's Quiz</h1>

    @if (!$quizDay)
        <p class="mt-4 text-gray-600">No active quiz right now.</p>
    @else
        <section class="mt-4 rounded-lg border border-gray-200 bg-white p-4">
            <h2 class="text-lg font-semibold">{{ $quizDay->title }}</h2>
            <p class="mt-2 text-sm text-gray-600">Window: {{ $quizDay->start_at }} - {{ $quizDay->end_at }}</p>
            <p class="text-sm text-gray-600">Duration: {{ $quizDay->duration_seconds }} seconds</p>
        </section>

        @if (!$attempt)
            <form class="mt-4" method="POST" action="{{ route('quiz.start', $quizDay) }}">
                @csrf
                <button class="rounded bg-blue-600 px-4 py-2 text-white" type="submit">Start Quiz</button>
            </form>
        @elseif ($attempt->status === 'in_progress' && $remainingSeconds !== null)
            <p class="mt-4 text-gray-700">Quiz in progress.</p>
            <p class="text-gray-600">Remaining time: {{ $remainingSeconds }} seconds</p>
            @if ($questions->isEmpty())
                <p class="mt-4 text-gray-600">No questions available.</p>
            @else
                <form class="mt-4 space-y-4" method="POST" action="{{ route('attempt.submit', $attempt) }}">
                    @csrf
                    @foreach ($questions as $question)
                        <fieldset class="rounded border border-gray-200 p-4">
                            <legend class="text-sm font-semibold text-gray-700">
                                {{ $question->order_index }}. {{ $question->question_text }}
                            </legend>
                            <div class="mt-2 space-y-2">
                                @foreach ($question->choices as $choice)
                                    <label class="flex items-center gap-2 text-sm text-gray-700">
                                        <input
                                            class="text-blue-600"
                                            type="radio"
                                            name="answers[{{ $question->id }}]"
                                            value="{{ $choice->id }}"
                                        >
                                        {{ $choice->choice_text }}
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>
                    @endforeach
                    <button class="rounded bg-green-600 px-4 py-2 text-white" type="submit">Submit Answers</button>
                </form>
            @endif
        @elseif ($attempt->status === 'submitted')
            <p class="mt-4 text-gray-700">Already submitted.</p>
            <p class="text-gray-600">Score: {{ $attempt->score }}</p>
        @else
            <p class="mt-4 text-gray-700">Attempt expired.</p>
        @endif
    @endif
@endsection
