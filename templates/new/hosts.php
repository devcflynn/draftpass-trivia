<?php $this->layout('template', ['title' => 'Existing Hosts']) ?>
<form method="post" action="/hosts/">
<table align="center" width="98%" class="answersheet">
    <tr>
        <td class="borders"></td>
        <td class="answersheet" colspan="4"></td>
        <td class="borders"></td>
    </tr>
        <?php for ($i = 1; $i <= 5; $i++) :
            $existingHost = $database->get("hosts", "*", ['id' => $i]);
        ?>
            <tr>
                <td class="borders"></td>
                <td class="answersheet_right" colspan="2">#<?= $i  ?> Host: </td>
                <td class="answersheet"><input type="text" name="hosts[<?= $i  ?>]" value="<?php echo  ($existingHost) ? $existingHost['host'] : ''; ?>"></td>
                <td class="borders"></td>
            </tr>
        <?php endfor; ?>
        <tr>
            <td class=borders></td>
            <td colspan=4><br><input type=submit value='Submit' name='btn-submit' id='san-button'></td>
            <td class=borders></td>
        </tr>
</table>