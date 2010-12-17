<?php

/**
 * YumMailer just implements the send() method that handles (guess what)
 * the mailing process.
 * @return true if sends mail, false otherwise
 */
class YumMailer
{
	static public function send($mail_array=null)
	{
		if ($mail_array && $mail_array != array())
		{
			if(Yum::module()->mailer == 'swift')
			{
				$sm = Yii::app()->swiftMailer;
				$mailer = $sm->mailer($sm->mailTransport());
				$message = $sm->newMessage($mail_array['subject'])
					->setFrom($mail_array['from'])
					->setTo($mail_array['to'])
					->setBody($mail_array['body']);
				return $mailer->send($message);
			}
			elseif(Yum::module()->mailer == 'PHPMailer')
			{
				Yii::import('application.extensions.phpmailer.JPhpMailer');
				$mailer = new JPhpMailer(true);
				if (Yum::module()->phpmailer['transport'])
					switch (Yum::module()->phpmailer['transport'])
					{
						case 'smtp':
							$mailer->IsSMTP();
							break;
						case 'sendmail':
							$mailer->IsSendmail();
							break;
						case 'qmail':
							$mailer->IsQmail();
							break;
						case 'mail':
						default:
							$mailer->IsMail();
					}
				else
					$mailer->IsMail();

				if (Yum::module()->phpmailer['html'])
					$mailer->IsHTML(Yum::module()->phpmailer['html']);
				else
					$mailer->IsHTML(false);

				$mailerconf=Yum::module()->phpmailer['properties'];
				if(is_array($mailerconf))
					foreach($mailerconf as $key=>$value)
					{
						if(isset(JPhpMailer::${$key}))
							JPhpMailer::${$key} = $value;
						else
							$mailer->$key=$value;
					}
				$mailer->SetFrom($mail_array['from'], Yum::module()->phpmailer['msgOptions']['fromName']); //FIXME
				$mailer->AddAddress($mail_array['to'], Yum::module()->phpmailer['msgOptions']['toName']); //FIXME
				$mailer->Subject = $mail_array['subject'];
				$mailer->Body = $mail_array['body'];
				return $mailer->Send();
			}
			else
			{
				$headers = "From: " . Yum::module()->registrationEmail ."\r\nReply-To: ".Yii::app()->params['adminEmail'];
				return mail($mail_array['to'], $mail_array['subject'], $mail_array['body'], $headers);
			}
		}
		else
			return false;
	}
}
