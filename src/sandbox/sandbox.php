<!-- 参考：https://itsakura.com/jquery-ui-autocomplete -->

<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">

<label for="label1">文字を入力して下さい: </label>
<input type="text" id="input10" maxlength="5">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
  $(function() {
    const arr1 = ["田中", "abc2", "abc3"];

    // オートコンプリート
    $("#input10").autocomplete({
      source: arr1
    });
  });
</script>

<?php
include "autocomplete-datasource.php";
