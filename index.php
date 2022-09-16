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
function generateRandomString($length = 7)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function passwordchecker($publicity, $pastekey)
{
    if ($publicity == "public") {
        $password = null;
        return $password;
    } else {
        $pastekey = ", '" . password_hash($pastekey, PASSWORD_DEFAULT) . "'";
        return $pastekey;
        
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="./codemirror/lib/codemirror.js"></script>
    <script src="./codemirror/mode/javascript/javascript.js"></script>
    <script src="./codemirror/mode/htmlmixed/htmlmixed.js"></script>
    <script src="./codemirror/mode/php/php.js"></script>
    <script src="./codemirror/mode/xml/xml.js"></script>
    <script src="./codemirror/mode/sql/sql.js"></script>
    <script src="./codemirror/mode/css/css.js"></script>
    <script src="./codemirror/mode/clike/clike.js"></script>
    <link rel="stylesheet" href="codemirror/theme/dracula.css">
    <link rel="stylesheet" href="codemirror/lib/codemirror.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="radiobuttoncheck.js"></script>
    <link rel="stylesheet" href="style.css">
    <title>PasteSpace</title>
    <script type="text/javascript">
        function yesnoCheck() {
            if (document.getElementById('private').checked) {
                document.getElementById('password').style.display = 'inline';
            } else document.getElementById('password').style.display = 'none';
        }

        function getSelectedMode() {
            var select = document.getElementById('selector'); 
            return select.value;
        }

        function update() {
            
            window.editor.setOption("mode",getSelectedMode());
			}

        function createEditor() {

            window.editor = CodeMirror.fromTextArea(document.getElementById('codeeditor'), {
                    mode: getSelectedMode(),
                    theme: "dracula",
                    lineNumbers: true
                });			
        }

    </script>
</head>
<body  style="background-image: url(pastespace.jpg); background-size: cover; ">
    <div class="w-full min-h-screen text-white">
        <div class="w-full flex items-center pt-14 flex-col">
            <h1 class="text-6xl text-white">PasteSpace</h1>
            <p class="text-white">Welcome to the PasteSpace</p>
        </div>
        <?php
      if (isset($_POST['submitpaste'])) {  
            $isUnique = false; 
            while (!$isUnique){
                $randomString = generateRandomString();
                $stmtlink = "SELECT * FROM paste WHERE linkname= '" . $randomString . "'";
                $linkNameExists = $pdo->query($stmtlink)->fetch(); 
                if (!$linkNameExists) {
                    $isUnique = true;
                }
            }

            $stmt = $pdo->prepare("INSERT INTO paste (codelanguage, linkname, pastename, pasteuploader, paste, created_at, access, passwords) VALUES(:codelanguage, :linkname, :pastename, :pasteuploader, :paste, NOW(), :access, :password)");


                if($_POST["publicity"] == "public") {
                    $password = NULL;
                } else {
                    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
                }
                $stmt->bindParam(":codelanguage", $_POST['text']);
                $stmt->bindParam(":pastename", $_POST['title']);
                $stmt->bindParam(":pasteuploader", $_POST['name']);
                $stmt->bindParam(":paste", $_POST['paste']);
                $stmt->bindParam(":linkname", $randomString);
                $stmt->bindParam(":access", $_POST['publicity']);
                $stmt->bindParam(":password" , $password);
                $stmt->execute();
                header("Location: pasteviewer.php/" . $randomString);
            } 

        ?>
        <div class="w-full pt-10git flex h-screen justify-center">
            <form class="w-full flex flex-col items-center" method="post">
                <div class="flex flex-row items-center w-10/12 lg:w-7/12 p-2.5  justify-around">
                    <input class="w-5/12 inputfield" type="text" placeholder="title" name="title">
                    <input class="w-5/12 inputfield" type="text" placeholder="name" name="name">
                </div>
                <textarea type="text" class="w-10/12 lg:w-7/12 p-2.5 h-80 text-white text-mono" id="codeeditor" name="paste"></textarea>
                <div class="flex flex-row w-10/12 lg:w-7/12 justify-around">
                    <div class="flex flex-col items-center">
                        <label class="text-white mb-2" for="text">Language</label>
                        <select name="text" id="selector" onchange="update()" class="flex flex-col mb-5 bg-gray-500">
                            <option value="htmlmixed" selected="selected">HTML</option>
                            <option value="css">CSS</option>
                            <option value="javascript">javaScript</option>
                            <option value="clike">PHP</option>
                            <option value="sql">SQL</option>
                        </select>
                  
                    </div>
                    <div>
                        <label for="public">open</label>
                        <input type="radio" id="public" onclick="javascript:yesnoCheck();" name="publicity" value="public" checked>
                        <label for="private">locked</label>
                        <input type="radio" id="private" onclick="javascript:yesnoCheck();" name="publicity" value="private">
                        <input class="w-5/12 inputfield" type="password" id="password" placeholder="password" name="password">

                    </div>
                </div>
                <input type="submit" name="submitpaste" class="link">
            </form>
        </div>
    </div>
    </div>
<script>

			createEditor();

    </script>
</body>
</html>