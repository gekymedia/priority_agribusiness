<tr class="line-item-row">
    <td>
        <select name="items[{{ $index }}][egg_size]" class="form-select form-select-sm @error('items.'.$index.'.egg_size') is-invalid @enderror" required>
            @foreach(\App\Models\EggSale::sizeOptions() as $value => $label)
                <option value="{{ $value }}" {{ old('items.'.$index.'.egg_size', $item['egg_size'] ?? 'small') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('items.'.$index.'.egg_size')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </td>
    <td>
        <input type="number" name="items[{{ $index }}][quantity]" class="form-control form-control-sm line-qty @error('items.'.$index.'.quantity') is-invalid @enderror"
               value="{{ old('items.'.$index.'.quantity', $item['quantity'] ?? '') }}" min="1" required>
        @error('items.'.$index.'.quantity')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </td>
    <td>
        <input type="number" name="items[{{ $index }}][price_per_unit]" step="0.01" class="form-control form-control-sm line-price @error('items.'.$index.'.price_per_unit') is-invalid @enderror"
               value="{{ old('items.'.$index.'.price_per_unit', $item['price_per_unit'] ?? '') }}" min="0" required>
        @error('items.'.$index.'.price_per_unit')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </td>
    <td class="line-total fw-semibold">₵0.00</td>
    <td>
        <select name="items[{{ $index }}][payment_status]" class="form-select form-select-sm line-payment">
            <option value="paid" {{ old('items.'.$index.'.payment_status', $item['payment_status'] ?? 'paid') === 'paid' ? 'selected' : '' }}>Paid</option>
            <option value="unpaid" {{ old('items.'.$index.'.payment_status', $item['payment_status'] ?? '') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
        </select>
    </td>
    <td>
        <input type="text" name="items[{{ $index }}][line_notes]" class="form-control form-control-sm"
               value="{{ old('items.'.$index.'.line_notes', $item['line_notes'] ?? '') }}" placeholder="e.g. Paid on 31-05-26">
    </td>
    <td class="text-center">
        <button type="button" class="btn btn-sm btn-outline-danger remove-line-item" title="Remove line">
            <i class="fas fa-times"></i>
        </button>
    </td>
</tr>
