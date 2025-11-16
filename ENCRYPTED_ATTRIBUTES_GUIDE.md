# HasEncryptedAttributes Trait - Complete Guide

## 🎯 Overview

The `HasEncryptedAttributes` trait provides **automatic encryption and decryption** for any model attribute. Simply specify which attributes to encrypt, and the trait handles everything else.

### ✨ Key Features:

- ✅ **Universal** - Works with ANY attribute in ANY model
- ✅ **Automatic** - Encrypts on save, decrypts on read
- ✅ **Transparent** - Works seamlessly with Eloquent
- ✅ **Smart** - Detects already encrypted values
- ✅ **Safe** - Handles errors gracefully with logging
- ✅ **Backward Compatible** - Works with existing plain text data

---

## 📚 Quick Start

### **1. Add the trait to your model:**

```php
use App\Traits\HasEncryptedAttributes;

class User extends Model
{
    use HasEncryptedAttributes;
    
    protected $encrypted = [
        'iban_number',
        'ssn',
        'credit_card',
    ];
}
```

### **2. That's it!**

```php
// Save - automatically encrypted
$user->iban_number = "SA0380000000608010167519";
$user->credit_card = "4111111111111111";
$user->save();

// Read - automatically decrypted
echo $user->iban_number; // Returns: SA0380000000608010167519
echo $user->credit_card; // Returns: 4111111111111111
```

---

## 💡 Usage Examples

### **Example 1: User Model with Sensitive Data**

```php
use App\Traits\HasEncryptedAttributes;

class User extends Model
{
    use HasEncryptedAttributes;
    
    protected $encrypted = [
        'iban_number',          // Bank account
        'ssn',                   // Social Security Number
        'passport_number',       // Passport
        'phone',                 // Phone number
        'emergency_contact',     // Emergency contact
    ];
}

// Usage
$user = new User();
$user->iban_number = "SA0380000000608010167519";
$user->ssn = "123-45-6789";
$user->passport_number = "A1234567";
$user->save();

// All fields are encrypted in database
// But automatically decrypted when accessed
echo $user->iban_number; // "SA0380000000608010167519"
```

### **Example 2: Payment Model**

```php
use App\Traits\HasEncryptedAttributes;

class Payment extends Model
{
    use HasEncryptedAttributes;
    
    protected $encrypted = [
        'credit_card_number',
        'cvv',
        'billing_address',
    ];
}

// Usage
$payment = Payment::create([
    'credit_card_number' => '4111111111111111',
    'cvv' => '123',
    'billing_address' => '123 Main St',
]);

// Automatically encrypted in database ✓
```

### **Example 3: Medical Records**

```php
use App\Traits\HasEncryptedAttributes;

class MedicalRecord extends Model
{
    use HasEncryptedAttributes;
    
    protected $encrypted = [
        'diagnosis',
        'medications',
        'allergies',
        'notes',
    ];
}

// Usage
$record = new MedicalRecord();
$record->diagnosis = "Type 2 Diabetes";
$record->medications = "Metformin 500mg";
$record->save();

// HIPAA-compliant encryption ✓
```

### **Example 4: Employee Data**

```php
use App\Traits\HasEncryptedAttributes;

class Employee extends Model
{
    use HasEncryptedAttributes;
    
    protected $encrypted = [
        'salary',
        'bank_account',
        'tax_id',
        'home_address',
    ];
}
```

### **Example 5: API Keys and Secrets**

```php
use App\Traits\HasEncryptedAttributes;

class ApiCredential extends Model
{
    use HasEncryptedAttributes;
    
    protected $encrypted = [
        'api_key',
        'api_secret',
        'access_token',
        'refresh_token',
    ];
}
```

---

## 🔧 How It Works

### **Encryption (Automatic on Save):**

```php
$user->iban_number = "SA0380000000608010167519";
$user->save();

// 1. Trait intercepts setAttribute()
// 2. Checks if 'iban_number' is in $encrypted array
// 3. Checks if value is already encrypted (skip if yes)
// 4. Encrypts: "SA0380..." → "eyJpdiI6Ik1RTU..."
// 5. Stores encrypted value in database
```

### **Decryption (Automatic on Read):**

