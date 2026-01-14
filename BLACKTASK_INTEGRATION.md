# BlackTask Integration Guide

This document explains how to integrate Priority Agribusiness with BlackTask to sync tasks between the two systems.

## Overview

When tasks are created in Priority Agribusiness (especially medication tasks from medication calendars), they can be automatically synced to BlackTask. This allows users to see their agricultural tasks alongside their other tasks in BlackTask.

## Features

- **Automatic Task Sync**: Tasks created in Agribusiness are automatically sent to BlackTask
- **Priority Mapping**: Agribusiness priorities (low/medium/high) are mapped to BlackTask priorities (0/1/2)
- **Date Sync**: Task due dates are synced to BlackTask task_date
- **Status Sync**: Task completion status is synced between systems
- **Update Sync**: Changes to tasks in Agribusiness are reflected in BlackTask
- **Delete Sync**: Deleting a task in Agribusiness removes it from BlackTask

## Setup Instructions

### 1. Generate API Token in BlackTask

1. Log in to your BlackTask account
2. Navigate to your profile/settings
3. Generate a new API token (Sanctum token)
4. Copy the token for use in Agribusiness

### 2. Configure Agribusiness

Add the following to your `.env` file in Priority Agribusiness:

```env
# BlackTask Integration
BLACKTASK_API_URL=http://blacktask.test
BLACKTASK_API_KEY=your-api-token-here
BLACKTASK_SYNC_ENABLED=true
BLACKTASK_API_TIMEOUT=10
```

**Configuration Options:**
- `BLACKTASK_API_URL`: The base URL of your BlackTask installation (e.g., `http://blacktask.test` or `https://blacktask.yourdomain.com`)
- `BLACKTASK_API_KEY`: The API token from BlackTask (Sanctum token)
- `BLACKTASK_SYNC_ENABLED`: Set to `true` to enable task syncing, `false` to disable
- `BLACKTASK_API_TIMEOUT`: Request timeout in seconds (default: 10)

### 3. Verify Integration

1. Create a new task in Agribusiness
2. Check your BlackTask account - the task should appear there
3. Complete the task in Agribusiness - it should be marked as done in BlackTask

## How It Works

### Task Creation Flow

1. **User creates task in Agribusiness** (manually or via medication calendar)
2. **Task is saved** to Agribusiness database
3. **If sync is enabled**, task is sent to BlackTask API
4. **BlackTask creates task** for the authenticated user (based on API token)
5. **BlackTask task ID is stored** in Agribusiness for future updates

### Priority Mapping

| Agribusiness | BlackTask |
|-------------|-----------|
| low         | 0         |
| medium      | 1         |
| high        | 2         |

### Task Updates

- When a task is updated in Agribusiness, the changes are synced to BlackTask
- When a task is marked as completed in Agribusiness, it's marked as done in BlackTask
- When a task is deleted in Agribusiness, it's removed from BlackTask

### Medication Calendar Tasks

When you assign a medication calendar to a bird batch:
1. Tasks are automatically created for each scheduled medication
2. Each task is synced to BlackTask (if enabled)
3. Tasks appear in BlackTask with proper due dates and priorities
4. You'll receive notifications in BlackTask for upcoming medications

## API Endpoints Used

### BlackTask API Endpoints

- `POST /api/tasks` - Create a new task
- `PATCH /api/tasks/{id}` - Update an existing task
- `DELETE /api/tasks/{id}` - Delete a task

All endpoints require Sanctum authentication via Bearer token.

## Troubleshooting

### Tasks Not Syncing

1. **Check Configuration**: Verify `BLACKTASK_SYNC_ENABLED=true` in `.env`
2. **Check API Credentials**: Ensure `BLACKTASK_API_URL` and `BLACKTASK_API_KEY` are correct
3. **Check Logs**: Review `storage/logs/laravel.log` for sync errors
4. **Test API Connection**: Verify the BlackTask API URL is accessible

### Common Issues

**Issue**: "Failed to sync task to BlackTask"
- **Solution**: Check that the API token is valid and has proper permissions

**Issue**: "Task created but not appearing in BlackTask"
- **Solution**: Verify the API token belongs to the correct user account

**Issue**: "Tasks syncing but updates not working"
- **Solution**: Ensure the `blacktask_task_id` column exists in the tasks table (run migrations)

## Security Notes

- API tokens should be kept secure and not committed to version control
- Use HTTPS in production for API communication
- Regularly rotate API tokens for security
- Each API token is linked to a specific user account in BlackTask

## Disabling Sync

To disable task syncing without removing configuration:

```env
BLACKTASK_SYNC_ENABLED=false
```

Tasks will continue to be created in Agribusiness but won't be sent to BlackTask.

## Support

For issues or questions about the integration, check the logs in `storage/logs/laravel.log` for detailed error messages.

