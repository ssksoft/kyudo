<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

// View for confirmation
$player_id = $_POST['player_id'];
$hit_record_array = $_POST['hit_record'];
$match_id = $_POST['match_id'];
$competition_id = $_POST['competition_id'];

const NUM_SHOOT = 4;
define("NUM_PLAYER", count($hit_record_array) / NUM_SHOOT);

for ($i = 0; $i < NUM_PLAYER; $i++) {
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
                for ($i = 0; $i < NUM_SHOOT; $i++) {
                    $num_shoot = NUM_SHOOT - $i;
                ?>
                    <tr>
                        <td>
                            <?php
                            echo $num_shoot . "本目：";
                            ?>
                        </td>

                        <?php
                        for (
                            $current_player = 0;
                            $current_player < NUM_PLAYER;
                            $current_player++
                        ) {
                        ?>
                            <td>
                                <?php
                                echo
                                    $hit_record_array[$i * NUM_PLAYER + $current_player];
                                ?>
                            </td>
                        <?php
                        }
                        ?>

                    </tr>
                <?php
                }
                ?>
                <tr>
                    <td>選手ID</td>
                    <?php
                    for ($person = 0; $person < NUM_PLAYER; $person++) {
                    ?>
                        <td>
                            <?php
                            echo $player_id[$person];
                            ?>
                        </td>
                    <?php
                    }
                    ?>
                </tr>
                <tr>
                    <td>選手名</td>
                    <?php
                    for ($person = 0; $person < NUM_PLAYER; $person++) {
                    ?>
                        <td>
                            <?php
                            echo $player[$person]['player_name'];
                            ?>
                        </td>
                    <?php
                    }
                    ?>

                </tr>
            </table>

            <?php
            $player_id = ($_POST['player_id']);

            $current_hit_record[] = array();
            $hit_records[] = array();

            for ($person = 0; $person < NUM_PLAYER; $person++) {
                $hit_record = 0;

                for ($i = 0; $i < NUM_SHOOT; $i++) {
                    $current_hit_record[$i] = $hit_record_array[$i * NUM_PLAYER + $person];
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

            for (
                $current_player = 0;
                $current_player < NUM_PLAYER;
                $current_player++
            ) {
                $record_id = get_record_id_from_matchid_playerid(
                    $pdo,
                    $match_id,
                    $player_id[$current_player]
                );

                echo "現在のループ：";
                echo $current_player;
                echo "<br/>";

                echo "レコードID：";
                echo $record_id;
                echo "<br/>";

                if (isset($record_id)) {
                    try {
                        $num = update_hit_record($pdo, $record_id, $player_id[$current_player], $hit_records[$current_player]);
                        echo $player_id[$current_player];
                        echo "の記録を更新しました。";
                        echo "<br/>";
                        echo "レコードID：";
                        echo "$record_id";
                        echo "<br/>";
                        echo "<br/>";
                    } catch (\PDOException $e) {
                        error_log("\PDO::Exception: " . $e->getMessage());
                        echo (" error message: <br />");
                        echo ($e->getMessage());
                        return;
                    }
                    error_log("UPDATE: affected lins = $num");
                } else {
                    try {
                        $record_id = insert_hit_record(
                            $pdo,
                            $player_id[$current_player],
                            $hit_records[$current_player],
                            $competition_id,
                            $match_id
                        );
                        echo $player_id[$current_player];
                        echo "の記録を追加しました。";
                        echo "<br/>";
                        echo "新規追加レコードID：";
                        echo $record_id;
                        echo "<br/>";
                        echo "<br/>";
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