```php
echo $user->iban_number;

// 1. Trait intercepts getAttribute()
// 2. Gets value from database: "eyJpdiI6Ik1RTU..."
// 3. Detects it's encrypted (starts with "eyJ")
// 4. Decrypts: "eyJpdiI6..." → "SA0380..."
// 5. Returns decrypted value
```

### **Smart Detection:**

The trait automatically detects if a value is already encrypted by checking if it starts with `"eyJ"` (base64 encoded JSON). This prevents double-encryption and allows the trait to work with existing plain text data.

---

## 📝 Complete Model Example

```php
<?php

namespace App\Models;

use App\Traits\HasEncryptedAttributes;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasEncryptedAttributes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'credit_card',
        'ssn',
    ];

    /**
     * The attributes that should be encrypted.
     *
     * @var array
     */
    protected $encrypted = [
        'phone',
        'address',
        'credit_card',
        'ssn',
    ];

    /**
     * Get list of encrypted attributes.
     *
     * @return array
     */
    public function getEncryptedAttributes()
    {
        return $this->encrypted;
    }
}
```

---

## 🌐 Works Everywhere

The trait works transparently with all Eloquent features:

### **Mass Assignment:**
```php
User::create([
    'name' => 'John Doe',
    'iban_number' => 'SA0380000000608010167519', // Encrypted ✓
]);
```

### **Update:**
```php
$user->update([
    'iban_number' => 'SA1234567890123456789012', // Encrypted ✓
]);
```

### **Fill:**
```php
$user->fill([
    'iban_number' => 'SA9876543210987654321098', // Encrypted ✓
]);
$user->save();
```

### **JSON Serialization:**
```php
$user->toArray();
// iban_number is automatically decrypted ✓

$user->toJson();
// iban_number is automatically decrypted ✓
```

### **Blade Templates:**
```blade
<input value="{{ $user->iban_number }}" />
<!-- Automatically decrypted ✓ -->
```

### **API Resources:**
```php
return [
    'iban_number' => $this->iban_number, // Automatically decrypted ✓
];
```

### **Query Results:**
```php
$users = User::all();
foreach ($users as $user) {
    echo $user->iban_number; // Automatically decrypted ✓
}
```

---

## 🔒 Database Storage

### **Plain Text (Before):**
```
| id | iban_number                  |
|----|------------------------------|
| 1  | SA0380000000608010167519     |
```

### **Encrypted (After):**
```
| id | iban_number                                                                   |
|----|-------------------------------------------------------------------------------|
| 1  | eyJpdiI6IlpqWW1qam52eURJcnRLUEQxSS9wQkE9PSIsInZhbHVlIjoiQnpRVE1hMm5aeE... |
```

### **Column Requirements:**

Encrypted values are much longer than plain text:
- **Plain IBAN**: ~30 characters
- **Encrypted IBAN**: ~300-400 characters

**Recommendation:**
```sql
ALTER TABLE users MODIFY COLUMN iban_number TEXT;
ALTER TABLE users MODIFY COLUMN ssn TEXT;
-- Use TEXT type for encrypted columns
```

---

## ⚠️ Important Notes

### **1. Column Size:**

Make sure your database columns are large enough:

```php
// Migration
Schema::table('users', function (Blueprint $table) {
    $table->text('iban_number')->nullable()->change();
    $table->text('credit_card')->nullable()->change();
});
```

### **2. APP_KEY is Critical:**

The trait uses Laravel's `encrypt()` and `decrypt()` functions which rely on `APP_KEY`:

```env
APP_KEY=base64:YOUR_KEY_HERE
```

**⚠️ NEVER change APP_KEY after encrypting data!**

### **3. Backward Compatibility:**

The trait works with existing plain text data:
- Plain text values are returned as-is
- When you save them, they get encrypted
- Gradual migration is possible

### **4. Error Handling:**

If decryption fails:
- Error is logged to Laravel logs
- Original (encrypted) value is returned
- Application doesn't crash

```php
// Check logs
tail -f storage/logs/laravel.log
```

### **5. Performance:**

Encryption/decryption is fast (microseconds per operation):
- Negligible performance impact
- No noticeable difference in queries
- Laravel handles caching

---

## 🔍 Debugging

### **Check if attribute is encrypted:**

