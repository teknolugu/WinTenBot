<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 07/09/2018
 * Time: 19.02
 */

namespace Longman\TelegramBot\Commands\UserCommands;
;

use src\Utils\Time;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use src\Model\Notes;
use src\Model\Settings;

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
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $mssg_id = $message->getMessageId();

        $time = $message->getDate();
	    $time = Time::jeda($time);
	
	    $pecah = explode(' ', $message->getText(true));

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

//        $cmds = 'test';
//        $data = Crud::baca('spell');
//        $cmds = json_encode($data, true);
//        error_log("Method name must be a string\n");
//        $d = new Crud();
//        $cmds = $d->masuk('spell',[
//           'typo' => 'a',
//           'fix' => 'sd'
//        ]);
//        $data = $d->baca('spell');

//        $db = new Medoo(db_data);
//        $data = db_data['server'];

//        try {
//            $db = new PDO('mysql:host=' .db_data['server'].
//                ';dbname='.db_data['database_name'],
//                db_data['username'], db_data['password']);
//            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//
//            $sql = "INSERT INTO spell (typo, fix) VALUES('mavar','mabar')";
//            $db->exec($sql);

//                        $db = new Medoo(db_data);
//
//            $data = 'succes';

//
//                    $data = $db->select('spell','*');
//            $data =  $db->debug();
//
//            $data = $db->insert('spell', [
//                'typo' => 'a',
//                'fix' => 'sd'
//            ]);

//            $data = Crud::tambah('spell',[
//                'typo' => 'a',
//                'fix' => 'sd'
//            ]);

//
//            $data = json_encode($data, true);

//            file_put_contents(log_file, $db->info()."Wik\n", FILE_APPEND);
//        } catch (PDOException $ex) {
//            file_put_contents(log_file, $ex->getMessage()."\n", FILE_APPEND);
//        }

//        $builder = new MySqlBuilder();
//        $query = $builder->insert()
//            ->setTable('spell')
//            ->setValues([
//                'typo' => 'a',
//                'fix' => 'sd'
//            ]);
//
//        $data = $builder->write($query);
//            ."\n"
//        . $builder->getValues();

//        file_put_contents(log_file, "Wik\n", FILE_APPEND);
//        $data = 'test'.Notes::getNotes($chat_id);
	
	    $text = Settings::save([
		    'chat_id'  => $chat_id,
		    'property' => $pecah[0],
		    'value'    => $pecah[1],
	    ]);
	    
        $data = [
	        'chat_id'                  => $chat_id,
	        'text'                     => $text . $time,
	        'reply_to_message_id'      => $mssg_id,
	        'disable_web_page_preview' => true,
	        'parse_mode'               => 'HTML',
        ];

        return Request::sendMessage($data);
    }
}
