<?php
use Vtiful\Kernel\Format;

function emptyInputRegister($username, $email, $password, $r_password) {
    if (empty($username) || empty($email) || empty($password) || empty($r_password)) {
        $result = true;
    } else {
        $result = false;
    }
    return $result;
}
function invalidUsername($username) {
    if (!preg_match("/^[a-zA-Z0-9]{6,50}$/", $username)) {
        $result = true;
    } else {
        $result = false;
    }
    return $result;
}
function invalidPwd($password) {
    if (!preg_match("/^(?=.*[\da-zA-Z]).{6,}$/", $password)) {
        $result = true;
    } else {
        $result = false;
    }
    return $result;
}
function invalidEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $result = true;
    } else {
        $result = false;
    }
    return $result;
}
function pwdMatch($password, $r_password) {
    if ($password !== $r_password) {
        $result = true;
    } else {
        $result = false;
    }
    return $result;
}
function userExist($conn, $username, $email) {
    $sql = 'SELECT * FROM account_info WHERE username = ? OR email = ?;';
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header('location: ../register.php?error=stmtfailed');
        exit();
    }

    mysqli_stmt_bind_param($stmt, 'ss', $username, $email);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($resultData)) {
        mysqli_stmt_close($stmt);
        return $row;
    } else {
        mysqli_stmt_close($stmt);
        $result = false;
        return $result;
    }
}
function createUser($conn, $username, $email, $password) {
    $sql = 'INSERT INTO account_info (username, email, password, role) VALUE (?, ?, ?, ?);';
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header('location: ../register.php?error=stmtfailed');
        exit();
    }

    // $hashPwd = password_hash($password, PASSWORD_DEFAULT);
    $role = 'GUEST';
    mysqli_stmt_bind_param($stmt, 'ssss', $username, $email, $password, $role);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header('location: /btl/account/register.php?error=none');
    exit();

}



function emptyInputLogin($username, $password) {
    if (empty($username) || empty($password)) {
        $result = true;
    } else {
        $result = false;
    }
    return $result;
}
function checkedUid($conn, $username) {
    $sql = 'SELECT * FROM account_info WHERE username = ? OR email = ?;';
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header('location: ../login.php?error=stmtfailed');
        exit();
    }

    mysqli_stmt_bind_param($stmt, 'ss', $username, $username);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($resultData)) {
        mysqli_stmt_close($stmt);
        return $row;
    } else {
        mysqli_stmt_close($stmt);
        $result = false;
        return $result;
    }
}

function checkedPwd($conn, $username, $password) {
    $sql = 'SELECT * FROM account_info WHERE username = ? AND password = ?;';
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header('location: ../login.php?error=stmtfailed');
        exit();
    }

    mysqli_stmt_bind_param($stmt, 'ss', $username, $password);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($resultData)) {
        mysqli_stmt_close($stmt);
        return $row;
    } else {
        mysqli_stmt_close($stmt);
        $result = false;
        return $result;
    }
}

function loginUser($conn, $username, $password) {
    $sql = 'SELECT * FROM account_info WHERE username = ? AND password = ?;';
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header('location: /btl/account/login.php?error=stmtfailed');
        exit();
    }

    mysqli_stmt_bind_param($stmt, 'ss', $username, $password);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    $row = mysqli_fetch_assoc($resultData);

    $_SESSION['username'] = $row['username'];
    $_SESSION['user_id'] = $row['user_id'];
    $_SESSION['role'] = $row['role'];

    setcookie('user_id', $row['user_id'], time() + 86400 * 30, '/btl');
    setcookie('username', $row['username'], time() + 86400 * 30, '/btl');
    setcookie('role', $row['role'], time() + 86400 * 30, '/btl');

    mysqli_stmt_close($stmt);

    $sql = "UPDATE account_info SET last_access = ? WHERE username = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
      header('location: ../register.php?error=stmtfailed');
      exit();
    }
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $now = date("Y-m-d H:i:s");
    mysqli_stmt_bind_param($stmt, 'ss', $now, $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header('location: /btl/account/login.php?error=none');
    exit();
}