```php
// In database
$raw = DB::table('users')->where('id', 1)->value('iban_number');
echo substr($raw, 0, 10); // "eyJpdiI6..." = encrypted

// Via model
$user = User::find(1);
echo $user->iban_number; // Decrypted value

// Check manually
if (strpos($raw, 'eyJ') === 0) {
    echo "Encrypted ✓";
} else {
    echo "Plain text ✗";
}
```

### **List encrypted attributes:**

```php
$user = new User();
$encrypted = $user->getEncryptedAttributes();
dd($encrypted); // ['iban_number', 'ssn', ...]
```

### **Test encryption/decryption:**

```php
$original = "SA0380000000608010167519";
$encrypted = encrypt($original);
$decrypted = decrypt($encrypted);

echo $original === $decrypted ? "✓" : "✗";
```

---

## 🚀 Migration Guide

### **For Existing Applications:**

**Step 1: Add trait to model**
```php
use HasEncryptedAttributes;

protected $encrypted = ['iban_number'];
```

**Step 2: Expand database column**
```php
// Migration
Schema::table('users', function (Blueprint $table) {
    $table->text('iban_number')->change();
});
```

**Step 3: Test with one record**
```php
$user = User::find(1);
$user->iban_number = $user->iban_number; // Re-save to encrypt
$user->save();

// Verify
$raw = DB::table('users')->where('id', 1)->value('iban_number');
echo substr($raw, 0, 10); // Should be "eyJpdiI6..."
```

**Step 4: Encrypt existing data** (optional)
```php
// Command
User::chunk(100, function ($users) {
    foreach ($users as $user) {
        if (!empty($user->iban_number)) {
            $user->iban_number = $user->iban_number;
            $user->save();
        }
    }
});
```

---

## 📊 Examples by Use Case

### **Banking/Finance:**
```php
protected $encrypted = [
    'iban_number',
    'account_number',
    'routing_number',
    'swift_code',
];
```

### **Healthcare:**
```php
protected $encrypted = [
    'medical_record_number',
    'insurance_number',
    'diagnosis',
    'prescription',
];
```

### **E-Commerce:**
```php
protected $encrypted = [
    'credit_card_number',
    'cvv',
    'billing_address',
    'shipping_address',
];
```

### **Personal Data:**
```php
protected $encrypted = [
    'ssn',
    'passport_number',
    'drivers_license',
    'date_of_birth',
];
```

### **API/Credentials:**
```php
protected $encrypted = [
    'api_key',
    'api_secret',
    'access_token',
    'private_key',
];
```

---

## ✅ Best Practices

1. **Use TEXT columns** for encrypted fields
2. **Never change APP_KEY** after encryption
3. **Backup your APP_KEY** securely
4. **Log decryption errors** for monitoring
5. **Test in development** before production
6. **Encrypt gradually** in existing applications
7. **Document encrypted fields** for your team
8. **Use HTTPS** for data in transit
9. **Regular backups** of encrypted data
10. **Monitor logs** for decryption failures

---

## 🎓 Advanced Usage

### **Custom Encryption:**

Override methods in your model:

```php
protected function encryptValue($value)
{
    // Custom encryption logic
    return my_custom_encrypt($value);
}

protected function decryptValue($value)
{
    // Custom decryption logic
    return my_custom_decrypt($value);
}
```

### **Conditional Encryption:**

```php
protected function shouldEncryptAttribute($key)
{
    // Custom logic
    if ($this->is_test_account) {
        return false; // Don't encrypt test accounts
    }
    
    return parent::shouldEncryptAttribute($key);
}
```

### **Multiple Encryption Keys:**

Use different keys for different attributes:

```php
protected function getEncryptionKey($attribute)
{
    return match($attribute) {
        'credit_card' => config('keys.payment'),
        'ssn' => config('keys.personal'),
        default => config('app.key'),
    };
}
```

---

## 📞 Support

**Check logs:**
```bash
tail -f storage/logs/laravel.log | grep "decrypt"
```

**Common errors:**
- "The payload is invalid" → Column too short or APP_KEY changed
- "Failed to decrypt attribute" → Corrupted data or wrong key
- Value shows encrypted → Trait not applied or cache issue

**Need help?**
1. Check this guide
2. Review Laravel logs
3. Verify APP_KEY hasn't changed
4. Test with fresh data

---

**Version:** 1.0  
**Laravel:** 8.x+  
**PHP:** 7.4+  
**Status:** ✅ Production Ready

