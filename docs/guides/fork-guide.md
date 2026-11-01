# Lecturer Repository Fork Guide (Windows & macOS)

This guide explains how to fork the instructor's repository, work on assignments in your own fork, and pull ongoing updates from the upstream repository.

**Key Concepts**

| Term | Definition |
|---|---|
| **Fork** | A remote copy of the instructor's repository hosted under your personal GitHub account. |
| **Clone** | Downloading your remote fork to your local computer. |
| **`origin`** | Remote repository pointing to your personal fork (where you `push` changes). |
| **`upstream`** | Remote repository pointing to the original instructor's repository (where you `pull` updates). |
| **Branch** | An isolated working branch to ensure the `main` branch remains clean. |

Instructor's upstream repository: <https://github.com/mirzayogy/laravel5d>

---

## 1. Initial Environment Setup (One-Time)

### Windows
1. Create an account at <https://github.com>.
2. Install **Git for Windows**: <https://git-scm.com/download/win>.
3. Install **PHP** and **Composer** (via Laragon, Herd, or installer).
4. Install **Node.js LTS**: <https://nodejs.org>.
5. Open PowerShell and verify:
   ```powershell
   git --version
   php -v
   composer -V
   node -v
   ```

### Configure Git Identity
```bash
git config --global user.name "Your Name"
git config --global user.email "your-email@example.com"
```

---

## 2. Fork the Repository
1. Navigate to <https://github.com/mirzayogy/laravel5d>.
2. Click the **Fork** button in the top-right corner.
3. Select your account as the **Owner**.
4. Click **Create fork**. Your fork will be available at `https://github.com/YOUR-USERNAME/laravel5d`.

---

## 3. Clone Fork to Local Machine
```bash
git clone https://github.com/YOUR-USERNAME/laravel5d.git
cd laravel5d
```

---

## 4. Connect Upstream Remote
```bash
git remote add upstream https://github.com/mirzayogy/laravel5d.git
git remote -v
```

---

## 5. Daily Development Workflow

Always create a dedicated feature branch for assignments:
```bash
git switch -c feature/your-feature-name
```

Commit and push your work:
```bash
git add .
git commit -m "feat: description of work"
git push -u origin feature/your-feature-name
```

---

## 6. Submitting a Pull Request (PR)
1. Open your repository on GitHub.
2. Click **Compare & pull request** on your feature branch.
3. Ensure the base repository is set to `mirzayogy/laravel5d` (`main`) and compare branch is your feature branch.
4. Fill in the title and description, then click **Create pull request**.
