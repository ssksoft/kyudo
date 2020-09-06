<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

// View for confirmation
$player_id = $_POST['player_id'];
$hit_record_array = $_POST['hit_record'];
$match_id = $_POST['match_id'];
$competition_id = $_POST['competition_id'];
echo "player_id:";
echo $player_id[0];
echo $player_id[1];

for ($i = 0; $i < 2; $i++) {
    $player[$i] = get_player($pdo, $player_id[$i]);
}

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
                            echo $hit_record_array[$i][0];
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $hit_record_array[$i][0];
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
                        echo $player_id[0];
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $player_id[1];
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>選手名</td>
                    <td>
                        <?php
                        echo $player[0]['player_name'];
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $player[1]['player_name'];
                        ?>
                    </td>
                </tr>
            </table>

            <?php
            $player_id = ($_POST['player_id']);
            $hit_record = 0;

            for ($i = 0, $len = count($hit_record_array); $i
                < $len; ++$i) {
                switch ($hit_record_array[$i][0]) {
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

            $record_id = NULL;
            $record_id = get_record_id_from_matchid_playerid($pdo, $match_id, $player_id[0]);


            if (isset($record_id)) {
                try {
                    $num = update_hit_record($pdo, $record_id, $player_id[0], $hit_record);
                    echo "記録を更新しました";
                } catch (\PDOException $e) {
                    error_log("\PDO::Exception: " . $e->getMessage());
                    echo (" error message: <br />");
                    echo ($e->getMessage());
                    return;
                }
                error_log("UPDATE: affected lins = $num");
            } else {
                try {
                    echo "player_id";
                    echo $player_id[0];
                    $record_id = insert_hit_record($pdo, $player_id[0], $hit_record, $competition_id, $match_id);
                    echo "記録を追加しました";
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