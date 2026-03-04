---
name: commit-jira-workflow
description: Analyzes git changes, generates conventional commit messages, and creates Jira task templates (title + description) for manual entry. Use when the user finishes editing, asks to commit, prepare commit, create Jira tasks, or says "خلصت التعديلات" or similar.
---

# Commit & Jira Workflow

## Trigger Phrases

Apply this skill when the user says things like:
- خلصت التعديلات
- اعمل commit
- جهزلي الـ commit
- اعمل تاسكات Jira
- جهز التغييرات للـ commit و Jira
- commit + Jira
- (أي طلب لتحضير التعديلات للـ commit أو إنشاء تاسكات)

## Workflow Overview

1. **Analyze** → git diff/status
2. **Group** → by module/area
3. **Commit** → conventional message per scope
4. **Jira** → N tasks (title + description), user specifies count

---

## Step 1: Analyze Changes

```bash
git status
git diff --staged   # إذا كان فيه staged
git diff           # كل التغييرات
```

Identify:
- **Modified files** (paths imply module/scope)
- **New files**
- **Deleted files**
- **Scope**: استنتج الـ module من المسار (مثلاً: `nova-components/Reports/` → Reports, `app/Models/` → Models)

---

## Step 2: Group by Module

Group changes logically. Example mapping for surebills-type project:

| Path pattern | Scope/module |
|-------------|--------------|
| `nova-components/Reports/*` | reports |
| `app/Models/*` | models |
| `app/Http/Controllers/*` | api / controllers |
| `resources/js/*` or `resources/views/*` | frontend |
| `database/migrations/*` | database |
| `routes/*` | routing |
| `*.blade.php` | views |
| `tests/*` | tests |

Use the most relevant scope for the commit. If changes span modules, either:
- One commit per logical group, or
- Single commit with main scope + (optional) "cross-cutting" note

---

## Step 3: Generate Commit Message

**Format** (Conventional Commits):

```
<type>(<scope>): <short description>

Optional body explaining what and why.
```

**Types**: `feat`, `fix`, `refactor`, `style`, `docs`, `test`, `chore`

**Examples**:

```
feat(reports): add export to PDF for invoice list
fix(api): handle null customer in billing endpoint
refactor(models): extract shared logic to trait
```

**Rules**:
- Use English for the commit message
- Scope = module/area inferred from paths
- Body optional but useful for complex changes
- One commit per logical unit; if many unrelated changes, suggest multiple commits

---

## Step 4: Create Jira Tasks

User may specify:
- **"اعمل كام تاسك"** / **"كام تاسك"** → use that number
- **"تاسك واحد"** → 1 task
- If not specified: suggest a count based on logical groupings (1–3 typically)

**Per task, output**:

```markdown
## Jira Task [N]

**Title:** [Short, actionable title in English]

**Description:**
[2–4 sentences describing:
- What was changed
- Which files/components
- Why / business value if relevant]
```

**Example**:

```markdown
## Jira Task 1

**Title:** Add PDF export for invoice reports

**Description:**
Added PDF export functionality to the invoice reports module. Users can now download report data as PDF from the Reports screen. Changes include new export service, API endpoint, and UI button. Supports Arabic RTL layout.
```

---

## Output Structure

When applying this skill, output in this order:

1. **Summary** – what was analyzed (e.g., "X files changed across Reports and API")
2. **Suggested commit(s)** – full message(s) ready to copy
3. **Jira tasks** – N tasks with Title + Description
4. **Optional**: `git add` suggestions if nothing staged

---

## Examples

### Single scope, 1 task

User: "خلصت شغل على الـ Reports، اعمل commit و تاسك Jira"

Output:
- Commit: `feat(reports): add date range filter to invoice reports`
- 1 Jira task with title + description

### Multi-scope, user says "تاسكين"

User: "اعمل commit وتاسكين لـ Jira"

Output:
- 1 or 2 commits (grouped logically)
- 2 Jira tasks, each with distinct scope

### No count specified

User: "اعمل commit و Jira"

Output:
- 1–2 commits
- Suggest 1–2 tasks based on changes; ask "عايز كام تاسك؟" if ambiguous
