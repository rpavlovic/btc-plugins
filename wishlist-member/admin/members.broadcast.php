<?php
/*
 * View Broadcast message sent to members
 */
global $wpdb;
/* delete an email */
$queue_broadcast = false;
if (isset($_POST['delete'])) {
	$ids = $_POST['wpm_broadcast_id'];
	if (empty($ids)) {
		echo "<div class='error fade'>" . __('<p>No selection to be deleted.</p>', 'wishlist-member') . "</div>";
	} else {
		$this->DeleteEmailBroadcast($ids);
		echo "<div class='updated fade'>" . __('<p>Selected Broadcasts were deleted.</p>', 'wishlist-member') . "</div>";
	}
/*Force send an email*/
} else if (isset($_POST['force_send'])) {
	$cnt_sent = $this->ForceSendMail();
	$msg_sent = ($cnt_sent == 1) ? "1 queued email was sent" : $cnt_sent . " queued emails were sent";
} else if (isset($_POST['pause'])) {
	$ids = implode(',', $_POST['wpm_broadcast_id']);
	if ($ids == "") {
		echo "<div class='error fade'>" . __('<p>No selection to be paused.</p>', 'wishlist-member') . "</div>";
	} else {
		foreach ((array) $ids AS $id) {
			$broadcast = $this->GetEmailBroadcast($id);
			if($broadcast && $broadcast->status != "Queueing"){
				$this->UpdateEmailBroadcast($id, array("status"=>"Paused"));
			}
		}
		echo "<div class='updated fade'>" . __('<p>Selected Queue were paused.</p>', 'wishlist-member') . "</div>";
	}
/*Queue an email*/
} else if (isset($_POST['queue'])) {
	$ids = implode(',', $_POST['wpm_broadcast_id']);
	if ($ids == "") {
		echo "<div class='error fade'>" . __('<p>No selection to be queued.</p>', 'wishlist-member') . "</div>";
	} else {
		foreach ((array) $ids AS $id) {
			$broadcast = $this->GetEmailBroadcast($id);
			if($broadcast && $broadcast->status == "Paused"){
				$this->UpdateEmailBroadcast($id, array("status"=>"Queued"));
			}
		}
		echo "<div class='updated fade'>" . __('<p>Selection was Queued.</p>', 'wishlist-member') . "</div>";
	}
} else if (isset( $_POST['broadcast_id'] )){
	$broadcast_id = $_POST['broadcast_id'] != "" ? $_POST['broadcast_id']: "";
	$broadcast_action = isset( $_POST['EmailBroadcastAction'] ) ? $_POST['EmailBroadcastAction']: "";
	if( !empty( $broadcast_id ) && $broadcast_action == "Save" ) {
		if ( $broadcast_id !== false ) {
			$queue_broadcast = true;
			echo "<div class='updated fade'>" . __('<p>You have successfully created your email broadcast, we are currently processing it.</p>', 'wishlist-member') . "</div>";
		}else{
			echo "<div class='error fade'>" . __('<p>An error occured while creating your broadcast, please try again.</p>', 'wishlist-member') . "</div>";
		}
	}
}else if ( isset ( $_GET['action'] ) ){
	if($_GET['action'] == "requeue" && isset($_GET['id']) && $_GET['id'] != ""){
		$id = $_GET['id'];
		if($this->RequeueEmail($id)){
			echo "<div class='updated fade'>" . __('<p>Your email has been added to queue.</p>', 'wishlist-member') . "</div>";
		}else{
			echo "<div class='error fade'>" . __('<p>An error occured while requeueing the email.</p>', 'wishlist-member') . "</div>";
		}
	}
}

if (isset($_POST['logon'])) {
	$log = false;
	echo "<div class='updated fade'>" . __('<p>Broadcast Log is Disabled.</p>', 'wishlist-member') . "</div>";
	if (isset($_POST['clear_logs'])) {
		$ret = $this->LogEmailBroadcastActivity("==Empty==", true);
		echo "<div class='updated fade'>" . __('<p>Logs Cleared.</p>', 'wishlist-member') . "</div>";
	} else {
		$ret = $this->LogEmailBroadcastActivity("**Disabled**");
	}
	$this->DeleteOption('WLM_BroadcastLog');
} elseif (isset($_POST['logoff'])) {
	$log = true;
	$this->SaveOption('WLM_BroadcastLog', '1');
	$ret = $this->LogEmailBroadcastActivity("==Log Enabled==");
	if (!$ret) {
		echo "<div class='error fade'>" . __('<p>Error Creating/Opening Log File. Please check folder permission or manually create the file ' . WLM_BACKUP_PATH . 'broadcast.txt </p>', 'wishlist-member') . "</div>";
		$this->DeleteOption('WLM_BroadcastLog');
		$log = false;
	} else {
		echo "<div class='updated fade'>" . __('<p>Broadcast Log is Enabled.</p>', 'wishlist-member') . "</div>";
	}
} else {
	if ($this->GetOption('WLM_BroadcastLog') == 1) {
		$log = true;
	} else {
		$log = false;
	}
}

