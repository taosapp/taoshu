<?php

if (isset($_POST['submit-login'])) {

    require_once '../config.php';

    $nameOrMail = $_POST["aName"];
    $password = $_POST["aPw"];

    $sql = "SELECT * FROM users WHERE username=? OR email=?;";
    $stmt = mysqli_stmt_init($con);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: ./index.php?errmsg=sqlerror");
        exit();
    }else {
        mysqli_stmt_bind_param($stmt, "ss", $nameOrMail, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if($row = mysqli_fetch_assoc($result)){
            $pwCheck = password_verify($password, $row['passwd']);
            if ($pwCheck == false) {
                header("Location: ./index.php?errmsg=wrongpw");
                exit();
            }else if($pwCheck == true){
                session_start();
                $_SESSION['uid'] = $row['id'];
                $_SESSION['user'] = $row['username'];

                /* update login time */
                $updateUserLogin="UPDATE users SET logtime = NOW() WHERE id=".$row['id']."";
                mysqli_query($con, $updateUserLogin);
                /* redirect postlist */
                header("Location: ./postlist.php");
                exit();
            }else {
                header("Location: ./index.php?login=wrongpw");
                exit();
            }
        }else{
            header("Location: ./index.php?errmsg=nouser");
            exit();
        }
    }
}else{
    header("Location: ./index.php");
    exit();
}