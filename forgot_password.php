<?php # Script 16.10 - forgot_password.php
// This page allows a user to reset their password, if forgotten.

require_once ('includes/config.inc.php');
$page_title = 'Forgot Your Password';
include ('includes/header.html');
?>
</head>
<body>
<?php
include ('includes/menus.html');
?>
	<div id="page">
		<div id="content">
			<?php
		if (isset($_POST['submitted'])) {
			require_once ('mysqli_connect.php'); // Connect to the db and creates $dbc
			
			// Assume nothing:
			$uid = FALSE;

			// Validate the email address...
			if (!empty($_POST['email'])) {

				// Check for the existence of that email address...
				$q = 'SELECT id FROM employee WHERE email="'.  mysqli_real_escape_string ($dbc, $_POST['email']) . '"';
				$r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));

				if (mysqli_num_rows($r) == 1) { // Retrieve the user ID:
					list($uid) = mysqli_fetch_array ($r, MYSQLI_NUM);
				} else { // No database match made.
					echo '<p class="error">The submitted email address does not match those on file!</p>';
				}

			} else { // No email!
				echo '<p class="error">You forgot to enter your email address!</p>';
			} // End of empty($_POST['email']) IF.

			if ($uid) { // If everything's OK and employee with email address was found.

				// Create a new, random password:
				$p = substr ( md5(uniqid(rand(), true)), 3, 10);

				// Update the database:
				$q = "UPDATE employee SET password=SHA1('$p') WHERE id=$uid LIMIT 1";
				$r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));

				if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.

					// Send an email:
					$body = "Your password to log into TIMEX has been temporarily changed to '$p'. Please log in using this password and this email address. Then you may change your password to something more familiar.";
					//mail ($_POST['email'], 'Your temporary password.', $body, 'From: ealvaro@nova.edu');
					echo $body;

					// Print a message and wrap up:
					//echo '<h3>Your password has been changed. You will receive the new, temporary password at the email address with which you registered. Once you have logged in with this password, you may change it by clicking on the "Change Password" link.</h3>';
					mysqli_close($dbc);
					include ('includes/footer.html');
					exit(); // Stop the script.

				} else { // If it did not run OK.
					echo '<p class="error">Your password could not be changed due to a system error. We apologize for any inconvenience.</p>';
				}

			} else { // Failed the validation test.
				echo '<p class="error">Please try again.</p>';
			}

			mysqli_close($dbc);

		} // End of the main Submit conditional.

		?>

			<form method="post">
				<table width="100%" border="0" align="center" cellpadding="60" cellspacing="5">
					<tr valign="middle">
						<td width="90%" height="60" valign="middle">
							<h1 class="title"><?php echo $page_title;?></h1> <br />
						</td>
						<td align="right" nowrap="nowrap"></td>
					</tr>
					<tr>
						<td colspan="2" align="center">
							<center>

								<!-- status messages -->
							</center>
							<p>Enter same email address when you registered and your password will be reset.</p></td>
					</tr>
					<tr>
						<td colspan="2" align="center">
							<table align="center" cellpadding="20" cellspacing="10">
								<tr>
									<td>Email Address:</td>
									<td><input type="text" name="email" size="20" maxlength="40"
										value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>" /></td>
								</tr>
							</table> <br> </br>
						</td>
					</tr>				
				</table>
				<div align="center">
					<input type="submit" name="submit" value="Reset My Password" />
				</div>
				<input type="hidden" name="submitted" value="TRUE" />
			</form>
		</div>
		<!-- end #content -->
		<?php
		include ('includes/sidebar_signin.html');
		?>
		<div style="clear: both; height: 1px;"></div>
	</div>
	<!-- end #page -->
	<?php
	include ('includes/footer.html');
	?>
</body>
</html>
