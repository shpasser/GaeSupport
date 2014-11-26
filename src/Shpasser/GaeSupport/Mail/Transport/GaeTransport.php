<?php namespace Shpasser\GaeSupport\Mail\Transport;

use Swift_Transport;
use Swift_Mime_Message;
use Swift_Events_EventListener;

use Shpasser\GaeSupport\Foundation\Application;

require_once 'google/appengine/api/mail/Message.php';
use google\appengine\api\mail\Message as GAEMessage;

class GaeTransport implements Swift_Transport {

	/**
	 *  Application instance.
	 *
	 * @var \Shpasser\GaeSupport\Foundation\Application
	 */
	protected $app;

	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isStarted()
	{
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function start()
	{
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function stop()
	{
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function send(Swift_Mime_Message $message, &$failedRecipients = null)
	{
        try {
            $to = implode(', ', array_keys((array) $message->getTo()));
			$cc = implode(', ', array_keys((array) $message->getCc()));
			$bcc = implode(', ', array_keys((array) $message->getBcc()));
			$replyto = '';

			foreach ($message->getReplyTo() as $address => $name)
			{
				$replyto = $address;
				break;
			}

            $mail_options = [
                "sender"	=> "admin@" . $this->app->getGaeAppId() . ".appspotmail.com",
                "to"		=> $to,
                "subject"	=> $message->getSubject(),
                "htmlBody"	=> $message->getBody()
            ];

			if ($cc  !== '') 		$mail_options['cc'] = $cc;
			if ($bcc !== '') 		$mail_options['bcc'] = $bcc;
			if ($replyto !== '')	$mail_options['replyto'] = $replyto;

            $gae_message = new GAEMessage($mail_options);
			$gae_message->send();
        } catch (InvalidArgumentException $e) {
            syslog(LOG_WARNING, "Exception sending mail: " . $e);
        }
	}

	/**
	 * {@inheritdoc}
	 */
	public function registerPlugin(Swift_Events_EventListener $plugin)
	{
		//
	}

}
