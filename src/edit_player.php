<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

$competition_id = $_GET['competition_id'];

$title = "選手登録";
$player_name = '';
$hit_record = '';

?>
<left>
  <font size="5"><?php echo $title; ?></font><br>
  </font>
</left>

<form action="/kyudo/?mode=register_player&competition_id= 
  <?php
  echo $competition_id;
  ?>
  " method="post">
  <input type="text" name="player_name" value=<?php
                                              echo $player_name;
                                              ?>>
  <left>
    <input type="submit" name="SaveOpt" value="Cancel" />
    <input type="submit" name="SaveOpt" value="Save" />
  </left>
</form>