//check if old stat is missing
$isold_missing = $this->IsEmailBroadcastMissingStats();
if ( $isold_missing ) {
	$this->EmailBroadcastSyncStat();
}


//get the number of emails in queue
$email_queue_count = $this->GetEmailBroadcastQueue(null,false,false,0,true);

/* variables for page numbers */
$pagenum = isset($_GET['pagenum']) ? absint(wlm_arrval($_GET,'pagenum')) : 0;
if (empty($pagenum)) $pagenum = 1;
$per_page = 20;
$start = ($pagenum == '' || $pagenum < 0) ? 0 : (($pagenum - 1) * $per_page);
$broadcast_emails = $this->GetALLEmailbroadcast(false,$start,$per_page);
$emails_count = $this->GetALLEmailbroadcast(true);

/* Prepare pagination */
$num_pages = ceil($emails_count / $per_page);
$page_links = paginate_links(array(
	'base' => add_query_arg('pagenum', '%#%'),
	'format' => '',
	'prev_text' => __('&laquo;'),
	'next_text' => __('&raquo;'),
	'total' => $num_pages,
	'current' => $pagenum
));

?>
<h2>
	<?php _e('Members &raquo; Email Broadcast', 'wishlist-member'); ?>
	<a class="button button-primary" href="?<?php echo $this->QueryString('usersearch', 'mode', 'level') ?>&mode=sendbroadcast"><?php _e('Create Email Broadcast', 'wishlist_member') ?></a>
