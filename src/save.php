<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
$record_id = NULL;
if (!empty($_POST['record_id'])) {
    $record_id = intval($_POST['record_id']);
    $task = "Refresh";
} else {
    $task = "Save";
}

// View for confirmation
$player_id = htmlspecialchars($_POST['player_id']);
$hit_record_array = $_POST['hit_record'];

$player = get_player($pdo, $player_id);

?>
<table borderwith='1'>
    <tr>
        <th colspan="2" align="left">行射記録</th>
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
                            echo $hit_record_array[$i]
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
                <tr>
                    <td>選手名</td>
                    <td>
                        <?php
                        echo $player['player_name'];
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

            if (isset($record_id)) {
                try {
                    $num = update_hit_record($pdo, $record_id, $player_id, $hit_record);
                } catch (\PDOException $e) {
                    error_log("\PDO::Exception: " . $e->getMessage());
                    echo (" error message: <br />");
                    echo ($e->getMessage());
                    return;
                }
                error_log("UPDATE: affected lins = $num");
            } else {
                try {
                    $record_id = insert_hit_record($pdo, $player_id, $hit_record);
                } catch (\PDOException $e) {
                    echo ($e->getMessage());
                    error_log("\PDO::Exception: " . $e->getMessage());
                    return;
                }
                error_log("INSERT: new id = $record_id");
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