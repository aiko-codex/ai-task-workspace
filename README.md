# AI Task Workspace

A local workspace for packaging isolated coding tasks with full context so an AI assistant can solve them.

## Quick Start

### 1. Create a new task

**PowerShell:**
```powershell
.\new-task.ps1 fix-auth-bug ..\my-app\src\auth.ts ..\my-app\src\middleware.ts
```

**Git Bash:**
```bash
./new-task.sh fix-auth-bug ../my-app/src/auth.ts ../my-app/src/middleware.ts
```

This creates a numbered folder like `tasks/task-01-fix-auth-bug/` with the template files and copies any source files you specify into `src/`.

### 2. Fill in the context

Edit `tasks/task-XX-your-task/CONTEXT.md` — describe the objective, tech stack, errors, included files, and expected output.

### 3. Add source files

Copy any relevant source files into the task's `src/` folder. The helper scripts do this automatically for files passed as arguments.

### 4. Send to AI

**Option A — Push to GitHub:**
```bash
git add .
git commit -m "Add task-01-fix-auth-bug"
git push
```
Then import the repo in your AI tool and prompt:

> "Focus only on `tasks/task-01-fix-auth-bug`. Read `CONTEXT.md` first, inspect the files in `src/`, and provide the solution based on the Expected Output section."

**Option B — Upload directly:**
Upload the specific `tasks/task-XX-...` folder to your AI assistant and use the same prompt.

## Structure

```
ai-task-workspace/
├── .gitignore
├── README.md
├── _template/          # Template for new tasks
│   ├── CONTEXT.md
│   ├── src/
│   └── sample-data/
├── tasks/              # All task folders live here
├── new-task.sh         # Task generator (Git Bash)
└── new-task.ps1        # Task generator (PowerShell)
```
