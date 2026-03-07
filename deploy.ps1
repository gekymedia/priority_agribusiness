# Priority Agribusiness Production Deployment Script (PowerShell)
# Live site: https://agribusiness.prioritysolutionsagency.com
# Server: gekymedia.com
# Path: /home/gekymedia/web/agribusiness.prioritysolutionsagency.com/public_html

$ErrorActionPreference = 'Continue'
$repoRoot = if ($PSScriptRoot) { $PSScriptRoot } else { Get-Location }
Push-Location $repoRoot

# Ensure .gitignore exists so storage/logs and sessions are not tracked (avoids merge conflicts on pull)
if (-not (Test-Path .gitignore)) {
    Write-Host "Creating .gitignore (was missing)..." -ForegroundColor Yellow
    @'
*.log
.DS_Store
.env
.env.backup
.env.production
/node_modules
/public/build
/public/hot
/public/storage
/storage/*.key
/storage/pail
/storage/logs/
/storage/framework/sessions/
/storage/framework/views/
/storage/framework/cache/
/vendor
Homestead.json
Homestead.yaml
Thumbs.db
'@ | Set-Content -Path .gitignore -Encoding utf8
}

# Stop tracking storage paths that should be ignored (so pull on server does not see "local changes")
git rm -r --cached storage/logs storage/framework/sessions 2>$null

Write-Host "Committing and pushing local changes..." -ForegroundColor Cyan
git add .
git commit -m "Deploy: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"
if ($LASTEXITCODE -ne 0) { Write-Host "No changes to commit" -ForegroundColor Yellow }
git push origin main

Write-Host "Deploying to production..." -ForegroundColor Cyan
# Discard local changes to storage on server so pull can succeed; then pull (preserves .env etc.)
$remoteCmd = 'cd /home/gekymedia/web/agribusiness.prioritysolutionsagency.com/public_html && git restore storage/ 2>/dev/null; git fetch origin main && git checkout main && git pull origin main && composer install --no-dev --optimize-autoloader && php artisan migrate --force && php artisan optimize:clear && php artisan queue:restart'
ssh root@gekymedia.com $remoteCmd

Pop-Location
