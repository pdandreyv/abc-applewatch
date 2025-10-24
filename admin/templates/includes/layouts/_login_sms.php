<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>abc-cms.com</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<link href="/admin/templates/css/reset.css" rel="stylesheet" type="text/css" />
	<link href="/admin/templates/css/style.css?0" rel="stylesheet" type="text/css" />
	<meta name="robots" content="noindex, nofollow">
	<?= html_sources('return', 'jquery.js') ?>
	<style type="text/css">
		#auth {height:100%; width:348px; margin:auto}
		#auth .form {width:348px}
		#auth .form_head {padding-left:30px; background: #4C4C4C}
		#auth .form_content {padding-top:10px; background:#E8E8E8;}
		#auth .button {float:right; margin:15px 23px 0 0}
		#auth .remember {float:left; padding:20px 0 0;}
		#auth .copyright {margin-top:20px; padding:10px 0px 40px; color:#666; font:11px Arial; border-top:0px solid #333}
		#auth .copyright div {float:right}
		#auth .copyright a {color:#666;}
		#auth .message {padding:0 0 20px 0px}
	</style>
</head>
<body class="b-size">
<table id="auth"><tr><td>
			<?=(isset($message) AND $message) ? '<div class="message"><b>'.$message.'</b></div>' : ''?>
			<form <?=@$_POST['sessioninfo']?'style="display:none"':''?> id="sign-in-form" class="form" method="post" action="#">
				<div class="form_head corner_top">АВТОРИЗАЦИЯ</div>
				<div class="form_content corner_bottom">
					<div class="field input td4">
						<label><span>Телефон:</span></label>
						<div><input id="phone-number" value=""></div>
					</div>
					<div id="recaptcha-container" style="height:130px; width: 300px"></div>
					<div class="clear"></div>
					<div class="button red"><input id="sign-in-button" type="submit" value="ВОЙТИ" /></div>
					<div class="clear"></div>
				</div>
			</form>

			<form <?=@$_POST['sessioninfo']?'':'style="display:none"'?> id="verification-code-form" class="form" method="post" action="/admin.php?m=<?=$get['m']?>">
				<div class="form_head corner_top">АВТОРИЗАЦИЯ</div>
				<div class="form_content corner_bottom">
					<?=form('input td4','code','',array('name'=>'Код:'))?>
					<textarea style="display:none" id="sessionInfo" name="sessionInfo"><?=@$_POST['sessioninfo']?></textarea>
					<div class="clear"></div>
					<div class="button red"><input type="submit" value="ВОЙТИ" /></div>
					<div class="clear"></div>
				</div>
			</form>
			<div class="copyright">
				<div><?=date('Y')?> &copy; abc-cms.com</div>
				<a href="/" target="_blank" title="перейти на сайт">перейти на сайт</a>
			</div>
		</td></tr></table>


<script src="https://www.gstatic.com/firebasejs/5.5.6/firebase.js"></script>
<script>
	// Initialize Firebase
	var config = {
		apiKey: "<?=$config['firebase_key']?>",
		authDomain: "<?=$config['firebase_project']?>.firebaseapp.com",
		databaseURL: "https://<?=$config['firebase_project']?>.firebaseio.com",
		projectId: "<?=$config['firebase_project']?>",
		storageBucket: "<?=$config['firebase_project']?>.appspot.com",
		messagingSenderId: "<?=$config['firebase_sender']?>"
	};
	firebase.initializeApp(config);
</script>

