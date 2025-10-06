#!/bin/bash

echo "🚀 Pushing SuperAdmin Campusway to GitHub"
echo "=========================================="

# Set repository name
REPO_NAME="superadmin-campusway"
GITHUB_USER="Rifaiiii04"  # GitHub username

echo "📁 Repository: $GITHUB_USER/$REPO_NAME"

# Check if remote already exists
if git remote get-url origin >/dev/null 2>&1; then
    echo "✅ Remote origin already exists"
    git remote -v
else
    echo "➕ Adding remote origin..."
    git remote add origin https://github.com/$GITHUB_USER/$REPO_NAME.git
fi

# Push to GitHub
echo "📤 Pushing to GitHub..."
git push -u origin main

if [ $? -eq 0 ]; then
    echo "✅ Successfully pushed to GitHub!"
    echo "🌐 Repository URL: https://github.com/$GITHUB_USER/$REPO_NAME"
else
    echo "❌ Failed to push to GitHub"
    echo "💡 Make sure you have:"
    echo "   1. Created repository on GitHub: https://github.com/new"
    echo "   2. Set correct GitHub username in this script"
    echo "   3. Authenticated with GitHub (username/password or token)"
fi