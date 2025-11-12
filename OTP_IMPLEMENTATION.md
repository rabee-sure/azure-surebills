# OTP Verification System Implementation

This document describes the OTP (One-Time Password) verification system that has been added to the Laravel authentication flow.

## Overview

The OTP verification system adds a second authentication step after users enter their email and password. When enabled, users must verify their identity by entering a 6-digit OTP code sent to them via email, SMS, or both.

## Features

- ✅ 6-digit random OTP generation
- ✅ Secure hashing and storage of OTPs
- ✅ Configurable delivery channels (email, SMS, or both)
- ✅ Configrable expiration time (default 5 minutes)
- ✅ OTP resend functionality
- ✅ Enable/disable OTP system via configuration
- ✅ Automatic cleanup of expired OTPs
- ✅ Backward compatible with existing login system

## Configuration

Add the following environment variables to your `.env` file:

```env
# Merchant OTP Configuration
MERCHANT_OTP_ENABLED=true
MERCHANT_OTP_CHANNEL=email
MERCHANT_OTP_EXPIRATION_MINUTES=5
```

### Configuration Options

- **MERCHANT_OTP_ENABLED**: `true` or `false` (default: `false`)
  - When `false`, the system bypasses OTP and logs users in directly
  - When `true`, users must complete OTP verification after entering credentials

- **MERCHANT_OTP_CHANNEL**: `email`, `sms`, or `both` (default: `email`)
  - `email`: Send OTP via email only
  - `sms`: Send OTP via SMS only
  - `both`: Send OTP via both email and SMS

- **MERCHANT_OTP_EXPIRATION_MINUTES**: 'integer' (default: 5)
    - This minutes of code expiration

## Database Structure

### `user_otps` Table

The system creates a new table to store OTP records:

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| user_id | bigint | Foreign key to users table |
| otp_hash | string | Hashed OTP code |
| expires_at | timestamp | OTP expiration time (5 minutes from creation) |
| verified_at | timestamp (nullable) | Timestamp when OTP was verified |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

## User Flow

### With OTP Enabled

1. User enters email and password on login page
2. System validates credentials
3. If valid, system:
   - Stores `pending_user_id` in session
   - Generates 6-digit OTP
   - Hashes and stores OTP in database
   - Sends OTP via configured channel(s)
   - Redirects to OTP verification page
4. User enters the 6-digit OTP
5. System verifies OTP against hashed value
6. If valid:
   - Marks OTP as verified
   - Logs user in
   - Clears `pending_user_id` from session
   - Redirects to dashboard
7. If invalid/expired:
   - Shows error message
   - Allows user to resend OTP

### With OTP Disabled

1. User enters email and password
2. System validates credentials
3. If valid, logs user in directly (normal Laravel behavior)

## Components Created

### Models

- **`app/Models/UserOtp.php`**: Eloquent model for OTP records

### Controllers

- **`app/Http/Controllers/Auth/OtpController.php`**: Handles OTP verification and resend
  - `showVerifyForm()`: Displays OTP verification form
  - `verify()`: Verifies the entered OTP
  - `resend()`: Generates and sends a new OTP

### Services

- **`app/Services/OtpService.php`**: Business logic for OTP operations
  - `generateAndSend($user)`: Generates and sends OTP
  - `verify($user, $otp)`: Verifies OTP code
  - `cleanupExpired()`: Removes expired OTPs
  - `isEnabled()`: Checks if OTP is enabled

### Notifications

- **`app/Notifications/OtpNotification.php`**: Notification for sending OTP
- **`app/Channels/SmsChannel.php`**: Custom notification channel for SMS delivery

### Middleware

- **`app/Http/Middleware/OtpVerified.php`**: Ensures users complete OTP verification
  - Redirects to OTP page if there's a pending verification

### Commands

- **`app/Console/Commands/CleanupExpiredOtps.php`**: Artisan command to clean up expired OTPs
  - Command: `php artisan otp:cleanup`
  - Scheduled to run daily

### Views

- **`resources/views/auth/verify-otp.blade.php`**: OTP verification form

### Routes

```php
Route::get('/verify-otp', 'Auth\OtpController@showVerifyForm')->name('otp.verify.form');
Route::post('/verify-otp', 'Auth\OtpController@verify')->name('otp.verify');
Route::post('/resend-otp', 'Auth\OtpController@resend')->name('otp.resend');
```

### Migrations

- **`database/migrations/2025_10_15_172145_create_user_otps_table.php`**: Creates the `user_otps` table

## Usage Examples

### Checking if OTP is Enabled

```php
use App\Services\OtpService;

if (OtpService::isEnabled()) {
    // OTP is enabled
}
```

### Manually Generating an OTP

```php
use App\Services\OtpService;

$otpService = new OtpService();
$result = $otpService->generateAndSend($user);

if ($result['success']) {
    // OTP sent successfully
}
```

### Manually Verifying an OTP

```php
use App\Services\OtpService;

$otpService = new OtpService();
$result = $otpService->verify($user, '123456');

if ($result['success']) {
    // OTP is valid
}
```

### Running OTP Cleanup Manually

```bash
php artisan otp:cleanup
```

## Security Features

1. **Hashed Storage**: OTPs are hashed using Laravel's `Hash` facade before storage
2. **Time-Limited**: OTPs expire after 5 minutes
3. **Single Use**: Once verified, an OTP is marked and cannot be reused
4. **Session-Based**: Pending user ID is stored in session, not in URL or cookies
5. **Rate Limiting**: Uses existing Laravel login throttling

## Testing in Development

In non-production environments:
- SMS messages are logged instead of being sent
- You can check the log file at `storage/logs/laravel.log` to see generated OTPs

## Maintenance

The system automatically cleans up expired OTPs daily through Laravel's scheduler. Ensure your cron job is configured:

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

## Troubleshooting

### OTP Not Received

1. Check that `OTP_ENABLED=true` in `.env`
2. Verify `OTP_CHANNEL` is set correctly
3. For email: Check mail configuration in `.env`
4. For SMS: Verify SMS provider configuration
5. Check `storage/logs/laravel.log` for errors

### Session Expired Errors

- Session timeout is configured in `config/session.php`
- Users must complete OTP verification before session expires

### OTP Already Used

- OTPs are single-use only
- Users must request a new OTP using "Resend OTP" button

## Backward Compatibility

The implementation maintains full backward compatibility:

1. When `OTP_ENABLED=false`, the system behaves exactly as before
2. No changes to existing database tables
3. No changes to existing user authentication flows
4. Existing middleware and functionality remain unchanged

## Future Enhancements

Potential improvements that could be added:

- [ ] Configurable OTP length
- [ ] Configurable expiration time
- [ ] Rate limiting for OTP requests
- [ ] SMS verification for specific user roles only
- [ ] Remember device functionality
- [ ] OTP backup codes
- [ ] Multi-factor authentication options

## Support

For issues or questions, please contact your development team or refer to the Laravel documentation for general authentication topics.