<script type="text/javascript">

	/**
	 * Set up UI event listeners and registering Firebase auth listeners.
	 */
	window.onload = function() {
		// Listening for auth state changes.
		/*
		 firebase.auth().onAuthStateChanged(function(user) {
		 if (user) {
		 // User is signed in.
		 var uid = user.uid;
		 var email = user.email;
		 var photoURL = user.photoURL;
		 var phoneNumber = user.phoneNumber;
		 var isAnonymous = user.isAnonymous;
		 var displayName = user.displayName;
		 var providerData = user.providerData;
		 var emailVerified = user.emailVerified;
		 }
		 updateSignInButtonUI();
		 updateSignInFormUI();
		 updateSignOutButtonUI();
		 updateSignedInUserStatusUI();
		 updateVerificationCodeFormUI();
		 });
		 */

		// Event bindings.
		document.getElementById('sign-in-form').addEventListener('submit', onSignInSubmit);
		//document.getElementById('sign-out-button').addEventListener('click', onSignOutClick);
		document.getElementById('phone-number').addEventListener('keyup', updateSignInButtonUI);
		document.getElementById('phone-number').addEventListener('change', updateSignInButtonUI);
		//document.getElementById('verification-code').addEventListener('keyup', updateVerifyCodeButtonUI);
		//document.getElementById('verification-code').addEventListener('change', updateVerifyCodeButtonUI);
		//document.getElementById('verification-code-form').addEventListener('submit', onVerifyCodeSubmit);
		//document.getElementById('cancel-verify-code-button').addEventListener('click', cancelVerification);

		// [START appVerifier]
		window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
			'size': 'normal',
			'callback': function(response) {
				// reCAPTCHA solved, allow signInWithPhoneNumber.
				// [START_EXCLUDE]
				updateSignInButtonUI();
				// [END_EXCLUDE]
			},
			'expired-callback': function() {
				// Response expired. Ask user to solve reCAPTCHA again.
				// [START_EXCLUDE]
				updateSignInButtonUI();
				// [END_EXCLUDE]
			}
		});
		// [END appVerifier]

		// [START renderCaptcha]
		recaptchaVerifier.render().then(function(widgetId) {
			window.recaptchaWidgetId = widgetId;
		});
		// [END renderCaptcha]
	};

	/**
	 * Function called when clicking the Login/Logout button.
	 */
	function onSignInSubmit(e) {
		e.preventDefault();
		if (isCaptchaOK() && isPhoneNumberValid()) {
			window.signingIn = true;
			updateSignInButtonUI();
			// [START signin]
			var phoneNumber = getPhoneNumberFromUserInput();
			var appVerifier = window.recaptchaVerifier;
			firebase.auth().signInWithPhoneNumber(phoneNumber, appVerifier)
				.then(function (confirmationResult) {
					//console.log(confirmationResult);
					//alert(confirmationResult);
					document.getElementById('sessionInfo').value = confirmationResult.verificationId;
					//console.log(confirmationResult);
					// SMS sent. Prompt user to type the code from the message, then sign the
					// user in with confirmationResult.confirm(code).
					window.confirmationResult = confirmationResult;
					// [START_EXCLUDE silent]
					window.signingIn = false;
					updateSignInButtonUI();
					updateVerificationCodeFormUI();
					//updateVerifyCodeButtonUI();
					updateSignInFormUI();
					// [END_EXCLUDE]
				}).catch(function (error) {
				// Error; SMS not sent
				// [START_EXCLUDE]
				console.error('Error during signInWithPhoneNumber', error);
				window.alert('Error during signInWithPhoneNumber:\n\n'
					+ error.code + '\n\n' + error.message);
				window.signingIn = false;
				updateSignInFormUI();
				updateSignInButtonUI();
				// [END_EXCLUDE]
			});
			// [END signin]
		}
	}

	/**
	 * Function called when clicking the "Verify Code" button.
	 */
	function onVerifyCodeSubmit(e) {
		e.preventDefault();
		if (!!getCodeFromUserInput()) {
			window.verifyingCode = true;
			updateVerifyCodeButtonUI();
			// [START verifyCode]
			var code = getCodeFromUserInput();
			confirmationResult.confirm(code).then(function (result) {
				// User signed in successfully.
				var user = result.user;
				// [START_EXCLUDE]
				window.verifyingCode = false;
				window.confirmationResult = null;
				updateVerificationCodeFormUI();
				// [END_EXCLUDE]
			}).catch(function (error) {
				// User couldn't sign in (bad verification code?)
				// [START_EXCLUDE]
				console.error('Error while checking the verification code', error);
				window.alert('Error while checking the verification code:\n\n'
					+ error.code + '\n\n' + error.message);
				window.verifyingCode = false;
				updateSignInButtonUI();
				updateVerifyCodeButtonUI();
				// [END_EXCLUDE]
			});
			// [END verifyCode]
		}
	}

	/**
	 * Cancels the verification code input.
	 */
	function cancelVerification(e) {
		e.preventDefault();
		window.confirmationResult = null;
		updateVerificationCodeFormUI();
		updateSignInFormUI();
	}

	/**
	 * Signs out the user when the sign-out button is clicked.
	 */
	function onSignOutClick() {
		firebase.auth().signOut();
	}

	/**
	 * Reads the verification code from the user input.
	 */
	function getCodeFromUserInput() {
		return document.getElementById('verification-code').value;
	}

	/**
	 * Reads the phone number from the user input.
	 */
	function getPhoneNumberFromUserInput() {
		return document.getElementById('phone-number').value;
	}

	/**
	 * Returns true if the phone number is valid.
	 */
	function isPhoneNumberValid() {
		var pattern = /^\+[0-9\s\-\(\)]+$/;
		var phoneNumber = getPhoneNumberFromUserInput();
		return phoneNumber.search(pattern) !== -1;
	}

	/**
	 * Returns true if the ReCaptcha is in an OK state.
	 */
	function isCaptchaOK() {
		if (typeof grecaptcha !== 'undefined'
			&& typeof window.recaptchaWidgetId !== 'undefined') {
			// [START getRecaptchaResponse]
			var recaptchaResponse = grecaptcha.getResponse(window.recaptchaWidgetId);
			// [END getRecaptchaResponse]
			return recaptchaResponse !== '';
		}
		return false;
	}

	/**
	 * Re-initializes the ReCaptacha widget.
	 */
	function resetReCaptcha() {
		if (typeof grecaptcha !== 'undefined'
			&& typeof window.recaptchaWidgetId !== 'undefined') {
			grecaptcha.reset(window.recaptchaWidgetId);
		}
	}

	/**
	 * Updates the Sign-in button state depending on ReCAptcha and form values state.
	 */
	function updateSignInButtonUI() {
		document.getElementById('sign-in-button').disabled =
			!isCaptchaOK()
			|| !isPhoneNumberValid()
			|| !!window.signingIn;
	}

	/**
	 * Updates the Verify-code button state depending on form values state.
	 */
	function updateVerifyCodeButtonUI() {
		document.getElementById('verify-code-button').disabled =
			!!window.verifyingCode
			|| !getCodeFromUserInput();
	}

	/**
	 * Updates the state of the Sign-in form.
	 */
	function updateSignInFormUI() {
		if (firebase.auth().currentUser || window.confirmationResult) {
			document.getElementById('sign-in-form').style.display = 'none';
		} else {
			resetReCaptcha();
			document.getElementById('sign-in-form').style.display = 'block';
		}
	}

	/**
	 * Updates the state of the Verify code form.
	 */
	function updateVerificationCodeFormUI() {
		if (!firebase.auth().currentUser && window.confirmationResult) {
			document.getElementById('verification-code-form').style.display = 'block';
		} else {
			document.getElementById('verification-code-form').style.display = 'none';
		}
	}

	/**
	 * Updates the state of the Sign out button.
	 */
	function updateSignOutButtonUI() {
		if (firebase.auth().currentUser) {
			document.getElementById('sign-out-button').style.display = 'block';
		} else {
			document.getElementById('sign-out-button').style.display = 'none';
		}
	}

	/**
	 * Updates the Signed in user status panel.
	 */
	function updateSignedInUserStatusUI() {
		var user = firebase.auth().currentUser;
		if (user) {
			firebase.auth().currentUser.getIdToken(/* forceRefresh */ true)
				.then(function(idToken) {
					console.log(idToken);
					//window.location.href = '/?jwt='+idToken;
				}).catch(function(error) {
				// Handle error
			});
			//document.getElementById('sign-in-status').textContent = 'Signed in';
			//document.getElementById('account-details').textContent = JSON.stringify(user, null, '  ');
		} else {
			//document.getElementById('sign-in-status').textContent = 'Signed out';
			//document.getElementById('account-details').textContent = 'null';
		}
	}
</script>


</body>
</html>