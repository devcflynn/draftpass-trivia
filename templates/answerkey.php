<?php $this->layout('template', ['title' => 'Answer Key', 'alerts' => $alerts]); ?>
<form method="post">
<div style="text-align: center;">
	<table style="text-align: center;" width="98%" class="answersheet">
	<?php
		foreach($rounds AS $roundKey => $currentRound) : ?>
			<tr>
				<td class=borders></td>
				<td class=answersheet colspan="4">
				<br><br>
					<?php echo $currentRound['round_title']; ?></td>
				<td class=borders></td>
			</tr>
			<?php for ($i = 1; $i <= $currentRound['total']; $i++) :
				$answer = $database->get(
					'answerkey', //Table
					'*', // Select cols
					[ // Where
						'round' => $currentRound['round_name'],
						'question' => $i
					]
				);
				$checked = (isset($answer['shortcut']) && $answer['shortcut'] == 1) ? 'checked' : ''; ?>
				<tr>
					<td class="borders"></td>
					<td class="answersheet_right">#<?php echo $i; ?> Answer: </td>
					<td class="answersheet">
						<input type="text" 
							name="<?php echo $currentRound['input_name_prefix'].$i; ?>"
							value="<?php echo $answer['answer']; ?>"
						/>
					</td>
					<td class="answersheet_left">
						<input type="checkbox" 
							name="<?php echo $currentRound['input_name_prefix'].'c'.$i; ?>" 
							value="1" <?php echo $checked; ?> 
						/>
					</td>
					<td class="borders"></td>
				</tr>
			<?php endfor;
		endforeach; ?>
			<tr>
				<td class=borders></td>
				<td colspan=4><br><input type=submit value='Submit' name='btn-submit' id='san-button'><br><br>
					<a href=answerkey.php?delete=1>Delete Answers</a></td>
				<td class=borders></td>
			</tr>
		</form>
	</table>
</div>
