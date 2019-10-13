<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 05/08/2018
 * Time: 12.11
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InputMedia\InputMediaPhoto;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use WinTenDev\Utils\Time;

class KucingCommand extends UserCommand
{
	protected $name = 'kucing';
	protected $description = 'Get random cat picture based on random.cat';
	protected $usage = '<kucing>';
	protected $version = '1.0.0';
	
	/**
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	public function execute()
	{
		$message = $this->getMessage();
		$chat_id = $message->getChat()->getId();
		$mssg_id = $message->getMessageId();
		
		$time = $message->getDate();
		$time = Time::jeda($time);
		
		$bacot = explode(' ', $message->getText(true));
		if ($bacot[0] > 1) {
			$data = [
				'chat_id' => $chat_id,
			];
			
			$dataPrep = [
				'chat_id' => $chat_id,
				'text'    => 'Initializing..',
			];
			if ($bacot[0] <= 5) {
				$mssg = Request::sendMessage($dataPrep);
				$dataPrep['message_id'] = $mssg->result->message_id;
				$media = [];
				for ($i = 1; $i <= $bacot[0]; $i++) {
					$json = file_get_contents('http://aws.random.cat/meow');
					$json = json_decode($json, true);
					$file = $json['file'];
					$index = 'photo_' . $i;
					$data[$index] = Request::encodeFile($file);
					$media[] = new InputMediaPhoto(['media' => 'attach://' . $index]);
					$dataPrep['text'] = "Processing $i of " . $bacot[0] . ' kucings';
					Request::editMessageText($dataPrep);
				}
				$data['caption'] = 'Kucing {bacot[0}!';
				$data['media'] = $media;
				
				$dataPrep['text'] = 'Completing process..';
				Request::editMessageText($dataPrep);
				$r = Request::sendMediaGroup($data);
			} else {
				$dataPrep['text'] = "for performance reason, I'm limit to 5 kucings";
				$r = Request::sendMessage($dataPrep);
			}
		} else {
			$json = file_get_contents('http://aws.random.cat/meow');
			$json = json_decode($json, true);
			$file = $json['file'];
			$text = 'Kucing gan!';
			
			$r = Request::sendPhoto([
				'chat_id'             => $chat_id,
				'photo'               => $file,
				'caption'             => $text . $time,
				'reply_to_message_id' => $mssg_id,
				'parse_mode'          => 'HTML',
			]);
		}
		
		return $r;
	}
}
