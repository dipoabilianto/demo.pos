---
name: clean-code
description: Always write clean, DRY, readable, and well-structured code. Single-responsibility functions, expressive naming, no duplication, consistent patterns.
---

## Clean Code & DRY

Apply these principles to **every** code change, review, or suggestion:

### DRY (Don't Repeat Yourself)
- Every piece of logic must have **one** authoritative place
- Extract duplication into reusable functions, classes, or config
- Never copy-paste — abstract the common pattern
- Use loops, parameters, and generics instead of repetitive blocks

### Naming
- Functions are verbs (`getUser()`, `formatCurrency()`) — what they do
- Variables are nouns (`$totalPrice`, `$userList`) — what they hold
- Booleans use prefixes (`isActive`, `hasPermission`, `canEdit`)
- Avoid abbreviations unless universally known (e.g. `id`, `url`, `html`)

### Single Responsibility
- One function = one job. If it does two things, split it
- Extract conditionals into named helper functions
- Keep functions short — if it needs a comment to explain a block, extract that block

### Structure & Consistency
- Follow existing patterns in the codebase (file structure, naming, framework conventions)
- Group related code together; separate concerns (UI / logic / data)
- Minimal nesting — early return over deep `if` chains

### Comments
- Code should be **self-documenting** — prefer expressive names over comments
- Only comment *why* something exists, never *what* it does
- No commented-out code — delete it, git has history

### Testing
- Tests are code too — same DRY & clean code rules
- Arrange → Act → Assert pattern
- One logical assertion per test
