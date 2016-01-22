<?php
	session_start();
	require_once('connection.php');

    $select_query = "SELECT * FROM users";
    $users = fetch($select_query);

    if(!isset($_SESSION['user_id']))
    {
        header('Location: index.php');
    }

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>The Wall</title>
        <link rel="stylesheet" href="style.css"/>
	</head>
    <body>
    	<div id="container">
    		<h1 id="loggedInTitle">The Wall- Logged In</h1>
<?php
            if(isset($_SESSION['user_id']))
            {
?>              <p>You have logged in!  Welcome back <?php echo $_SESSION['first_name_login'] . " " . $_SESSION['last_name_login']; ?></p>
                <p>User ID: <?php echo $_SESSION['user_id']; ?></p>
<?php       }
?>

            <h2>Post a Message:</h2>

            <form id="messageForm" action="process.php" method="post">
                <input type="hidden" name="action" value="message">
                <input type="hidden" name="id" value="<?php echo $_SESSION['user_id']; ?>">
                <textarea name="post_message"></textarea>
                <p><input type="submit" value="Post a Message"></p>
            </form>

            <div class="errors">
<?php
                if(isset($_SESSION['message_errors']))
                {
                    foreach($_SESSION['message_errors'] as $error)
                    {
                        echo "<p>$error</p>";
                    }

                    unset($_SESSION['message_errors']);
                }           
?>
            </div>
<?php
            $messages_query = "SELECT messages.message, messages.updated_at, messages.id, users.first_name, users.last_name FROM messages LEFT JOIN users ON messages.user_id = users.id";
            $messages = fetch($messages_query);

            if($messages != NULL)
            {
?>              <div id="messagesContainer">
                    <h2>Messages:</h2>
<?php               
                    foreach($messages as $message)
                    {
?>                      <div class="message"><?php echo $message['first_name'] . " " . $message['last_name'] . " " . date('F j Y', strtotime($message['updated_at'])); ?><br><?php echo $message['message']; ?><br>
                            <div class="comment">
<?php                       
                                $comments_query = "SELECT users.first_name, users.last_name, messages.message, messages.id, comments.comment, comments.id, comments.updated_at FROM comments LEFT JOIN users ON users.id = comments.user_id LEFT JOIN messages ON messages.id = comments.message_id WHERE comments.message_id = {$message['id']}";
                                $comments = fetch($comments_query);  

                                foreach($comments as $comment)
                                {
?>                                   <p><?php echo $comment['first_name'] . ' ' . $comment['last_name']; ?> <?php echo date('F j Y', strtotime($comment['updated_at'])); ?><br><?php echo $comment['comment']; ?></p>
<?php                                } 
?>
                                <form id="commentForm" action="process.php" method="post">
                                    <input type="hidden" name="action" value="comment"/>
                                    <input type="hidden" name="id" value="<?php echo $message['id']; ?>"/>
                                    <h2>Post a Comment:</h2>
                                    <textarea name="post_comment"></textarea><br>
                                    <input type="submit" value="Post a Comment"/>
                                </form>

                            </div>
                        </div>
<?php               } 
?>              </div>      
<?php            }
?>
            <p><a class="logout" href="reset.php">Log Out</a></p>
            <p><a href="reset.php">Reset Session</a></p>
        </div>
    </body>
</html> 