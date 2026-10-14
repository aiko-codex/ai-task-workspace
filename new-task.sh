#!/usr/bin/env bash
set -e

if [ -z "$1" ]; then
  echo "Usage: ./new-task.sh <task-slug> [files-to-copy...]"
  echo "Example: ./new-task.sh fix-auth-bug ../my-app/src/auth.ts ../my-app/src/middleware.ts"
  exit 1
fi

TASK_SLUG=$1
shift

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
TASKS_DIR="$SCRIPT_DIR/tasks"
TEMPLATE_DIR="$SCRIPT_DIR/_template"

# Calculate next task number
COUNT=$(find "$TASKS_DIR" -mindepth 1 -maxdepth 1 -type d 2>/dev/null | wc -l | tr -d ' ')
NEXT_NUM=$(printf "%02d" $((COUNT + 1)))
TARGET_DIR="$TASKS_DIR/task-${NEXT_NUM}-${TASK_SLUG}"

cp -r "$TEMPLATE_DIR" "$TARGET_DIR"

# Remove .gitkeep files from the new task
find "$TARGET_DIR" -name ".gitkeep" -delete 2>/dev/null || true

# Copy any passed files into the task's src/ folder
for FILE in "$@"; do
  if [ -e "$FILE" ]; then
    cp -r "$FILE" "$TARGET_DIR/src/"
    echo "Copied: $FILE -> $TARGET_DIR/src/"
  else
    echo "Warning: File not found: $FILE"
  fi
done

echo ""
echo "✅ Created task folder: $TARGET_DIR"
echo "👉 Next: Edit $TARGET_DIR/CONTEXT.md"
