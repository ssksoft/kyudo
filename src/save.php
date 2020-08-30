<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
$id = NULL;
if (!empty($_POST['record_id'])) {
    $id = intval($_POST['record_id']);
    $task = "Refresh";
} else {
    $task = "Save";
}

echo $_POST['player_id'];

// View for confirmation
$player_id = htmlspecialchars($_POST['player_id']);
$hit_record_array = [$_POST['hit_record1'], $_POST['hit_record2'], $_POST['hit_record3'], $_POST['hit_record4']];
?>
<table borderwith='1'>
    <tr>
        <th align="left">記録日</th>
        <td>
            <?php
            echo $datetime;
            ?>
        </td>
    </tr>
    <tr>
        <th colspan="2" align="left">的中</th>
    </tr>
    <tr>
        <td></td>
        <td>
            <table>
                <?php
                for ($i = 0, $len = count($hit_record_array); $i < $len; ++$i) {
                    $num = $len - $i;
                ?>
                    <tr>
                        <td>
                            <?php
                            echo $num . "本目：";
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $hit_record_array[$len - 1 - $i]
                            ?>
                        </td>
                    </tr>
                <?php
                }
                ?>
                <tr>
                    <td>選手ID</td>
                    <td>
                        <?php
                        echo $player_id;
                        ?>
                    </td>
                </tr>
            </table>

            <?php
            $player_id = ($_POST['player_id']);
            $hit_record = 0;

            for ($i = 0, $len = count($hit_record_array); $i
                < $len; ++$i) {
                switch ($hit_record_array[$i]) {
                    case '○':
                        $hit_record = $hit_record + 2 ** ($len - 1 - $i);
                        break;
                    case '×':
                        $hit_record = $hit_record;
                        break;
                    default:
                        $hit_record = 9999;
                        break;
                }
            }

            if (isset($id)) {
                try {
                    $num = updateKyudo($pdo, $id, $datetime, $player_id, $hit_record);
                } catch (\PDOException $e) {
                    error_log("\PDO::Exception: " . $e->getMessage());
                    echo (" error message: <br />");
                    echo ($e->getMessage());
                    return;
                }
                error_log("UPDATE: affected lins = $num");
            } else {
                try {
                    $id = insertKyudo($pdo, $datetime, $player_id, $hit_record);
                } catch (\PDOException $e) {
                    echo ($e->getMessage());
                    error_log("\PDO::Exception: " . $e->getMessage());
                    return;
                }
                error_log("INSERT: new id = $id");
            }


            ?>
            <center>
                <table borderwith='1'>
                    <tr>
                        <td>[<a href="/kyudo/?mode=list">All</a>]</td>
                        <td>[<a href="/kyudo/?mode=edit&id=<?php {
                                                                echo $id;
                                                            } ?>">Re-edit</a>]</td>
                    </tr>
                </table>
            </center>