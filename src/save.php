<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

// View for confirmation
$player_id = $_POST['player_id'];
$hit_record_array = $_POST['hit_record'];
$match_id = $_POST['match_id'];
$competition_id = $_POST['competition_id'];

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

                for ($i = 0, $len = (count($hit_record_array) / 2); $i < $len; $i++) {
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
                            echo $hit_record_array[$i * 2];
                            ?>
                        </td>
                        <td>
                            <?php
                            echo $hit_record_array[$i * 2 + 1];
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

            $current_hit_record[] = array();
            $hit_records[] = array();

            for ($person = 0; $person < 2; $person++) {
                $hit_record = 0;
                for ($i = 0; $i < 4; $i++) {
                    $current_hit_record[$i] = $hit_record_array[$i * 2 + $person];
                }
                // echo implode(',', $current_hit_record);
                for ($i = 0, $len = count($current_hit_record); $i
                    < $len; $i++) {
                    switch ($current_hit_record[$i]) {
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
                $hit_records[$person] = $hit_record;
            }

            $record_id = NULL;

            for ($current_player = 0; $current_person < 2; $current_person++) {
                $record_id = get_record_id_from_matchid_playerid($pdo, $match_id, $player_id[$current_player]);



                if (isset($record_id)) {
                    try {
                        foreach ($hit_records as $person => $hit_record) {
                            echo $player_id[$person];
                            echo "の記録を更新します。";
                            $num = update_hit_record($pdo, $record_id, $player_id[$person], $hit_record);
                            echo "記録を更新しました";
                        }
                    } catch (\PDOException $e) {
                        error_log("\PDO::Exception: " . $e->getMessage());
                        echo (" error message: <br />");
                        echo ($e->getMessage());
                        return;
                    }
                    error_log("UPDATE: affected lins = $num");
                } else {
                    try {
                        foreach ($hit_records as $person => $hit_record) {
                            echo "player_id";
                            echo $player_id[$person];
                            $record_id = insert_hit_record($pdo, $player_id[$person], $hit_record, $competition_id, $match_id);
                            echo "記録を追加しました";
                        }
                    } catch (\PDOException $e) {
                        echo ($e->getMessage());
                        error_log("\PDO::Exception: " . $e->getMessage());
                        return;
                    }
                    error_log("INSERT: new id = $record_id");
                }
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