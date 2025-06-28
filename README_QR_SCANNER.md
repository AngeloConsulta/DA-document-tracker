# Enhanced QR Scanner Implementation

## Overview

The QR scanner has been enhanced to provide automatic scanning functionality similar to Google's QR scanner. The scanner now continuously monitors for QR codes and automatically processes them when detected. **The scanner can now find documents using either the `qr_code` field or the `qr_code_path` field**, making it more flexible for different QR code storage strategies.

## Features

### 🎯 Automatic Scanning
- **Continuous scanning**: The scanner runs continuously and automatically detects QR codes
- **Real-time feedback**: Visual indicators show scanning status and success
- **Auto-restart**: Automatically restarts after processing a scan
- **Dual field search**: Can find documents using either `qr_code` or `qr_code_path`

### 🎨 Visual Enhancements
- **Scanning overlay**: Corner indicators and center target frame
- **Animated scanning line**: Moving line animation to show active scanning
- **Success feedback**: Green border flash when QR code is successfully detected
- **Status messages**: Real-time status updates during scanning process

### 🔧 Technical Improvements
- **Enhanced camera configuration**: Optimized for automatic detection
- **Better error handling**: Comprehensive error messages for camera issues
- **Camera switching**: Ability to switch between front and back cameras
- **Permission handling**: Clear messages for camera permission issues
- **Flexible document lookup**: Supports both QR code values and file paths

## How It Works

### 1. Scanner Initialization
```javascript
// Enhanced configuration for automatic scanning
const config = {
    fps: 10,
    qrbox: { width: 250, height: 250 },
    aspectRatio: 1.0,
    disableFlip: false,
    experimentalFeatures: {
        useBarCodeDetectorIfSupported: true
    }
};
```

### 2. Automatic Detection
- Scanner runs continuously in the background
- When a QR code is detected, it automatically processes the scan
- **Document lookup searches both `qr_code` and `qr_code_path` fields**
- Visual feedback is provided to the user
- Scanner restarts automatically after processing

### 3. Document Lookup Strategy
The scanner uses a flexible lookup strategy:

```php
// Search for document using both qr_code and qr_code_path
$document = Document::where(function($query) use ($request) {
    $query->where('qr_code', $request->qr_code)
          ->orWhere('qr_code_path', $request->qr_code);
})->first();
```

This means:
- If you store QR code values in `qr_code` field → ✅ Works
- If you store QR code image paths in `qr_code_path` field → ✅ Works  
- If you store both → ✅ Works (prioritizes `qr_code` field)

### 4. Error Handling
- Camera permission errors are handled gracefully
- Clear error messages guide users to resolve issues
- Automatic retry mechanisms for common errors

## Usage

### Accessing the Scanner
1. Navigate to `/scanner` in your application
2. Grant camera permissions when prompted
3. Point the camera at a QR code
4. The scanner will automatically detect and process the QR code

### Camera Controls
- **Switch Camera**: Toggle between front and back cameras
- **Restart Scanner**: Manually restart the scanning process

### Status Updates
The scanner provides real-time status updates:
- "Initializing camera..."
- "Starting camera..."
- "Scanning for QR codes..."
- "QR Code detected! Processing..."
- "Document found!" or "Document not found"

## Database Schema

### Documents Table
```sql
ALTER TABLE documents ADD COLUMN qr_code VARCHAR(255) NULL;
CREATE INDEX idx_documents_qr_code ON documents(qr_code);
```

### QR Code Storage Options

#### Option 1: Store QR Code Values
```php
$document->qr_code = 'DOCUMENT-123-QR-CODE';
$document->qr_code_path = null; // or path to QR image
```

#### Option 2: Store QR Code Image Paths
```php
$document->qr_code = null; // or actual QR code value
$document->qr_code_path = 'documents/qr-codes/document-123.png';
```

#### Option 3: Store Both
```php
$document->qr_code = 'DOCUMENT-123-QR-CODE';
$document->qr_code_path = 'documents/qr-codes/document-123.png';
```

**The scanner will find the document regardless of which field contains the scanned value!**

## API Endpoints

### Scan QR Code
```http
POST /scanner/scan
Content-Type: application/json

{
    "qr_code": "DOCUMENT-QR-CODE-123"
}
```

**Response includes which field was matched:**
```json
{
    "success": true,
    "document": { ... },
    "message": "Document found successfully"
}
```

### Update Document Status
```http
POST /scanner/document/{document}/status
Content-Type: application/json

{
    "status_id": 2,
    "remarks": "Status updated via scanner"
}
```

### Get Available Statuses
```http
GET /scanner/statuses
```

## Security & Permissions

### Document Access Control
The scanner respects the existing document access control system:

- **Superadmin**: Can access and update any document
- **Admin**: Can access documents within their department
- **Department User**: Can access documents within their department

### Authentication Required
All scanner endpoints require authentication and proper permissions.

## Testing

Run the QR scanner tests:
```bash
php artisan test tests/Feature/QRScannerTest.php
```

The test suite covers:
- Scanner page access
- QR code detection (both `qr_code` and `qr_code_path`)
- Document lookup with different field combinations
- Invalid QR code handling
- Document status updates
- Permission-based access control
- Authentication requirements

### Test Scenarios
1. **Basic QR code detection** - Finds document by `qr_code` field
2. **Path-based detection** - Finds document by `qr_code_path` field
3. **Priority handling** - When both fields exist, prioritizes `qr_code`
4. **Different values** - Can find document when both fields have different values
5. **Permission testing** - Ensures proper access control

## CSS Animations

The scanner includes custom CSS animations for better user experience:

```css
/* Scanning line animation */
@keyframes scan-line {
    0% { transform: translateY(-50px); opacity: 0; }
    50% { opacity: 1; }
    100% { transform: translateY(50px); opacity: 0; }
}

/* Corner indicator pulsing */
@keyframes corner-pulse {
    0%, 100% { opacity: 0.5; }
    50% { opacity: 1; }
}

/* Target frame glow */
@keyframes target-glow {
    0%, 100% { box-shadow: 0 0 5px rgba(34, 197, 94, 0.5); }
    50% { box-shadow: 0 0 20px rgba(34, 197, 94, 0.8); }
}
```

## Browser Compatibility

The scanner uses the HTML5 QR Code library and requires:
- Modern browser with camera API support
- HTTPS connection (required for camera access)
- User permission to access the camera

## Troubleshooting

### Common Issues

1. **Camera not working**
   - Ensure HTTPS is enabled
   - Check camera permissions
   - Try switching cameras

2. **QR codes not detected**
   - Ensure good lighting
   - Hold camera steady
   - Check QR code quality

3. **Permission denied**
   - Allow camera access in browser settings
   - Refresh the page and try again

4. **Document not found**
   - Verify the QR code value matches either `qr_code` or `qr_code_path`
   - Check if the document exists in the database
   - Ensure proper permissions to access the document

### Error Messages
- "Camera access denied" - User needs to allow camera permissions
- "No camera found" - Device doesn't have a camera
- "Camera not supported" - Browser doesn't support camera API
- "Document not found" - QR code doesn't match any document in the system

## Migration Strategy

If you're migrating from storing only QR code paths to storing QR code values:

1. **Backward Compatibility**: The scanner will continue to work with existing `qr_code_path` values
2. **Gradual Migration**: You can gradually populate the `qr_code` field for new documents
3. **Hybrid Approach**: Use both fields during transition period

## Future Enhancements

Potential improvements for the future:
- Offline QR code processing
- Batch QR code scanning
- Custom QR code formats
- Integration with mobile apps
- Advanced analytics and reporting
- QR code generation with both value and path storage 