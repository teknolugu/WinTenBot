<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 07/09/2018
 * Time: 19.02
 */

namespace Longman\TelegramBot\Commands\UserCommands;
;

use http\Message;
use Medoo\Medoo;
use src\Handlers\MessageHandlers;
use Longman\TelegramBot\Commands\UserCommand;
use src\Model\DB;
use src\Model\Settings;
use src\Model\Spell;

class TestCommand extends UserCommand
{
	protected $name = 'test';
	protected $description = 'Labs for feature update';
	protected $usage = '/test';
	protected $version = '1.0.0';
	
	/**
	 * Execute command
	 *
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 */
	public function execute()
	{
		$mssg = $this->getMessage();
		$mssgText = $mssg->getText(true);
		$pecah = explode(' ', $mssgText);

//        $entity_data = null;
//        $entities = $repMssg->getEntities();
//        $text = $repMssg->getText();

//        $text = Kata::processEntities($text, $entities);
//        $entities = $this->genHtml();
//        $entities = Bot::getTermsUse();

//        $entities = KuttAPI::tambahTag([
//            'target' => $pecah[0]
//        ]);
//
//        $entities = json_encode($entities, true);

//        $entities = $pecah[0];

//        $cmds = json_encode($this->telegram->getCommandConfig("ping"));
		
		$mHandler = new MessageHandlers($mssg);

//		$mHandler->sendText('lorem','');
		
		$mHandler->deleteMessage();
		$mHandler->sendText('Initializing..');
//		sleep(1);
//		$mHandler->editText('Collecting data..', null, BTN_EXAMPLE);
//		$mHandler->editText('Calculating data..');
//		sleep(1);
//		$mHandler->editText('Processing..', null, BTN_EXAMPLE);
//		$mHandler->editText('Expanding all..');
//		$mHandler->editText('Processing 99999..');

//		$s = Spell::spellTest($mssgText);
//		$s = Spell::listSpell();
//		foreach ($s as $e) {
//			$mssgText = str_replace($e['typo'], $e['fix'], $mssgText);
//			$mHandler->editText("Spelling...\n" . $mssgText);
//		}

//		DB::insert('test', ['data' => 'lorem']);
//		$d = DB::insertOrUpdate('test', ['name' => 'Fulan 2'], ['data' => 'lorem']);
//		$d = Settings::saveNew([
//			'last_welcome_message_id' => 'lorem',
//			'chat_id' => 'asd'
//		], [
//			'chat_id' => 'asd',
//		]);
		$d = Settings::getNew(['chat_id' => $mssg->getChat()->getId()]);
		$welcome = $d[0]['welcome_message'];
//		$mHandler->editText('Finalizing..', null, BTN_EXAMPLE);
//		$mHandler->editText('F*cking finalizing..');
//		sleep(0.5);

//		for ($i = 0; $i <= 3; $i++) {
//			$mHandler->editText('test ' . $i);
//		}
		
		return $mHandler->editText( $welcome. "\nCompleted.", null, BTN_OK_NO_CANCEL);
	}
}
