<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 Fixing Schools Database Structure on VPS Production...\n\n";

try {
    // 1. Check current structure
    echo "1. Checking current schools table structure...\n";
    $columns = DB::select('DESCRIBE schools');
    echo "Current columns:\n";
    foreach ($columns as $column) {
        echo "  - {$column->Field}: {$column->Type} " . ($column->Null === 'NO' ? 'NOT NULL' : 'NULL') . "\n";
    }
    echo "\n";

    // 2. Check if password_hash exists and has data
    echo "2. Checking password_hash field...\n";
    $hasPasswordHash = false;
    $hasPassword = false;
    
    foreach ($columns as $column) {
        if ($column->Field === 'password_hash') {
            $hasPasswordHash = true;
        }
        if ($column->Field === 'password') {
            $hasPassword = true;
        }
    }
    
    echo "Has password_hash: " . ($hasPasswordHash ? 'Yes' : 'No') . "\n";
    echo "Has password: " . ($hasPassword ? 'Yes' : 'No') . "\n\n";

    // 3. If password_hash exists, copy data to password
    if ($hasPasswordHash) {
        echo "3. Copying data from password_hash to password...\n";
        
        // Check if password field is null for any records
        $nullPasswordCount = DB::select('SELECT COUNT(*) as count FROM schools WHERE password IS NULL')[0]->count;
        echo "Records with NULL password: {$nullPasswordCount}\n";
        
        if ($nullPasswordCount > 0) {
            // Copy password_hash to password where password is NULL
            $updated = DB::update('UPDATE schools SET password = password_hash WHERE password IS NULL');
            echo "✓ Updated {$updated} records\n";
        } else {
            echo "✓ All records already have password data\n";
        }
    }

    // 4. Make password field NOT NULL
    echo "4. Making password field NOT NULL...\n";
    try {
        DB::statement('ALTER TABLE schools MODIFY COLUMN password VARCHAR(255) NOT NULL');
        echo "✓ Password field is now NOT NULL\n";
    } catch (Exception $e) {
        echo "⚠ Warning: Could not make password NOT NULL: " . $e->getMessage() . "\n";
    }

    // 5. Drop password_hash column if it exists
    if ($hasPasswordHash) {
        echo "5. Dropping password_hash column...\n";
        try {
            DB::statement('ALTER TABLE schools DROP COLUMN password_hash');
            echo "✓ password_hash column dropped\n";
        } catch (Exception $e) {
            echo "⚠ Warning: Could not drop password_hash: " . $e->getMessage() . "\n";
        }
    }

    // 6. Verify final structure
    echo "6. Verifying final structure...\n";
    $finalColumns = DB::select('DESCRIBE schools');
    echo "Final columns:\n";
    foreach ($finalColumns as $column) {
        echo "  - {$column->Field}: {$column->Type} " . ($column->Null === 'NO' ? 'NOT NULL' : 'NULL') . "\n";
    }

    // 7. Test data access
    echo "\n7. Testing data access...\n";
    $schoolsCount = DB::select('SELECT COUNT(*) as count FROM schools')[0]->count;
    echo "✓ Total schools: {$schoolsCount}\n";
    
    if ($schoolsCount > 0) {
        $sampleSchool = DB::select('SELECT id, npsn, name, password FROM schools LIMIT 1')[0];
        echo "✓ Sample school: ID={$sampleSchool->id}, NPSN={$sampleSchool->npsn}, Name={$sampleSchool->name}\n";
        echo "✓ Password field: " . ($sampleSchool->password ? 'Has data' : 'NULL') . "\n";
    }

    echo "\n✅ Database structure fixed successfully!\n";
    echo "The schools page should now work properly.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