</h2>
<br>
<form id="posts-filter" action="?<?php echo $this->QueryString('usersearch', 'mode', 'level') ?>&mode=broadcast" method="post">
	<p class="search-box">
		&nbsp;&nbsp;<input type="submit" value="<?php echo $log ? 'Disable' : 'Enable'; ?> Broadcast Log" name="<?php echo $log ? 'logon' : 'logoff'; ?>" id="log" class="button-secondary action" />
		&nbsp;<?php echo $log ? '<input type="checkbox" name="clear_logs" value="1" /><label> Clear Logs</label>' : ''; ?>
	</p>
	<p>Emails in queue: <strong><?php echo $email_queue_count <= 0 ? '0' : $email_queue_count; ?></strong>
		<?php if (count($email_queue_count) > 0 || isset($_POST['force_send'])) { ?>
			&nbsp;&nbsp;&nbsp;<a class="button" href="javascript:void(0);" id="send-mail-queue">Send Mails Left in Queue</a> &nbsp;&nbsp;<span style="color:#0000FF;"><?php echo $msg_sent; ?></span>			
		<?php } ?>
		&nbsp;&nbsp; Last Queued Email Sent:
		<strong><?php
		$Queue_Sent = $this->GetOption('WLM_Last_Queue_Sent');
		echo ($Queue_Sent == '' ? '----' : $Queue_Sent);
		?></strong>
	</p>
	<?php if ($emails_count): /* Display  Pagination */  ?>
		<div class="tablenav"><div class="tablenav-pages"><?php
		$page_links_text = sprintf('<span class="displaying-num">' . __('Displaying %s&#8211;%s of %s') . '</span>%s', number_format_i18n(( $pagenum - 1 ) * $per_page + 1), number_format_i18n(min($pagenum * $per_page, $emails_count)), number_format_i18n($emails_count), $page_links
		);
		echo $page_links_text;
		?></div>
			<input type="submit" value="Delete Selected" name="delete" id="delete" class="button-secondary action" />
			<input type="submit" value="Pause Selected" name="pause" id="pause" class="button-secondary action" />
			<input type="submit" value="Queue Selected" name="queue" id="queue" class="button-secondary action" />
		</div>
	<?php endif; /* Pagination Ends here */ ?>
	<table class="widefat" id='wpm_broadcast'>
		<thead>
            <tr>
				<th  nowrap scope="col" class="check-column"><input type="checkbox" onClick="wpm_selectAll(this,'wpm_broadcast')" /></th>
				<th scope="col" class="num"><?php _e('Subject', 'wishlist-member'); ?></th>
				<th scope="col" class="num"><?php _e('Total Recipients', 'wishlist-member'); ?></th>
				<th scope="col" class="num"><?php _e('Sent/Failed', 'wishlist-member'); ?></th>
				<th scope="col" class="num"><?php _e('Sent To', 'wishlist-member'); ?></th>
				<th scope="col" class="num"><?php _e('Sent As', 'wishlist-member'); ?></th>
				<th scope="col" class="num"><?php _e('Status', 'wishlist-member'); ?></th>
				<th scope="col" class="num"><?php _e('Date Sent', 'wishlist-member'); ?></th>
            </tr>
		</thead>
		<tbody>
		<?php foreach ($broadcast_emails AS $broadcast): 

			$qcount = $this->GetEmailBroadcastQueue($broadcast->id,false,true,0,true);
			$scount = (int)$broadcast->total_queued - (int)$qcount;
			$fcount = $this->GetFailedQueue($broadcast->id,true);	
		?>
			<tr class="<?php echo $alt++ % 2 ? '' : 'alternate'; ?>">
				<th scope="row" class="check-column"><input type="checkbox" name="wpm_broadcast_id[]" value="<?php echo $broadcast->id ?>" /></th>
				<td>
					<a href="?page=WishListMember&wl=members&mode=sendbroadcast&id=<?php echo $broadcast->id ?>"><?php echo cut_string($broadcast->subject, 30, 4); ?></a>
				</td>
				<td class="num broadcast-tq-<?php echo $broadcast->id; ?>" ><?php echo $broadcast->total_queued; ?></td>
				<td class="num"><?php
					echo '<span style="color:green;">' .$scount . '</span>' . ' / ' . '<span style="color:red;">' .$fcount . '</span>';
					if ($fcount > 0) {
						echo '<br /><a href="?page=WishListMember&wl=members&mode=broadcast&action=requeue&id=' . $broadcast->id . '">Requeue Failed</a>';
					}
					?>
				</td>
				<td class="num"><?php
					$lvl_id = explode('#', $broadcast->mlevel);
					$em = "";
					foreach ((array) $lvl_id AS $id => $level) {
						if (isset($wpm_levels[$level])) {
							$em .= "<u>" . $wpm_levels[$level]["name"] . "</u>, ";
						}else if(strpos($level,"SaveSearch") !== false){
							$em .= "<u>" . $level . "</u>, ";
						}
					}
					echo substr($em, 0, -2);
					?>
				</td>
				<td class="num"><?php echo strtoupper($broadcast->sent_as); ?></td>
				<td class="num">
					<?php
					if ($qcount <= 0 && $broadcast->status != 'Queueing') {
						echo '<span style="color:#000099">DONE</span>';
					} elseif ($broadcast->status == 'Queued') {
						echo '<span style="color:#009900">' . strtoupper($broadcast->status) . '</span>';
					} else {
						echo '<span style="color:#999999">' . strtoupper($broadcast->status) . '</span>';
					}
					?>
				</td>
				<td class="num"><?php echo $broadcast->date_added; ?></td>
			</tr >
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php if ($emails_count): /* Display  Pagination */ ?>
		<div class="tablenav"><div class="tablenav-pages"><?php
			$page_links_text = sprintf('<span class="displaying-num">' . __('Displaying %s&#8211;%s of %s') . '</span>%s', number_format_i18n(( $pagenum - 1 ) * $per_page + 1), number_format_i18n(min($pagenum * $per_page, $emails_count)), number_format_i18n($emails_count), $page_links
			);
			echo $page_links_text;
			?></div>
			<input type="submit" value="Delete Selected" name="delete" id="delete" class="button-secondary action" />
			<input type="submit" value="Pause Selected" name="pause" id="pause" class="button-secondary action" />
			<input type="submit" value="Queue Selected" name="queue" id="queue" class="button-secondary action" />
		</div>
	<?php endif; /* Pagination Ends here */ ?>
</form>

<div id="send-mail-queue-modal" style="display:none;">
 	<h3 class="modal-title">Sending mails in queue</h3>
 	<p>Emails in Queue: <strong class="email-queue-count-holder">Calculating...</strong></p>
 	<p>Sending emails:&nbsp;&nbsp;<span class="modal-sent-percent" style="font-weight:bold;">0%</span>
 		&nbsp;&nbsp;Sent:&nbsp;&nbsp;<span class="modal-sent-count" style="font-weight:bold;">0</span>
 		&nbsp;&nbsp;Failed:&nbsp;&nbsp; <span class="modal-failed-count" style="font-weight:bold;">0</span>
 	</p>
 	<p style="text-align:center;"><span class="waiting-msg">Please wait...</span><input style="display:none;"  type="button" class="button button-secondary simplemodal-close" value="Close" /></p>
</div>

<?php if($queue_broadcast) { ?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {		
			wpm_processEmailBroadcast(<?php echo $broadcast_id; ?>);
		});
	</script>
<?php } ?>

<?php
/* Cut the string */
function cut_string($str, $length, $minword) {
	$sub = '';
	$len = 0;
	foreach (explode(' ', $str) as $word) {
		$part = (($sub != '') ? ' ' : '') . $word;
		$sub .= $part;
		$len += strlen($part);
		if (strlen($word) > $minword && strlen($sub) >= $length)
			break;
	}
	return $sub . (($len < strlen($str)) ? '...' : '');
}
?>