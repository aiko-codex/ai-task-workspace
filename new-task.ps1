<#
.SYNOPSIS
  Creates a new numbered task folder from the _template.

.EXAMPLE
  .\new-task.ps1 fix-auth-bug ..\my-app\src\auth.ts ..\my-app\src\middleware.ts
#>

param(
    [Parameter(Mandatory = $true, Position = 0)]
    [string]$TaskSlug,

    [Parameter(ValueFromRemainingArguments = $true)]
    [string[]]$FilesToCopy
)

$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$TasksDir = Join-Path $ScriptDir "tasks"
$TemplateDir = Join-Path $ScriptDir "_template"

# Calculate next task number
$ExistingDirs = Get-ChildItem -Path $TasksDir -Directory -ErrorAction SilentlyContinue
$Count = if ($ExistingDirs) { $ExistingDirs.Count } else { 0 }
$NextNum = "{0:D2}" -f ($Count + 1)
$TargetDir = Join-Path $TasksDir "task-$NextNum-$TaskSlug"

# Copy template
Copy-Item -Path $TemplateDir -Destination $TargetDir -Recurse

# Remove .gitkeep files from the new task
Get-ChildItem -Path $TargetDir -Filter ".gitkeep" -Recurse | Remove-Item -Force -ErrorAction SilentlyContinue

# Copy any passed files into src/
$SrcDir = Join-Path $TargetDir "src"
foreach ($FilePath in $FilesToCopy) {
    if (Test-Path $FilePath) {
        Copy-Item -Path $FilePath -Destination $SrcDir -Recurse
        Write-Host "Copied: $FilePath -> $SrcDir"
    } else {
        Write-Warning "File not found: $FilePath"
    }
}

Write-Host ""
Write-Host "Created task folder: $TargetDir" -ForegroundColor Green
Write-Host "Next: Edit $TargetDir\CONTEXT.md" -ForegroundColor Cyan
