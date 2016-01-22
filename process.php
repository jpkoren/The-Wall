<?php
session_start();
require_once('connection.php');

function register_user($post, $get_connection)
{
    
    $_SESSION['errors'] = [];
    
    if(!isset($post['first_name']) || $post['first_name'] == NULL)
    {
        $_SESSION['errors'][] = 'Please enter your first name';
    }
    elseif(strlen($post['first_name']) < 2 || strlen($post['first_name'] > 100))
    {
        $_SESSION['errors'][] = 'Your first name must be at least 2 characters and less than 100 characters!';
    }
    


    if(!isset($post['last_name']) || $post['last_name'] == NULL)
    {
        $_SESSION['errors'][] = 'Please enter your last name';
    }
    elseif(strlen($post['last_name']) < 2 || strlen($post['last_name']) > 100)
    {
        $_SESSION['errors'][] = 'Your last name must be at least 2 characters and less than 100 characters!';
    }
    


    if(!isset($post['email']) || $post['email'] == NULL)
    {
        $_SESSION['errors'][] = 'Please enter an email';
    }
    elseif(!filter_var($post['email'], FILTER_VALIDATE_EMAIL))
    {
        $_SESSION['errors'][] = 'Please enter a VALID email!';
    }
   

    $email_sec = mysqli_real_escape_string($get_connection, $post['email']);
    $check_email_password_query = "SELECT * FROM users WHERE users.email = '$email_sec'";
    $execute_check_email_password_query = fetch($check_email_password_query);
    
    if(count($execute_check_email_password_query) > 0)
    {
        if($execute_check_email_password_query[0]['email'] == $post['email'])
        {
            $_SESSION['errors'][] = 'This email is already in use. Please choose another email';
        }
    }
    

    if(!isset($post['password']) || $post['password'] == NULL)
    {
        $_SESSION['errors'][] = 'Please enter a password';
    }
    elseif(strlen($post['password']) < 6 || strlen($post['password']) > 20)
    {
        $_SESSION['errors'][] = 'Your password must be at least 6 characters and 20 characters or less';
    }
    

    if(!isset($post['confirm_password']) || $post['confirm_password'] == NULL)
    {
        $_SESSION['errors'][] = 'Please re-type your password';
    }
    elseif($post['password'] != $post['confirm_password'])
    {
        $_SESSION['errors'][] = 'Your password must match your re-typed password!';
    }
    

    if($_SESSION['errors'] != NULL)
    {
        header('Location: index.php');
    }
    else 
    {
        if(!isset($_SESSION['first_name']) && !isset($_SESSION['last_name']))
        {
        
            $_SESSION['first_name'] = $post['first_name'];
            $_SESSION['last_name'] = $post['last_name'];
            $first_name_sec = mysqli_real_escape_string($get_connection, $post['first_name']);
            $last_name_sec = mysqli_real_escape_string($get_connection, $post['last_name']);
            $email_sec = mysqli_real_escape_string($get_connection, $post['email']);
            $password_sec = mysqli_real_escape_string($get_connection, md5($post['password']));
            $insert_user = "INSERT INTO users(first_name, last_name, email, password, created_at, updated_at) VALUES ('$first_name_sec', '$last_name_sec', '$email_sec', '$password_sec', NOW(), NOW())";
            $execute_insert_user = run_mysql_query($insert_user);
            header('Location: index.php');
        }
    }
}


function login_user($post, $get_connection)
{

    $_SESSION['login_errors'] = [];
    $email_sec = mysqli_real_escape_string($get_connection, $post['email']);
    $password_sec = mysqli_real_escape_string($get_connection, md5($post['password']));
    $check_email_password_query = "SELECT * FROM users WHERE users.email = '$email_sec' AND users.password = '$password_sec'";
    $execute_check_email_password_query = fetch($check_email_password_query);

    if(count($execute_check_email_password_query) > 0)
    {
        $_SESSION['user_id'] = $execute_check_email_password_query[0]['id'];
        $_SESSION['first_name_login'] = $execute_check_email_password_query[0]['first_name'];
        $_SESSION['last_name_login'] = $execute_check_email_password_query[0]['last_name'];
        $_SESSION['login_success'] = 'login_success';
        header('Location: logged-in.php');
    }
    else
    {
        $_SESSION['login_errors'][] = 'Your email/password combination was not found. Please try again';
        header('Location: index.php');
    }
}


function user_message($post, $get_connection)
{

    $_SESSION['message_errors'] = [];
    
    if(!isset($post['post_message']) || $post['post_message'] == NULL)
    {
        $_SESSION['message_errors'][] = 'Please enter a comment';
    }
    elseif(strlen($post['post_message']) < 2 || strlen($post['post_message']) > 160)
    {
        $_SESSION['message_errors'][] = 'Your message must at least be 2 characters and less than 160 characters';
    }
    if($_SESSION['message_errors'] != NULL)
    {
        header('Location: logged-in.php');
    }
    else
    {
        $message_sec = mysqli_real_escape_string($get_connection, $post['post_message']);
        $user_id_sec = mysqli_real_escape_string($get_connection, $_SESSION['user_id']);
        // Insert message MySQL query
        $insert_message = "INSERT INTO messages(message, created_at, updated_at, user_id)
        VALUES ('$message_sec', NOW(), NOW(), $user_id_sec)";
        $execute_insert_message = run_mysql_query($insert_message);
        header('Location: logged-in.php');
    }
}

function user_comment($post, $get_connection)
{
    $comment_sec = mysqli_real_escape_string($get_connection, $post['post_comment']);
    $message_id_sec = mysqli_real_escape_string($get_connection, $post['id']);
    $insert_comment = "INSERT INTO comments(comment, created_at, updated_at, message_id, user_id)
        VALUES ('$comment_sec', NOW(), NOW(), $message_id_sec, {$_SESSION['user_id']})";
    $execute_insert_comment = run_mysql_query($insert_comment);
    header('Location: logged-in.php');
}


if(isset($_POST['action']) && $_POST['action'] == 'register')
{
    register_user($_POST, $connection);
}
if(isset($_POST['action']) && $_POST['action'] == 'login')
{
    login_user($_POST, $connection);
}
if(isset($_POST['action']) && $_POST['action'] == 'message')
{
    user_message($_POST, $connection);
}
if(isset($_POST['action']) && $_POST['action'] == 'comment')
{
    user_comment($_POST, $connection);
}
?>