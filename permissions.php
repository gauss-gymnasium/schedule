<?php
if (!defined('INCLUDE_ROOT')) {
    exit('Kein direkter Zugriff erlaubt.');
}

abstract class UserRole
{
    const STUDENT = 0;
    const ADMIN = 1;
}

class User
{
    private $name;
    private $pw;
    private $role;
    private $display_name;

    public function __construct(
        string $name,
        string $pw,
        int $role,
        string $display_name
    ) {
        $this->name = $name;
        $this->pw = $pw;
        $this->role = $role;
        $this->display_name = $display_name;
    }

    public function get_name()
    {
        return $this->name;
    }

    public function get_display_name()
    {
        return $this->display_name;
    }

    public function get_role()
    {
        return $this->role;
    }

    public function matches_credentials(string $name, string $pw)
    {
        return $this->name == $name && $this->pw == $pw;
    }
}

/**
 * Initializes current session with user, if the credentials in $_POST match a registered user.
 * Requires `user` and `pw` in $_POST to be set.
 * 
 * @return bool True if the login was successful
 */
function login()
{
    if ($_POST['user'] == "" || $_POST['pw'] == "") {
        return false;
    }

    foreach ($GLOBALS['users'] as $user) {
        if ($user->matches_credentials($_POST['user'], $_POST['pw'])) {
            $_SESSION['user'] = $user;
            return true;
        }
    }

    return false;
}

/**
 * @return bool True if there is a user currently logged in
 */
function is_logged_in()
{
    return isset($_SESSION['user']);
}

/**
 * @return bool True if there is a user with admin role currently logged in
 */
function is_admin()
{
    return is_logged_in() && $_SESSION['user']->get_role() == UserRole::ADMIN;
}

/**
 * @return bool True if there is a user with student role currently logged in
 */
function is_student()
{
    return is_logged_in() && $_SESSION['user']->get_role() == UserRole::STUDENT;
}

/**
 * Checks whether admin permissions are present.
 * If not, the script execution it blocked and an error message is displayed on the website.
 */
function require_admin_permission()
{
    if (!is_admin()) {
        http_response_code(401);
        echo "<p>Du hast nicht die Berechtigung, auf diese Seite zuzugreifen!<p>";
        include_once "footer.php";
        exit();
    }
}
