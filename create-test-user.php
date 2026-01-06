<?php
/**
 * Quick User Creation Script
 * 
 * ⚠️ DELETE THIS FILE AFTER USE! ⚠️
 * 
 * Usage: Visit http://yoursite.local/create-test-user.php in browser
 * Or run: php create-test-user.php from command line
 */

// Load WordPress
require_once(__DIR__ . '/wp-load.php');

// ============================================
// USER CONFIGURATION
// ============================================
$username = 'test';                    // Username
$password = 'test';                    // Password
$email = 'test@example.com';           // Email
$role = 'administrator';               // Role: administrator, editor, author
$first_name = 'Test';                  // First name
$last_name = 'User';                   // Last name

// ============================================
// CREATE USER
// ============================================

// Check if user exists
if (username_exists($username)) {
    die("✗ Użytkownik '{$username}' już istnieje!\n");
}

if (email_exists($email)) {
    die("✗ Email '{$email}' jest już zarejestrowany!\n");
}

// Create user
$user_data = array(
    'user_login' => $username,
    'user_pass' => $password,
    'user_email' => $email,
    'role' => $role,
    'first_name' => $first_name,
    'last_name' => $last_name,
    'display_name' => $first_name . ' ' . $last_name,
);

$user_id = wp_insert_user($user_data);

if (is_wp_error($user_id)) {
    die("✗ Błąd: " . $user_id->get_error_message() . "\n");
}

// Success
echo "✓ Użytkownik utworzony pomyślnie!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "User ID: {$user_id}\n";
echo "Username: {$username}\n";
echo "Password: {$password}\n";
echo "Email: {$email}\n";
echo "Role: {$role}\n";
echo "Display Name: {$first_name} {$last_name}\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n";
echo "⚠️  WAŻNE: Usuń ten plik (create-test-user.php) po użyciu!\n";
echo "\n";
echo "Możesz się teraz zalogować:\n";
echo "URL: " . wp_login_url() . "\n";
echo "Username: {$username}\n";
echo "Password: {$password}\n";




