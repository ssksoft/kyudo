<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
require 'record_manager.php';
$id = NULL;

if (isset($_GET['id'])) {
    echo ($_GET['mode']);
    $id = intval($_GET['id']);
    // Get Result of selected id
    try {
        $record = getRecordById($pdo, $id);
    } catch (\PDOException $e) {
        echo ($e->getMessage());
        error_log("\PDO::Exception: " . $e->getMessage());
        return;
    }
    echo ("no exception");
    $title = "Edit($id)";
    $datetime = htmlspecialchars($record['datetime']);
    $player_name = htmlspecialchars($record['player_name']);
    $hit_record = htmlspecialchars($record['hit_record']);
} else {
    $title = "行射記録";
    $datetime = $now; // Default value is current time as template
    $player_name = '';
    $hit_record = '';
}
$record_manager = new RecordManager();
$record_str = $record_manager->get_record_as_str($hit_record);
// Form view as follows:
?>
<center>
    <font size="5"><?php echo $title; ?></font><br>
    </font>
</center>
<table>
    <tr>
        <td>
            <form action="/kyudo/?mode=save" method="post">
                <input type="hidden" name="id" value="<?php echo $id; ?>" />
                <font size=-1><tt><b>日時</b></tt></font><br />
                <input type="text" name="datetime" size="19" value="<?php echo $datetime; ?>" />
                <br />
                <table>
                    <tr>
                        <td>4本目</td>
                        <td> <select name="hit_record4">
                                <option value="○">○</option>
                                <option value="×">×</option>
                        </td>
                    </tr>
                    <tr>
                        <td>3本目</td>
                        <td> <select name="hit_record3">
                                <option value="○">○</option>
                                <option value="×">×</option>
                        </td>
                    </tr>
                    <tr>
                        <td>2本目</td>
                        <td> <select name="hit_record2">
                                <option value="○">○</option>
                                <option value="×">×</option>
                        </td>
                    <tr>
                        <td>1本目</td>
                        <td> <select name="hit_record1">
                                <option value="○">○</option>
                                <option value="×">×</option>
                                <option value=<?php
                                                echo mb_substr($record_str, 0, 1);
                                                ?> selected>
                                    <?php
                                    echo mb_substr($record_str, 0, 1);
                                    ?>
                                </option>

                        </td>
                    </tr>
                    <tr>
                        <td>選手名</td>
                        <td>
                            <input type="text" name="player_name" value=<?php
                                                                        echo $player_name;
                                                                        ?>>
                        </td>
                    </tr>
                </table>
                <center>
                    <input type="submit" name="SaveOpt" value="Cancel" />
                    <input type="submit" name="SaveOpt" value="Save" />
                </center>
            </form>
        </td>
    </tr>
</table>