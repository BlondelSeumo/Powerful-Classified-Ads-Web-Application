<?php

return [
	
	/*
	|--------------------------------------------------------------------------
	| Emails Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines are used by the Mail notifications.
	|
	*/
	
	// built-in template
	'Whoops!' => 'Whoops!',
	'Hello!' => 'Hallo!',
	'Regards' => 'Grüße',
	"having_trouble_on_link" => "Wenn Sie Probleme haben, klicken Sie auf \":actionText \" Klicken Sie auf die Schaltfläche, kopieren Sie die folgende URL und fügen Sie sie in Ihren Webbrowser ein:",
	'All rights reserved.' => 'Alle Rechte vorbehalten.',
	
	
	// mail salutation
	'footer_salutation' => 'Mit Freundliche Grüßen,<br>:appName',
	
	
	// custom mail_footer (unused)
	'mail_footer_content'            => 'Verkaufen und kaufen Sie in Ihrer Nähe. Einfach, schnell und effizient.',
	
	
	// email_verification
	'email_verification_title'       => 'Bitte bestätige deine Email Adresse.',
	'email_verification_action'      => 'Email Adresse bestätigen',
	'email_verification_content_1'   => 'Hallo :userName !',
	'email_verification_content_2'   => 'Klicken Sie auf die Schaltfläche unten, um Ihre E-Mail-Adresse zu bestätigen.',
	'email_verification_content_3'   => 'Sie erhalten diese E-Mail, weil Sie kürzlich eine neue erstellt haben :appName Konto oder eine neue E-Mail-Adresse hinzugefügt. Wenn Sie es nicht waren, ignorieren Sie bitte diese E-Mail.',
	
	
	// post_activated (new)
	'post_activated_title'             => 'Ihre Anzeige wurde aktiviert',
	'post_activated_content_1'         => 'Hallo,',
	'post_activated_content_2'         => 'Ihre Anzeige <a href=":postUrl">:title</a> wurde aktiviert.',
	'post_activated_content_3'         => 'Es wird in Kürze von einem unserer Administratoren auf seine Online-Veröffentlichung geprüft.',
	'post_activated_content_4'         => 'Sie erhalten diese E-Mail, weil Sie kürzlich eine neue Anzeige erstellt haben :appName. Wenn Sie es nicht waren, ignorieren Sie bitte diese E-Mail.',
	
	
	// post_reviewed (new)
	'post_reviewed_title'              => 'Ihre Anzeige ist jetzt online',
	'post_reviewed_content_1'          => 'Hallo,',
	'post_reviewed_content_2'          => 'Ihre Anzeige <a href=":postUrl">:title</a> ist jetzt online.',
	'post_reviewed_content_3'          => 'Sie erhalten diese E-Mail, weil Sie kürzlich eine neue Anzeige erstellt haben :appName. Wenn Sie es nicht waren, ignorieren Sie bitte diese E-Mail.',
	
	
	// post_republished (new)
	'post_republished_title'              => 'Ihre Anzeige wurde erneut veröffentlicht',
	'post_republished_content_1'          => 'Hallo,',
	'post_republished_content_2'          => 'Ihre Anzeige <a href=":postUrl">:title</a> wurde erfolgreich neu veröffentlicht.',
	'post_republished_content_3'          => 'Sie erhalten diese E-Mail, weil Sie kürzlich eine neue Anzeige erstellt haben :appName. Wenn Sie es nicht waren, ignorieren Sie bitte diese E-Mail.',
	
	
	// post_deleted
	'post_deleted_title'               => 'Ihre Anzeige wurde gelöscht',
	'post_deleted_content_1'           => 'Hallo,',
	'post_deleted_content_2'           => 'Ihre Anzeige ":title" wurde gelöscht von <a href=":appUrl">:appName</a>',
	'post_deleted_content_3'           => 'Vielen Dank für Ihr Vertrauen und bis bald.',
	'post_deleted_content_4'           => 'Dies ist eine automatisierte E-Mail, bitte antworten Sie nicht.',
	
	
	// post_seller_contacted
	'post_seller_contacted_title'      => 'Ihre Anzeige ":title" auf :appName',
	'post_seller_contacted_content_1'  => '<strong>Kontakt Informationen:</strong>
<br>Name: :name
<br>Email address: :email
<br>Phone number: :phone',
	'post_seller_contacted_content_2'  => 'Diese E-Mail wurde Ihnen über die Anzeige gesendet ":title" Sie haben angemeldet :appName : <a href=":postUrl">:postUrl</a>',
	'post_seller_contacted_content_3'  => 'HINWEIS: Die Person, die Sie kontaktiert hat, kennt Ihre E-Mail nicht, da Sie nicht antworten werden.',
	'post_seller_contacted_content_4'  => 'Denken Sie daran, immer die Details Ihrer Kontaktperson (Name, Adresse, ...) zu überprüfen, um sicherzustellen, dass Sie im Streitfall einen Ansprechpartner haben. Wählen Sie im Allgemeinen die Lieferung des Artikels in der Hand.',
	'post_seller_contacted_content_5'  => 'Vorsicht vor verlockenden Angeboten! Seien Sie vorsichtig mit Anfragen aus dem Ausland, wenn Sie nur eine Kontakt-E-Mail haben. Die vorgeschlagene Überweisung durch Western Union oder MoneyGram kann durchaus künstlich sein.',
	'post_seller_contacted_content_6'  => 'Vielen Dank für Ihr Vertrauen und bis bald.',
	'post_seller_contacted_content_7'  => 'Dies ist eine automatisierte E-Mail, bitte antworten Sie nicht.',
	
	
	// user_deleted
	'user_deleted_title'             => 'Ihr Konto wurde am gelöscht :appName',
	'user_deleted_content_1'         => 'Hallo,',
	'user_deleted_content_2'         => 'Ihr Konto wurde gelöscht von <a href=":appUrl">:appName</a> am :jetzt.',
	'user_deleted_content_3'         => 'Vielen Dank für Ihr Vertrauen und bis bald.',
	'user_deleted_content_4'         => 'Dies ist eine automatisierte E-Mail, bitte antworten Sie nicht.',
	
	
	// user_activated (new)
	'user_activated_title'           => 'Willkommen zu :appName !',
	'user_activated_content_1'       => 'Willkommen zu :appName :userName !',
	'user_activated_content_2'       => 'Ihr Konto wurde aktiviert.',
	'user_activated_content_3'       => '<strong>Hinweis: :appName Team empfiehlt Ihnen:</strong>
<br><br>1 - Achten Sie immer darauf, dass Werbetreibende sich weigern, Ihnen das zum Verkauf oder zur Vermietung angebotene Gut zu zeigen,
<br>2 - Senden Sie niemals Geld per Western Union oder einem anderen internationalen Mandat.
<br><br> Wenn Sie Zweifel an der Ernsthaftigkeit eines Werbetreibenden haben, setzen Sie sich bitte umgehend mit uns in Verbindung. Wir können dann so schnell wie möglich neutralisieren und verhindern, dass jemand, der weniger informiert ist, zum Opfer wird.',
	'user_activated_content_4'       => 'Sie erhalten diese E-Mail, weil Sie kürzlich eine neue erstellt haben :appName account. Wenn Sie es nicht waren, ignorieren Sie bitte diese E-Mail.',
	
	
	// reset_password
	'reset_password_title'           => 'Setze dein Passwort zurück',
	'reset_password_action'          => 'Passwort zurücksetzen',
	'reset_password_content_1'       => 'Passwort vergessen?',
	'reset_password_content_2'       => 'Lass uns dir einen neuen besorgen.',
	'reset_password_content_3'       => 'Wenn Sie kein Zurücksetzen des Kennworts angefordert haben, sind keine weiteren Maßnahmen erforderlich.',
	
	
	// contact_form
	'contact_form_title'             => 'Neue Nachricht von :appName',
	
	
	// post_report_sent
	'post_report_sent_title'           => 'Neuer Missbrauchsbericht',
	'Post URL'                         => 'Post URL',
	
	
	// post_archived
	'post_archived_title'              => 'Ihre Anzeige wurde archiviert',
	'post_archived_content_1'          => 'Hallo,',
	'post_archived_content_2'          => 'Ihre Anzeige ":title" wurde archiviert von :appName',
	'post_archived_content_3'          => 'Sie können es erneut veröffentlichen, indem Sie hier klicken : <a href=":repostUrl">:repostUrl</a>',
	'post_archived_content_4'          => 'Wenn Sie nichts tun, wird Ihre Anzeige dauerhaft gelöscht :dateDel.',
	'post_archived_content_5'          => 'Vielen Dank für Ihr Vertrauen und bis bald.',
	'post_archived_content_6'          => 'Dies ist eine automatisierte E-Mail, bitte antworten Sie nicht.',
	
	
	// post_will_be_deleted
	'post_will_be_deleted_title'       => 'Ihre Anzeige wird in gelöscht :days Tagen',
	'post_will_be_deleted_content_1'   => 'Hallo,',
	'post_will_be_deleted_content_2'   => 'Ihre Anzeige ":title" wird in gelöscht :days Tagen von :appName.',
	'post_will_be_deleted_content_3'   => 'Sie können es erneut veröffentlichen, indem Sie hier klicken : <a href=":repostUrl">:repostUrl</a>',
	'post_will_be_deleted_content_4'   => 'Wenn Sie nichts tun, wird Ihre Anzeige dauerhaft gelöscht :dateDel.',
	'post_will_be_deleted_content_5'   => 'Vielen Dank für Ihr Vertrauen und bis bald.',
	'post_will_be_deleted_content_6'   => 'Dies ist eine automatisierte E-Mail, bitte antworten Sie nicht.',
	
	
	// post_notification
	'post_notification_title'          => 'Neue Anzeige wurde veröffentlicht',
	'post_notification_content_1'      => 'Hallo Admin,',
	'post_notification_content_2'      => 'Der Nutzer :advertiserName hat gerade eine neue Anzeige geschaltet.',
	'post_notification_content_3'      => 'Die Anzeige title: <a href=":postUrl">:title</a><br>Veröffentlicht am: :now um :time',
	
	
	// user_notification
	'user_notification_title'        => 'Neue Benutzerregistrierung',
	'user_notification_content_1'    => 'Hallo Admin,',
	'user_notification_content_2'    => ':name hat sich gerade registriert.',
	'user_notification_content_3'    => 'Registriert am: :now um :time<br>Email: <a href="mailto::email">:email</a>',
	
	
	// payment_sent
	'payment_sent_title'             => 'Vielen Dank für Ihre Zahlung!',
	'payment_sent_content_1'         => 'Hallo,',
	'payment_sent_content_2'         => 'Wir haben Ihre Zahlung für die Anzeige erhalten "<a href=":postUrl">:title</a>".',
	'payment_sent_content_3'         => 'Danke dir!',
	
	
	// payment_notification
	'payment_notification_title'     => 'Neue Zahlung wurde gesendet',
	'payment_notification_content_1' => 'Hallo Admin,',
	'payment_notification_content_2' => 'Der Nutzer :advertiserName hat gerade ein Paket für ihre Anzeige bezahlt "<a href=":postUrl">:title</a>".',
	'payment_notification_content_3' => 'DIE ZAHLUNGSDETAILS
<br><strong>Grund der Zahlung:</strong> Ad #:adId - :packageName
<br><strong>Amount:</strong> :amount :currency
<br><strong>Zahlungsmethode:</strong> :paymentMethodName',
	
	// payment_approved (new)
	'payment_approved_title'     => 'Ihre Zahlung wurde genehmigt!',
	'payment_approved_content_1' => 'Hallo,',
	'payment_approved_content_2' => 'Ihre Zahlung für die Anzeige "<a href=":postUrl">:title</a>" wurde genehmigt.',
	'payment_approved_content_3' => 'Vielen Dank!',
	'payment_approved_content_4' => 'DIE ZAHLUNGSDETAILS
<br><strong>Grund der Zahlung:</strong> Anzeige #:adId - :packageName
<br><strong>Amount:</strong> :amount :currency
<br><strong>Zahlungsmethode:</strong> :paymentMethodName',
	
	
	// reply_form
	'reply_form_title'               => ':subject',
	'reply_form_content_1'           => 'Hallo,',
	'reply_form_content_2'           => '<strong>Sie haben eine Antwort von erhalten: :senderName. Siehe die Nachricht unten:</strong>',
	
	
	// generated_password
	'generated_password_title'            => 'Ihr Passwort',
	'generated_password_content_1'        => 'Hallo :userName!',
	'generated_password_content_2'        => 'Ihr Konto wurde erstellt.',
	'generated_password_verify_content_3' => 'Klicken Sie auf die Schaltfläche unten, um Ihre E-Mail-Adresse zu bestätigen.',
	'generated_password_verify_action'    => 'Email Adresse bestätigen',
	'generated_password_content_4'        => 'Ihr Passwort lautet: <strong>:randomPassword</strong>',
	'generated_password_login_action'     => 'Jetzt einloggen!',
	'generated_password_content_6'        => 'Sie erhalten diese E-Mail, weil Sie kürzlich eine neue erstellt haben :appName Konto oder eine neue E-Mail-Adresse hinzugefügt. Wenn Sie es nicht waren, ignorieren Sie bitte diese E-Mail.',


];
