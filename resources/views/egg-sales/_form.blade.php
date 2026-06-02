@php
    $items = old('items', $items ?? [['egg_size' => 'small', 'quantity' => '', 'price_per_unit' => '', 'payment_status' => 'paid', 'line_notes' => '']]);
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label for="bird_batch_id" class="form-label">
            <i class="fas fa-dove me-2"></i>Bird Batch
        </label>
        <select name="bird_batch_id" id="bird_batch_id" class="form-select @error('bird_batch_id') is-invalid @enderror" required>
            <option value="">Select Batch</option>
            @foreach($batches as $batch)
                <option value="{{ $batch->id }}" {{ old('bird_batch_id', $clientSale->bird_batch_id ?? '') == $batch->id ? 'selected' : '' }}>
                    {{ $batch->batch_code }} - {{ $batch->farm->name }}
                </option>
            @endforeach
        </select>
        @error('bird_batch_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="date" class="form-label">
            <i class="fas fa-calendar me-2"></i>Sale Date
        </label>
        <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror"
               value="{{ old('date', isset($clientSale) ? $clientSale->date->format('Y-m-d') : date('Y-m-d')) }}" required>
        @error('date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="buyer_name" class="form-label">
            <i class="fas fa-user me-2"></i>Client / Buyer Name
        </label>
        <input type="text" name="buyer_name" id="buyer_name" class="form-control @error('buyer_name') is-invalid @enderror"
               value="{{ old('buyer_name', $clientSale->buyer_name ?? '') }}" placeholder="e.g. Priscilla, Hope, Collins' friend">
        @error('buyer_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="buyer_contact" class="form-label">
            <i class="fas fa-phone me-2"></i>Buyer Contact
        </label>
        <input type="text" name="buyer_contact" id="buyer_contact" class="form-control @error('buyer_contact') is-invalid @enderror"
               value="{{ old('buyer_contact', $clientSale->buyer_contact ?? '') }}">
        @error('buyer_contact')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="form-label mb-0">
                <i class="fas fa-egg me-2"></i>Egg Line Items
            </label>
            <button type="button" class="btn btn-sm btn-outline-primary" id="add_line_item">
                <i class="fas fa-plus me-1"></i>Add line
            </button>
        </div>
        <p class="text-muted small">Record each size and quantity sold to this client. Mark each line as paid or unpaid.</p>

        <div class="table-responsive">
            <table class="table table-bordered align-middle" id="line_items_table">
                <thead class="table-light">
                    <tr>
                        <th style="width: 18%">Size</th>
                        <th style="width: 12%">Qty (eggs)</th>
                        <th style="width: 15%">Price/egg (₵)</th>
                        <th style="width: 15%">Line total</th>
                        <th style="width: 15%">Payment</th>
                        <th>Notes</th>
                        <th style="width: 5%"></th>
                    </tr>
                </thead>
                <tbody id="line_items_body">
                    @foreach($items as $index => $item)
                        @include('egg-sales._line_item_row', ['index' => $index, 'item' => $item])
                    @endforeach
                </tbody>
            </table>
        </div>
        @error('items')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label text-muted">Total purchases</label>
        <p class="h5 mb-0"><strong id="sale_total_display">₵0.00</strong></p>
    </div>
    <div class="col-md-4">
        <label class="form-label text-muted">From paid lines</label>
        <p class="h5 mb-0 text-success"><strong id="paid_lines_display">₵0.00</strong></p>
    </div>
    <div class="col-md-4">
        <label for="amount_paid" class="form-label">
            <i class="fas fa-hand-holding-usd me-2"></i>Amount received (₵)
        </label>
        <input type="number" name="amount_paid" id="amount_paid" step="0.01" min="0"
               class="form-control @error('amount_paid') is-invalid @enderror"
               value="{{ old('amount_paid', $clientSale->amount_paid ?? '') }}"
               placeholder="Auto from paid lines">
        <small class="text-muted">Leave blank to use paid lines total. Override for partial client payments (e.g. Hope: 703 of 803).</small>
        @error('amount_paid')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label text-muted">Balance outstanding</label>
        <p class="h5 mb-0 text-danger"><strong id="balance_display">₵0.00</strong></p>
    </div>

    <div class="col-12">
        <label for="notes" class="form-label">
            <i class="fas fa-sticky-note me-2"></i>Notes
        </label>
        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $clientSale->notes ?? '') }}</textarea>
        @error('notes')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<template id="line_item_template">
    @include('egg-sales._line_item_row', ['index' => '__INDEX__', 'item' => ['egg_size' => 'small', 'quantity' => '', 'price_per_unit' => '', 'payment_status' => 'paid', 'line_notes' => '']])
</template>

@push('scripts')
<script>
(function() {
    var body = document.getElementById('line_items_body');
    var template = document.getElementById('line_item_template');
    var addBtn = document.getElementById('add_line_item');
    var amountPaidInput = document.getElementById('amount_paid');
    var nextIndex = body ? body.children.length : 0;

    function num(val) {
        var n = parseFloat(val);
        return isNaN(n) ? 0 : n;
    }

    function formatMoney(amount) {
        return '₵' + amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    function recalculate() {
        var total = 0;
        var paidLines = 0;

        body.querySelectorAll('tr.line-item-row').forEach(function(row) {
            var qty = num(row.querySelector('.line-qty').value);
            var price = num(row.querySelector('.line-price').value);
            var lineTotal = qty * price;
            var status = row.querySelector('.line-payment').value;
            row.querySelector('.line-total').textContent = formatMoney(lineTotal);
            total += lineTotal;
            if (status === 'paid') {
                paidLines += lineTotal;
            }
        });

        var amountPaid = amountPaidInput.value === '' ? paidLines : num(amountPaidInput.value);
        var balance = Math.max(0, total - amountPaid);

        document.getElementById('sale_total_display').textContent = formatMoney(total);
        document.getElementById('paid_lines_display').textContent = formatMoney(paidLines);
        document.getElementById('balance_display').textContent = formatMoney(balance);
    }

    function bindRow(row) {
        row.querySelectorAll('.line-qty, .line-price, .line-payment').forEach(function(el) {
            el.addEventListener('input', recalculate);
            el.addEventListener('change', recalculate);
        });
        var removeBtn = row.querySelector('.remove-line-item');
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                if (body.querySelectorAll('tr.line-item-row').length <= 1) {
                    return;
                }
                row.remove();
                recalculate();
            });
        }
    }

    if (addBtn && template && body) {
        addBtn.addEventListener('click', function() {
            var html = template.innerHTML.replace(/__INDEX__/g, nextIndex++);
            body.insertAdjacentHTML('beforeend', html);
            bindRow(body.lastElementChild);
            recalculate();
        });

        body.querySelectorAll('tr.line-item-row').forEach(bindRow);
        if (amountPaidInput) {
            amountPaidInput.addEventListener('input', recalculate);
        }
        recalculate();
    }
})();
</script>
@endpush
