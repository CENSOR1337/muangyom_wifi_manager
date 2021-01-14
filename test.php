<?php


include './lib/library_routeros.php';

$api_lib = new routeros_api_library();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

    <table border="1">
        <thead>
            <tr>
                <th>ลำดับ<br></th>
                <th>สถานะการใช้งาน</th>
                <th>username</th>
                <th>password</th>
                <th>profile type</th>
                <th>comment<br></th>
                <th>used-time</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $Array = $api_lib->get_users();
            print_r($Array);
            foreach ($Array as $value) {
                echo "<tr>";
                echo "<td>" . $value[".id"] . "</td>";
                echo "<td>" . $value["disabled"] . "</td>";
                echo "<td>" . $value["name"] . "</td>";
                echo "<td>" . $value["password"] . "</td>";
                echo "<td>" . $value["profile"] . "</td>";
                echo "<td>" . $value["comment"] . "</td>";
                echo "<td>" . $value["uptime"] . "</td>";
            echo "</tr>";
            }
            ?>
           
        </tbody>
    </table>

</body>

</html>