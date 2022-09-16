<?php
$host = 'localhost';
$database   = 'pastbinproject';
$username = 'project2';
$password = 'project2';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$database;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    $url = "https://";
else
    $url = "http://";
$url .= $_SERVER['HTTP_HOST'];
$url .= $_SERVER['REQUEST_URI'];
$dblink = substr($url, strpos($url, ".php/") + 5);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../codemirror/lib/codemirror.js"></script>
    <script src="../codemirror/mode/javascript/javascript.js"></script>
    <script src="../codemirror/mode/htmlmixed/htmlmixed.js"></script>
    <script src="../codemirror/mode/xml/xml.js"></script>
    <script src="../codemirror/mode/sql/sql.js"></script>
    <script src="../codemirror/mode/css/css.js"></script>
    <script src="../codemirror/mode/clike/clike.js"></script>
    <link rel="stylesheet" href="../codemirror/theme/dracula.css">
    <link rel="stylesheet" href="../codemirror/lib/codemirror.css">
    <link rel="stylesheet" href="../style.css">
    <title>Document</title>
    <script type="text/javascript">
        function passwordformdeleter() {
            var f = document.getElementById("passwordform");
            f.parentNode.removeChild(f);     
        }
    </script>
</head>
<body class="text-white h-screen" style="background-image: url(../pastespace.jpg); background-size: cover;">
    <div class="w-full pt-16 flex justify-center flex-col items-center">
    <h1 class="text-5xl text-white">PasteSpace</h1>
            <p class="text-white mb-12">Welcome to the PasteSpace</p>
        <?php
        $stmt = "SELECT * FROM paste WHERE linkname='" . $dblink . "'";
        $query = $pdo->query($stmt)->fetch();
        if (@$query['linkname'] != null){
        function passwordchecker($password, $query)
        {
            if ($password == null) {
                return 1;
            } else {
                ?><form method='post' id = 'passwordform'  action=''>
                <input type='password' name='password' placeholder='Password' class="inputfield">
                <input type='submit' name='submit' value='Submit'>
                </form><?php
                if (isset($_POST['submit'])) {
                    if (password_verify($_POST['password'], $password)) {
                        return 1;
                    } else {
                        echo "Wrong password";
                    }
                }
            }
        }
        if (passwordchecker($query["passwords"], $query) == 1){
            echo '<div class="flex flex-row items-center w-10/12 lg:w-7/12 p-2.5  justify-around">
            <p class="w-5/12 inputfield" type="text" name="title">' . $query["pastename"] . '</p>
            <p class="w-5/12 inputfield" type="text" name="name">' . $query["pasteuploader"] . '</p>
            </div>
            <textarea type="text" class="w-10/12 lg:w-7/12 p-2.5 h-80 text-white text-mono" id="codeeditor" name="paste">' .  htmlspecialchars($query["paste"]) . '</textarea>
            <p class="w-2/3 inputfield mt-3" type="text" name="name">Created on ' . $query["created_at"] . '</p>
            <a href="../index.php" class="mt-5 link">make your own paste</a>
            <script type="text/javascript">passwordformdeleter();</script>';
        };} else {
            echo "<p class='w-5/12 inputfield' type='text' name='title'>This page wasn't found</p>
            <a href='../index.php' class='mt-5 link'>Return to the homepage</a>";
        }

         ?>
    </div>
   <script>
     var editor = CodeMirror.fromTextArea(document.getElementById('codeeditor'), {
    mode: "<?php echo $query["codelanguage"]?>",
    theme: "dracula",
    lineNumbers: true
        });
   </script>
</body>

</html>
