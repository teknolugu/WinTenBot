<?php
/**
 * Created by PhpStorm.
 * User: azhe403
 * Date: 28/08/18
 * Time: 21:07
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use src\Handlers\ChatHandler;
use src\Model\Group;
use src\Model\UrlLists;
use src\Utils\Format;

class UrlCommand extends UserCommand
{
	protected $name = 'url';
	protected $description = 'Add word to blacklist or whitelist';
	protected $usage = '<kata>';
	protected $version = '1.0.0';
	
	public function execute()
	{
		$message = $this->getMessage();
		$chatHandler = new ChatHandler($message);
		$chat_id = $message->getChat()->getId();
		$from_id = $message->getFrom()->getId();
		
		$pecah = explode(' ', $message->getText());
		$r = $chatHandler->sendText("🔄 Checking permission..", '-1');
		if (Group::isSudoer($from_id)) {
			$chatHandler->deleteMessage();
			$r = $chatHandler->editText("🔄 Executing..", '-1');
			$validExec = ['blok', 'biar'];
			if (in_array($pecah[1], $validExec)) {
				$katas = [
					'url'     => $pecah[2],
					'class'   => strtolower($pecah[1]),
					'user_id' => $message->getFrom()->getId(),
					'chat_id' => $chat_id,
				];
				$r = $chatHandler->editText("🔄 Saving to database..", '-1');
				$blok = UrlLists::addUrl($katas);
				if ($blok->rowCount() > 0) {
					$chatHandler->editText("✍ Writing to cache..");
					$writeUrl = UrlLists::writeCache();
//                    $lists = UrlLists::getAll();
//                    $json = json_encode($lists);
//                    file_put_contents(botData . 'url-lists.json', $json);
					$text = "✅ <b>Url</b> berhasil di tambahkan. Ukuran Cache" . Format::formatSize($writeUrl);
				} else {
					$text = "⚠ <b>Url</b> sudah ada atau tidak dapat di perbarui";
				}
			} elseif ($pecah[1] == 'del') {
				$del = UrlLists::deleteUrl(['url' => $pecah[2]]);
				if ($del->rowCount() > 0) {
					$text = "✅ <b>Url</b> berhasil di hapus";
				} else {
					$text = "⚠ <b>Url</b> tidak dapat di hapus atau tidak ada";
				}
			} elseif ($pecah[1] == 'all') {
				$chatHandler->editText("👓 Loading data..");
				$lists = UrlLists::getAll();
//				$json = json_encode($lists);
				$chatHandler->editText("✍ Writing to cache..");
				$writeUrl = UrlLists::writeCache();
//				file_put_contents(botData . 'url-lists.json', $json);
				$list = "";
				ksort($lists);
				$countList = count($lists);
				if ($countList > 0) {
					foreach ($lists as $lis) {
						$list .= $lis['url'] . ' -> ' . $lis['class'] . "\n";
					}
				} else {
					$list = "No <b>Url</b> blocked globally";
				}
				$text = "📜 <b>Url-Lists</b>: <code>$countList</code>" .
					"\nCache: " . Format::formatSize($writeUrl) . "\n" .
					"===============================\n" .
					trim($list);
			} else {
				$text = 'ℹ <b>/url - Delete message if contain undesirable url</b>' .
					"\n<b>Usage: </b><code>/url [command] your_url</code>" .
					"\n<b>Command: </b><code>blok, del</code>";
			}
			$chatHandler->editText($text);
		} else {
			$r = $chatHandler->editText("⚠ <b>You isn't sudoer and can't use this feature.</b>");
		}
		return $r;
	}
	
}
