<?php
/**
 * User-related functions
 */

/**
 * Check if user is logged in by using the function from function.php
 * This prevents redeclaration errors
 *
 * @return bool True if logged in, false otherwise
 */
if (!function_exists('isUserLoggedIn')) {
    function isUserLoggedIn() {
        return isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
    }
}

// Add other user-related functions here
?>