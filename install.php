<?php

echo "
╔═══════════════════════════════════════════════════════════════╗
║         Smart Attendance System - Auto Installer              ║
║         Sistem Absensi Hybrid (Online + Offline)             ║
╚═══════════════════════════════════════════════════════════════╝
\n";

$errors = [];

function check($label, $command, $minVersion = null) {
    global $errors;
    echo "Checking $label ... ";
    exec("$command 2>&1", $output, $returnCode);
    if ($returnCode !== 0) {
        echo "❌ NOT FOUND\n";
        $errors[] = "$label is not installed.";
        return false;
    }
    $version = trim(implode(' ', $output));
    if ($minVersion) {
        preg_match('/(\d+\.\d+(?:\.\d+)?)/', $version, $matches);
        if ($matches && version_compare($matches[1], $minVersion, '<')) {
            echo "❌ Version $version (requires >= $minVersion)\n";
            $errors[] = "$label version $version is too old (requires >= $minVersion).";
            return false;
        }
    }
    echo "✅ $version\n";
    return true;
}

// Requirements
check("PHP", "php -v", "8.3");
check("Composer", "composer --version", "2.0");
check("Node.js", "node -v", "20.0");
check("npm", "npm -v", "10.0");
check("Git", "git --version");

// Check PostgreSQL
echo "Checking PostgreSQL ... ";
$pgCheck = shell_exec("psql --version 2>&1");
if (strpos($pgCheck, 'psql') === false) {
    echo "⚠️  psql CLI not found. Make sure PostgreSQL is installed and running.\n";
    $errors[] = "PostgreSQL client (psql) not found.";
} else {
    echo "✅ $pgCheck";
}

if (!empty($errors)) {
    echo "\n❌ INSTALLATION ABORTED - Missing requirements:\n";
    foreach ($errors as $e) {
        echo "   • $e\n";
    }
    exit(1);
}

echo "\n✅ All requirements satisfied!\n\n";

// Step 1: Composer install
echo "📦 Installing PHP dependencies ...\n";
passthru("composer install --no-dev --optimize-autoloader 2>&1", $ret);
if ($ret !== 0) {
    echo "❌ Composer install failed.\n";
    exit(1);
}

// Step 2: npm install & build
echo "\n📦 Installing frontend dependencies ...\n";
passthru("npm install 2>&1", $ret);
if ($ret !== 0) {
    echo "❌ npm install failed.\n";
    exit(1);
}

echo "\n🔨 Building frontend assets ...\n";
passthru("npm run build 2>&1", $ret);
if ($ret !== 0) {
    echo "❌ npm build failed.\n";
    exit(1);
}

// Step 3: Environment
echo "\n⚙️  Setting up environment ...\n";
if (!file_exists('.env')) {
    copy('.env.example', '.env');
    echo "Created .env from .env.example\n";
} else {
    echo ".env already exists, skipping.\n";
}

// Step 4: Generate key
passthru("php artisan key:generate 2>&1");

// Step 5: Database
echo "\n🗄️  Database Setup\n";
echo "Please enter your PostgreSQL credentials (press Enter for defaults):\n";

$dbHost = readline("   Host [127.0.0.1]: ") ?: '127.0.0.1';
$dbPort = readline("   Port [5432]: ") ?: '5432';
$dbName = readline("   Database [smart_attendance]: ") ?: 'smart_attendance';
$dbUser = readline("   Username [postgres]: ") ?: 'postgres';
$dbPass = readline("   Password []: ") ?: '';

$env = file_get_contents('.env');
$env = preg_replace('/DB_HOST=.*/', "DB_HOST=$dbHost", $env);
$env = preg_replace('/DB_PORT=.*/', "DB_PORT=$dbPort", $env);
$env = preg_replace('/DB_DATABASE=.*/', "DB_DATABASE=$dbName", $env);
$env = preg_replace('/DB_USERNAME=.*/', "DB_USERNAME=$dbUser", $env);
$env = preg_replace('/DB_PASSWORD=.*/', "DB_PASSWORD=$dbPass", $env);
file_put_contents('.env', $env);

echo "\n📡 Testing database connection ...\n";
try {
    $dsn = "pgsql:host=$dbHost;port=$dbPort;dbname=$dbName";
    $pdo = new PDO($dsn, $dbUser, $dbPass);
    echo "✅ Database connection successful!\n";
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    echo "Please check your PostgreSQL credentials and try again.\n";
    exit(1);
}

echo "\n🔄 Running migrations ...\n";
passthru("php artisan migrate --force 2>&1", $ret);
if ($ret !== 0) {
    echo "❌ Migrations failed.\n";
    exit(1);
}

// Step 6: Seed data
echo "\n🌱 Seeding demo data ...\n";
passthru("php artisan db:seed --force 2>&1");

// Step 7: Storage link
echo "\n🔗 Linking storage ...\n";
passthru("php artisan storage:link 2>&1");

// Step 8: Admin credentials
echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🎉 INSTALLATION COMPLETE!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n🔗 Application URL: http://localhost:8000\n";
echo "\n👤 Default Admin Account:\n";
echo "   Email:    admin@university.ac.id\n";
echo "   Password: password\n";
echo "\n📱 Demo Accounts:\n";
echo "   Dosen:    dosen@university.ac.id / password\n";
echo "   Mahasiswa: mahasiswa@university.ac.id / password\n";
echo "\n🚀 Start the server:\n";
echo "   php artisan serve\n";
echo "\n⚡ Start queue worker (for production):\n";
echo "   php artisan queue:work\n";
echo "\n⏰ Start scheduler (for auto-expire):\n";
echo "   php artisan schedule:work\n";
echo "\n📝 Or add to crontab:\n";
echo "   * * * * * cd " . getcwd() . " && php artisan schedule:run >> /dev/null 2>&1\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
