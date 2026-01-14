<?php

namespace App\Http\Controllers;

use App\Models\PoultryExpense;
use App\Models\CropInputExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Webhook Controller for receiving finance data from Priority Bank
 */
class PriorityBankWebhookController extends Controller
{
    /**
     * Handle income webhook from Priority Bank
     * 
     * POST /api/webhook/finance/income
     */
    public function handleIncome(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_type' => 'required|in:income',
            'priority_bank_transaction_id' => 'required|integer',
            'external_transaction_id' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'channel' => 'required|in:bank,momo,cash,other',
            'notes' => 'nullable|string',
            'category' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::warning('Priority Bank webhook validation failed', [
                'errors' => $validator->errors()->toArray(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $validator->validated();

            // For Priority Agriculture, we don't create income records directly from webhooks
            // because income comes from sales (egg, bird, crop) which have specific structures.
            // We'll log it for reference.
            Log::info('Income received from Priority Bank webhook (logged only)', [
                'priority_bank_id' => $data['priority_bank_transaction_id'],
                'amount' => $data['amount'],
                'category' => $data['category'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Income logged (Priority Agriculture uses sale records, not direct income)',
            ]);

        } catch (\Exception $e) {
            Log::error('Exception handling Priority Bank income webhook', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process webhook',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Handle expense webhook from Priority Bank
     * 
     * POST /api/webhook/finance/expense
     */
    public function handleExpense(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_type' => 'required|in:expense',
            'priority_bank_transaction_id' => 'required|integer',
            'external_transaction_id' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'channel' => 'required|in:bank,momo,cash,other',
            'notes' => 'nullable|string',
            'category' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::warning('Priority Bank webhook validation failed', [
                'errors' => $validator->errors()->toArray(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $validator->validated();

            // Check if we already have this transaction
            $existing = PoultryExpense::where('description', 'LIKE', '%pb_' . $data['priority_bank_transaction_id'] . '%')->first();
            
            if ($existing) {
                Log::info('Expense already exists from Priority Bank webhook', [
                    'expense_id' => $existing->id,
                    'priority_bank_id' => $data['priority_bank_transaction_id'],
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Expense already exists',
                    'data' => $existing,
                ]);
            }

            // Create expense record
            // We'll use PoultryExpense as the default, but you may want to create a general expense model
            $expense = PoultryExpense::create([
                'farm_id' => 1, // Default farm - adjust as needed
                'category_id' => null, // You may want to map category
                'amount' => $data['amount'],
                'date' => $data['date'],
                'description' => ($data['notes'] ?? "Expense from Priority Bank - {$data['category']}") . 
                                " [PB_ID: pb_{$data['priority_bank_transaction_id']}]",
            ]);

            Log::info('Expense received from Priority Bank webhook', [
                'expense_id' => $expense->id,
                'priority_bank_id' => $data['priority_bank_transaction_id'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Expense recorded successfully',
                'data' => $expense,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Exception handling Priority Bank expense webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process webhook',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }
}

