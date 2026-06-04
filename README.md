# Predictions

A football match prediction web application built with **Symfony 8** and **PHP 8.4**. Users register, submit score predictions for upcoming games, and compete on a live leaderboard.

## Features

- **Match predictions** — predict full-time scores for scheduled games; penalty shootout predictions for knockout ties that finish level
- **Automatic scoring** — 3 pts for an exact scoreline, 1 pt for the correct outcome (win/draw/loss), +1 bonus pt for predicting the penalty shootout winner
- **Competition management** — support for multiple competitions, each split into phases (group stage, league, knockout)
- **Live rankings** — leaderboard filterable by competition and phase, updated automatically via Mercure SSE
- **Admin panel** — full CRUD for competitions, phases, teams, games, stadiums, countries, and standings; live score entry during games
- **Authentication** — form-based login/logout with CSRF protection; admin area protected by role (`ROLE_ADMIN`)
- **File uploads** — logo management for teams and countries

## Tech Stack

| Layer | Technology                                      |
|---|-------------------------------------------------|
| Language | PHP 8.4                                         |
| Framework | Symfony 8.0                                     |
| Database | PostgreSQL |
| ORM | Doctrine ORM 3                        |
| Server | FrankenPHP |
| Templates | Twig + Bootstrap                                |
| Testing | PHPUnit 13                                      |
| Containerisation | Docker / Docker Compose                         |
