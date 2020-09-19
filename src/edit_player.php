<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

$competition_id = $_GET['competition_id'];

$title = "選手登録";

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
  <table>
    <tr>
      <td>
        団体名
      </td>
      <td>
        <input type="text" name="team_name">
      </td>
    </tr>
    <tr>
      <td>
        選手名
      </td>
      <td>
        <input type="text" name="player_name">
      </td>
    </tr>
    <tr>
      <td>
        段位
      </td>
      <td>
        <input type="text" name="dan">
      </td>
    </tr>
  </table>
  <left>
    <input type="submit" name="SaveOpt" value="Cancel" />
    <input type="submit" name="SaveOpt" value="Save" />
  </left>
</form>