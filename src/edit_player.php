<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

$title = "選手登録";
$player_name = '';
$hit_record = '';

?>
<center>
  <font size="5"><?php echo $title; ?></font><br>
  </font>
</center>

<form action="/kyudo/?mode=register_player" method="post">
  <input type="text" name="player_name" value=<?php
                                              echo $player_name;
                                              ?>>
  <center>
    <input type="submit" name="SaveOpt" value="Cancel" />
    <input type="submit" name="SaveOpt" value="Save" />
  </center>
</form>