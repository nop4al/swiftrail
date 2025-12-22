# Swiftrail Deployment Guide

## Railway Deployment Steps

### 1. Initial Setup
- Connect GitHub repo to Railway
- Add MySQL service
- Set environment variables (APP_KEY, DB_* credentials, APP_URL, etc.)

### 2. First Deployment
The build.sh script will:
- Install composer dependencies
- Generate APP_KEY if missing
- Clear and cache configuration & routes
- Build frontend assets

**Note:** Database migrations are NOT run during deployment to prevent crashes when DB is unavailable.

### 3. Manual Migration (AFTER deployment succeeds)

Once the app is deployed and running, run migrations via Railway CLI:

```bash
railway run php artisan migrate --force
```

Or use Railway dashboard:
1. Go to Swiftrail app
2. Click "Deploy" → "Logs"
3. Switch to "Command" tab
4. Run: `php artisan migrate --force`

### 4. Verify Deployment

Test the API endpoints:
```bash
curl https://your-railway-domain/api/v1/health
curl https://your-railway-domain/api/v1/info
```

Should return JSON responses.

## Troubleshooting

### Migration Fails with "Connection refused"
- Check MySQL service is running and health
- Verify DB credentials in Railway Variables
- Ensure `DB_HOST` is the public host, not internal

### API returns 404
- Clear route cache: `railway run php artisan route:cache`
- Check deployment logs for errors

### App crashes on startup
- Check `build.sh` - migrations should be non-blocking
- Verify APP_KEY is set
- Check app logs in Railway dashboard

## Environment Variables Required

```
APP_NAME=Swiftrail
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:xxxxx
APP_URL=https://your-domain

DB_CONNECTION=mysql
DB_HOST=mysql.railway.internal
DB_PORT=3306
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=xxxxx
```
