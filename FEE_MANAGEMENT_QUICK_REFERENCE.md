# Quick Reference - Fee Management URLs

## 🎯 Main Dashboard
- **URL**: `/admin/finance/fee-management`
- **Features**: Statistics, overview, quick actions
- **Route Name**: `finance.fee-management.index`

## 📋 Fee Categories
- **URL**: `/admin/finance/fee-management/categories`
- **Features**: View, create, edit, delete categories
- **Route Name**: `finance.fee-management.categories`
- **Related Routes**:
  - Create: `finance.fee-categories.create`
  - Edit: `finance.fee-categories.edit`
  - Delete: `finance.fee-categories.destroy`

## 📊 Fee Structures
- **URL**: `/admin/finance/fee-management/structures`
- **Features**: View fee structures by class/section
- **Route Name**: `finance.fee-management.structures`
- **Related Routes**:
  - Create: `finance.fee-structures.create`
  - Edit: `finance.fee-structures.edit`
  - Delete: `finance.fee-structures.destroy`

## ⚡ Special Fees
- **URL**: `/admin/finance/fee-management/special-fees`
- **Features**: Assign additional fees to students
- **Route Name**: `finance.fee-management.special-fees`
- **Related Routes**:
  - Create: `finance.special-fees.create`
  - Edit: `finance.special-fees.edit`
  - Delete: `finance.special-fees.destroy`
  - Get Amount: `finance.special-fees.get-amount` (POST)

## 💳 Payments
- **URL**: `/admin/finance/fee-management/payments`
- **Features**: View and manage fee payments
- **Query Parameters**:
  - `search`: Search by invoice or student name
  - `status`: Filter by payment status (paid/pending)
- **Route Name**: `finance.fee-management.payments`
- **Related Routes**:
  - Record Payment: `finance.payments.create`
  - Store Payment: `finance.payments.store`

## 📈 Analytics & Reports
- **URL**: `/admin/finance/fee-management/analytics`
- **Features**: Collection analysis, reports, statistics
- **Route Name**: `finance.fee-management.analytics`

## 📥 Export
- **URL**: `/admin/finance/fee-management/export`
- **Format**: CSV
- **Route Name**: `finance.fee-management.export`

---

## Blade Template Usage Examples

### Link to Dashboard
```blade
<a href="{{ route('finance.fee-management.index') }}">Fee Management</a>
```

### Link with Parameters
```blade
<a href="{{ route('finance.fee-management.payments', ['status' => 'pending']) }}">
    View Pending Payments
</a>
```

### Create New Items
```blade
<a href="{{ route('finance.fee-categories.create') }}">New Category</a>
<a href="{{ route('finance.fee-structures.create') }}">New Structure</a>
<a href="{{ route('finance.special-fees.create') }}">Assign Special Fee</a>
```

### Export Data
```blade
<a href="{{ route('finance.fee-management.export') }}" download>
    Download Report
</a>
```

---

## Navigation Integration

Include in your sidebar layout:
```blade
@include('admin.finance.partials.navigation')
```

This will automatically:
- Show the main "Fee Management" link
- Display submenu items on active routes
- Highlight current page
- Show appropriate icons

---

## Quick Statistics Available

From `finance.fee-management.index`:

```blade
{{ $stats['total_categories'] }}      <!-- Total fee categories -->
{{ $stats['active_categories'] }}     <!-- Active categories -->
{{ $stats['total_structures'] }}      <!-- Total structures -->
{{ $stats['total_special_fees'] }}    <!-- Total special fees -->
{{ $stats['pending_payments'] }}      <!-- Pending payment count -->
{{ $stats['collected_amount'] }}      <!-- Amount collected -->
{{ $stats['pending_amount'] }}        <!-- Amount pending -->
```

---

## Search and Filter Examples

### Search Payments
```blade
<form method="GET" action="{{ route('finance.fee-management.payments') }}">
    <input type="text" name="search" placeholder="Invoice or Student Name">
    <select name="status">
        <option value="">All</option>
        <option value="paid">Paid</option>
        <option value="pending">Pending</option>
    </select>
    <button type="submit">Search</button>
</form>
```

---

## API/AJAX Endpoints

### Get Fee Amount by Category
```javascript
fetch('{{ route("finance.special-fees.get-amount") }}', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({
        fee_category_id: categoryId
    })
})
.then(r => r.json())
.then(data => console.log(data.amount))
```

---

## Notes

- All routes require authentication (`middleware(['auth'])`)
- Data is filtered by current school and academic year automatically
- Pagination: 20 items per page
- CSV export includes: Invoice No, Student, Amount, Status, Date
