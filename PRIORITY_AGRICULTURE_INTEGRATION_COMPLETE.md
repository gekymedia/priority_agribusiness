# Priority Agriculture - Priority Bank Integration Complete ✅

## What Was Implemented

1. **Priority Bank API Client** (`app/Services/PriorityBankApiClient.php`)
   - Handles communication with Priority Bank API
   - Automatic retry logic with exponential backoff
   - Idempotency support

2. **Integration Service** (`app/Services/PriorityBankIntegrationService.php`)
   - Pushes egg sales to Priority Bank as income
   - Pushes bird sales to Priority Bank as income
   - Pushes crop sales to Priority Bank as income
   - Pushes poultry expenses to Priority Bank
   - Pushes crop input expenses to Priority Bank
   - Handles category mapping

3. **Controller Updates**
   - `EggSaleController`: Automatically pushes egg sales to Priority Bank when created
   - `BirdSaleController`: Automatically pushes bird sales to Priority Bank when created
   - `ExpenseController`: Automatically pushes poultry expenses to Priority Bank when created

4. **Webhook Endpoints** (`app/Http/Controllers/PriorityBankWebhookController.php`)
   - Receives expense data from Priority Bank
   - Creates local expense records when CEO creates entries in Priority Bank
   - Note: Income webhooks are logged only (Priority Agriculture uses sale records, not direct income)

5. **Configuration**
   - Created `config/services.php` with Priority Bank settings
   - Ready for environment variable configuration

## Environment Configuration Required

Add to Priority Agriculture's `.env` file:

```env
# Priority Bank Central Finance API
PRIORITY_BANK_API_URL=https://prioritybank.gekymedia.com
PRIORITY_BANK_API_TOKEN=your_token_here
PRIORITY_BANK_API_TIMEOUT=10
PRIORITY_BANK_API_MAX_RETRIES=3
```

## How It Works

### 1. Priority Agriculture → Priority Bank (Automatic Push)

When staff creates sales/expenses in Priority Agriculture:
- **Egg Sale**: Record saved → Total amount (quantity × price) pushed to Priority Bank as income
- **Bird Sale**: Record saved → Total amount (quantity × price) pushed to Priority Bank as income
- **Crop Sale**: Record saved → Total amount (quantity × price) pushed to Priority Bank as income
- **Poultry Expense**: Record saved → Amount pushed to Priority Bank as expense
- **Crop Input Expense**: Record saved → Amount pushed to Priority Bank as expense

If Priority Bank API is unavailable, error is logged but Priority Agriculture operation succeeds.

### 2. Priority Bank → Priority Agriculture (Webhook)

When CEO creates expense in Priority Bank and selects "Priority Agriculture":
- Priority Bank saves the record
- Priority Bank sends webhook to Priority Agriculture: `https://priorityagriculture.com/api/webhook/finance/expense`
- Priority Agriculture receives webhook and creates local expense record
- Transaction is now in both systems

**Note:** Income webhooks are logged only because Priority Agriculture uses structured sale records (egg, bird, crop) rather than direct income entries.

## Income Categories in Priority Bank

- **Egg Sales**: Income from egg sales
- **Bird Sales**: Income from bird sales
- **Crop Sales**: Income from crop sales

## Expense Categories in Priority Bank

- **Feed**: Poultry feed expenses
- **Veterinary Services**: Vet services, vaccinations
- **Labor**: Labor costs
- **Medication**: Medication expenses
- **Equipment**: Equipment purchases
- **Seeds**: Crop seeds
- **Fertilizer**: Fertilizer expenses
- **Pesticides**: Pesticide expenses
- **Other Expenses**: Unmapped categories

## Testing

1. **Test Egg Sale Push:**
   - Create egg sale in Priority Agriculture
   - Check Priority Bank to see if it appears as income
   - Check logs: `storage/logs/laravel.log`

2. **Test Bird Sale Push:**
   - Create bird sale in Priority Agriculture
   - Check Priority Bank to see if it appears as income

3. **Test Expense Push:**
   - Create poultry expense in Priority Agriculture
   - Check Priority Bank to see if it appears

4. **Test Webhook:**
   - Create expense in Priority Bank
   - Select "Priority Agriculture" system
   - Check Priority Agriculture to see if expense record appears
   - Check Priority Bank logs for webhook delivery status

## Next Steps

1. Get API token from Priority Bank administrator
2. Add token to `.env` file
3. Update Priority Bank systems registry with Priority Agriculture callback URL:
   ```
   https://priorityagriculture.com
   ```
4. Test the integration
5. Monitor logs for any issues

## Notes

- Integration is non-blocking: If Priority Bank API fails, Priority Agriculture operations still succeed
- All API calls are logged for debugging
- Webhook endpoints are public (no authentication required by default - consider adding if needed)
- Income calculations: Total amount = quantity × price per unit
- Sale records include buyer information in metadata

