<?php require('top.php'); ?>
<main class="container" style="padding:50px 20px"><h1>Demo contact form</h1>
<p>This form stores a demonstration message locally. It does not send email. Please use sample information.</p>
<div class="row"><div class="col-md-7"><form onsubmit="send_message(); return false;">
<p><label>Name <input class="form-control" id="name" name="name" maxlength="100" required></label></p>
<p><label>Email <input class="form-control" type="email" id="email" name="email" maxlength="190" required></label></p>
<p><label>Phone <input class="form-control" id="mobile" name="mobile" maxlength="20"></label></p>
<p><label>Message <textarea class="form-control" id="message" name="message" maxlength="2000" required></textarea></label></p>
<button class="btn btn-primary" type="submit">Save demo message</button>
</form></div></div></main>
<?php require('footer.php'); ?>
