# Priority Agribusiness Production Deployment Script (PowerShell)
# Live site: https://agribusiness.prioritysolutionsagency.com
# Server: gekymedia.com
# Path: /home/gekymedia/web/agribusiness.prioritysolutionsagency.com/public_html

Write-Host "Committing and pushing local changes..." -ForegroundColor Cyan
git add .
git commit -m "Deploy: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"
if ($LASTEXITCODE -ne 0) { Write-Host "No changes to commit" -ForegroundColor Yellow }
git push origin main

Write-Host "Deploying to production..." -ForegroundColor Cyan
# Using git pull instead of reset --hard to preserve local files like .env
$remoteCmd = 'cd /home/gekymedia/web/agribusiness.prioritysolutionsagency.com/public_html && git fetch origin main && git checkout main && git pull origin main && composer install --no-dev --optimize-autoloader && php artisan migrate --force && php artisan view:clear && php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan optimize && php artisan queue:restart'
ssh root@gekymedia.com $remoteCmd
