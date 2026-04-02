---
name: Use ddev for commands
description: Use "ddev artisan" instead of "php artisan" to run Laravel commands in this project
type: feedback
---

Use `ddev artisan test` instead of `php artisan test` to run tests and other artisan commands.

**Why:** The project uses DDEV as its local development environment, so PHP runs inside a container.
**How to apply:** Always prefix artisan commands with `ddev` (e.g., `ddev artisan test`, `ddev artisan migrate`).
