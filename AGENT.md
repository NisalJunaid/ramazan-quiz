# AGENTS.md — Ramazan Daily Quiz Portal (Laravel + Breeze)

Owner: Ibrahim Nisal  
Scope: Ramazan Daily Quiz Portal  
Framework: Laravel  
Auth: Laravel Breeze  
Database: MySQL or PostgreSQL  

This document defines the COMPLETE system blueprint.
Agents MUST NOT add features outside this document.

==================================================
1. SYSTEM GOAL
==================================================
Build a simple Ramazan Daily Quiz Portal where:

- One quiz exists per day
- Users register and log in
- Users can attempt the quiz ONCE per day
- Quiz is auto-graded
- A leaderboard shows results

NO extra features.
NO gamification.
NO referrals.
NO streaks.
NO prizes logic.
NO notifications.

==================================================
2. CORE RULES (NON-NEGOTIABLE)
==================================================
- One quiz per day
- One attempt per user per quiz
- Timer is enforced server-side
- Answers are auto-graded
- Leaderboard ranking is deterministic
- Laravel Breeze handles ALL authentication
- Role-based access (admin / user)

==================================================
3. AUTHENTICATION (LARAVEL BREEZE)
==================================================
Auth method:
- Laravel Breeze (email + password)
- Session-based authentication
- CSRF protection enabled

User roles:
- user  → can take quizzes
- admin → can manage quizzes

==================================================
4. DATABASE STRUCTURE
==================================================

-----------------------------------
users
-----------------------------------
id (bigint, PK)
name (string)
email (string, unique)
email_verified_at (timestamp, nullable)
password (string)
role (string, default 'user')
is_banned (boolean, default false)
remember_token
created_at
updated_at

-----------------------------------
quiz_days
-----------------------------------
id (bigint, PK)
quiz_date (date, unique)
title (string)
start_at (datetime)
end_at (datetime)
duration_seconds (int)
is_published (boolean)
created_at
updated_at

-----------------------------------
questions
-----------------------------------
id (bigint, PK)
quiz_day_id (FK → quiz_days.id)
question_text (text)
points (int, default 1)
order_index (int)
created_at
updated_at

-----------------------------------
choices
-----------------------------------
id (bigint, PK)
question_id (FK → questions.id)
choice_text (text)
is_correct (boolean)
order_index (int)

-----------------------------------
attempts
-----------------------------------
id (bigint, PK)
quiz_day_id (FK → quiz_days.id)
user_id (FK → users.id)
started_at (datetime)
expires_at (datetime)
submitted_at (datetime, nullable)
score (int)
status (enum: in_progress, submitted, expired)
created_at

UNIQUE (quiz_day_id, user_id)

-----------------------------------
answers
-----------------------------------
id (bigint, PK)
attempt_id (FK → attempts.id)
question_id (FK → questions.id)
choice_id (FK → choices.id)
is_correct (boolean)
points_awarded (int)

UNIQUE (attempt_id, question_id)

==================================================
5. ROUTES (LARAVEL)
==================================================

------------------
Public
------------------
GET  /               → Home (quiz status)
GET  /leaderboard    → Leaderboard

------------------
Auth (Breeze)
------------------
GET  /login
POST /login
GET  /register
POST /register
POST /logout

------------------
Quiz (Authenticated Users)
------------------
GET  /quiz/today
POST /quiz/{quizDay}/start
POST /attempt/{attempt}/submit

------------------
Admin (Admin Only)
------------------
GET  /admin
GET  /admin/quizzes
POST /admin/quizzes
PUT  /admin/quizzes/{id}
POST /admin/questions
POST /admin/choices

==================================================
6. CONTROLLERS
==================================================

QuizController
- showTodayQuiz()
- startAttempt()

AttemptController
- submitAttempt()

LeaderboardController
- todayLeaderboard()

AdminQuizController
- index()
- store()
- update()

AdminQuestionController
- store()

==================================================
7. QUIZ FLOW (USER)
==================================================

1. User logs in
2. System checks today's quiz:
   - published
   - current time between start_at and end_at
3. If user has NO attempt:
   - show "Start Quiz"
4. On start:
   - create attempt
   - set started_at
   - set expires_at = started_at + duration
5. User submits answers
6. System grades immediately
7. Attempt marked as submitted
8. Score saved
9. User sees result

==================================================
8. TIMER & EXPIRY LOGIC
==================================================

- Timer is NOT trusted from frontend
- expires_at is calculated server-side
- If now > expires_at:
  - attempt becomes expired
  - submission rejected

==================================================
9. GRADING LOGIC
==================================================

For each question:
- Compare selected choice_id with correct choice
- If correct:
  - points_awarded = question.points
- Else:
  - points_awarded = 0

Final score:
- sum(points_awarded)

==================================================
10. LEADERBOARD LOGIC
==================================================

Daily leaderboard:
- Only submitted attempts
- Sorted by:
  1. score DESC
  2. submitted_at ASC

==================================================
11. ADMIN RULES
==================================================

Admins can:
- Create daily quiz
- Add questions
- Add choices
- Mark correct answer
- Publish quiz

Admins CANNOT:
- Edit quiz after users submit attempts

==================================================
12. MIDDLEWARE
==================================================

auth                 → all quiz routes
admin                → admin routes
not_banned           → quiz routes

==================================================
13. FILE STRUCTURE
==================================================

app/
 ├── Models/
 ├── Http/
 │   ├── Controllers/
 │   ├── Middleware/
 ├── Services/
resources/views/
routes/web.php
database/migrations/

==================================================
14. DEVELOPMENT RULES FOR CODEX
==================================================

- Follow this file exactly
- Do NOT invent features
- Do NOT change auth method
- Do NOT add tables
- Do NOT add background jobs
- Do NOT add caching
- Keep logic synchronous and simple
- Favor clarity over abstraction

==================================================
END OF AGENTS.md
==================================================
