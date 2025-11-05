# Excel Import NPM Field Fix

## Problem
When downloading the Excel template and re-uploading it, the import failed with error:
```
The npm field must be a string.
```

This happened because Excel automatically converts numeric values (like NPM: 2024010001) to numbers instead of keeping them as strings.

## Solution

### 1. Template Export (MahasiswaTemplateExport.php)
Added `WithColumnFormatting` interface to format NPM and Phone columns as text:

```php
public function columnFormats(): array
{
    return [
        'A' => NumberFormat::FORMAT_TEXT, // NPM column
        'H' => NumberFormat::FORMAT_TEXT, // Phone column
    ];
}
```

This ensures that when users download the template, Excel will treat these columns as text, not numbers.

### 2. Import Handler (MahasiswaImport.php)
Added `prepareForValidation()` method to convert numeric values back to strings before validation:

```php
public function prepareForValidation($row, $index)
{
    // Convert NPM to string if it's numeric
    if (isset($row['npm']) && is_numeric($row['npm'])) {
        $row['npm'] = (string) $row['npm'];
    }
    
    // Convert phone to string if it's numeric
    if (isset($row['phone']) && is_numeric($row['phone'])) {
        $row['phone'] = (string) $row['phone'];
    }
    
    return $row;
}
```

This handles cases where Excel still converts the values to numbers, ensuring they're converted back to strings before validation.

## Testing
1. Download the template from Filament admin panel
2. Fill in the data (or use the example data as-is)
3. Upload the file back
4. Import should succeed without "npm field must be a string" errors

## Files Modified
- `app/Exports/MahasiswaTemplateExport.php`
- `app/Imports/MahasiswaImport.php`
- `public/templates/mahasiswa-import-template.csv`
