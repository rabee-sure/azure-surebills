# HasEncryptedAttributes - Quick Reference

## 🚀 Quick Start (30 seconds)

### Step 1: Add to your model
```php
use App\Traits\HasEncryptedAttributes;

class YourModel extends Model {
    use HasEncryptedAttributes;
    
    protected $encrypted = [
        'field1',
        'field2',
    ];
}
```

### Step 2: Done! ✅
```php
// Encrypts automatically
$model->field1 = "sensitive data";
$model->save();

// Decrypts automatically  
echo $model->field1; // Returns: "sensitive data"
```

---

## 📖 Examples

### User Model
```php
protected $encrypted = [
    'iban_number',
    'ssn',
    'phone',
];
```

### Payment Model
```php
protected $encrypted = [
    'credit_card_number',
    'cvv',
];
```

### Any Model
```php
protected $encrypted = [
    'any_field_you_want',
    'to_be_encrypted',
];
```

---

## ⚙️ Database Setup

### Required: Expand column size
```sql
-- Encrypted data is longer than plain text
ALTER TABLE your_table MODIFY COLUMN field_name TEXT;
```

### Or in migration:
```php
Schema::table('your_table', function (Blueprint $table) {
    $table->text('field_name')->change();
});
```

---

## ✅ What Works (Everything!)

- ✅ `$model->field = value` - Property assignment
- ✅ `Model::create([...])` - Mass assignment
- ✅ `$model->update([...])` - Updates
- ✅ `$model->fill([...])` - Fill
- ✅ `{{ $model->field }}` - Blade templates
- ✅ `$model->toArray()` - Array conversion
- ✅ `$model->toJson()` - JSON conversion
- ✅ API Resources - Automatic
- ✅ Query results - Automatic
- ✅ Relations - Automatic

---

## ⚠️ Important

1. **Use TEXT columns** (not VARCHAR)
2. **Never change APP_KEY** after encrypting
3. **Backup your APP_KEY**
4. **Test first** in development

---

## 🐛 Troubleshooting

### Shows encrypted string?
```bash
# Check column type
SHOW COLUMNS FROM your_table WHERE Field = 'field_name';
# Should be TEXT, not VARCHAR(191)
```

### Decryption error?
```bash
# Check if APP_KEY changed
grep APP_KEY .env

# Check logs
tail -f storage/logs/laravel.log | grep "decrypt"
```

---

## 📚 Full Documentation

See `ENCRYPTED_ATTRIBUTES_GUIDE.md` for:
- Complete examples
- All use cases
- Advanced usage
- Migration guide
- Best practices

---

## 💡 Real-World Examples

### Banking App
```php
protected $encrypted = [
    'iban_number',
    'account_number',
    'swift_code',
];
```

### Medical App
```php
protected $encrypted = [
    'diagnosis',
    'medications',
    'medical_history',
];
```

### E-commerce
```php
protected $encrypted = [
    'credit_card',
    'billing_address',
];
```

### SaaS Platform
```php
protected $encrypted = [
    'api_key',
    'api_secret',
    'access_token',
];
```

---

**That's it! The trait handles everything else automatically. 🎉**

