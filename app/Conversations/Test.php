<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use Illuminate\Support\Facades\Storage;
use Log;
use DB;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

class Test extends Conversation
{
    /**
     * Practice class
     * @category, @user 
     * @return bool
     */
    function __construct($category, $user, $bot, $practice) {
        $this->category = $category; //Teszt kategória
        $this->userID = $user->getId(); //UserID
        $this->bot = $bot; //BOT Instance
        $this->practice = $practice; //Gyakorlás/vizsga?

        $userInfo = $user->getInfo(); //User neve, meg ilyenek

        if(DB::table('bot_users')->where('BotUserID', $userInfo['id'])->first() === NULL){ //Ha még nem használta a botot, 'regisztráljuk'
            DB::insert('insert into bot_users (BotUserID, firstName, lastName, profilePic) values (?, ?, ?, ?)', [$userInfo['id'], $userInfo['first_name'], $userInfo['last_name'], $userInfo['profile_pic']]);
        }else{
            DB::table('bot_users')->where('BotUserID', $userInfo['id'])->update([ //Ha már regisztrálva van, frissítjük az adatait
                    'firstName' => $userInfo['first_name'],
                    'lastName' => $userInfo['last_name'],
                    'profilePic' => $userInfo['profile_pic']
                ]
            );
        }
    }

    /**
     * Kérdés betöltése
     *
     * @return bool
     */
    function loadQuestion() {
        switch($this->category){ //Kategória szerint beállítjuk a kérdések forrását
            case "a":
                $json = json_decode(Storage::disk('local')->get('questions/a.json'), true);
                break;
            
            case "b":
                $json = json_decode(Storage::disk('local')->get('questions/b.json'), true);
            break;
                
            case "c":
                $json = json_decode(Storage::disk('local')->get('questions/c.json'), true);
                break;
        
            case "d":
                $json = json_decode(Storage::disk('local')->get('questions/d.json'), true);
                break;
            
            case "e":
                $json = json_decode(Storage::disk('local')->get('questions/e.json'), true);
                break;
            
            default:
                $json = json_decode(Storage::disk('local')->get('questions/b.json'), true);
                break;  
        }

        $this->questionIndex = array_rand($json['questions'], 1); //Lekérünk egy random kérdést
        $this->group = $this->getQuestionGroup($json['groups'], $this->questionIndex);  // Lekérjük a kérdéshez tartozó kategóriát
        $this->correctIndex = $json['questions'][$this->questionIndex]['correct']; //Eltároljuk a helyes válasz ID-t
        $this->correctAnswer = $json['questions'][$this->questionIndex]['choices'][$this->correctIndex]; //Eltároljuk a helyes választ
        $this->question = $json['questions'][$this->questionIndex]["question"] ; //Kiemeljük a kérdést
        $this->choices = $json['questions'][$this->questionIndex]['choices']; //Kiemeljük a válaszokat (és megkeverjük őket)

        if($json['questions'][$this->questionIndex]["assets"] != null){ /* Megnézzük van-e hozzá tartozó kép, ha van akkor betöltjük és azt is tároljuk */
            //$this->asset = \URL::to('/') . '/data/asset/' . $json['questions'][$this->questionIndex]["assets"][0];
            $this->asset = 'https://b37d11e6.ngrok.io' . '/data/asset/' . $json['questions'][$this->questionIndex]["assets"][0];
        }

        if(config('app.debug') == true){
            Log::channel('customlog')->info('------ QUESTION ------');
            Log::channel('customlog')->info('QINDEX: ' . $this->questionIndex);
            Log::channel('customlog')->info('Group: ' . print_r($this->group, true));
            Log::channel('customlog')->info('Asset: ' . (isset($this->asset) ? $this->asset : NULL));
            Log::channel('customlog')->info('CorrINDEX: ' . $this->correctIndex);
            Log::channel('customlog')->info('Question: ' . $this->question);
            Log::channel('customlog')->info('Choices: ' . print_r($this->choices, true));    
        }

        /* Gombok a válaszokhoz */
        $this->buttons = [];
        $valIndex = 0;
        foreach($this->choices as $index => &$choice) {
            $valIndex += 1;
            array_push($this->buttons, Button::create('⚪ ('. $valIndex .')')->value($index));
        }
        
        return true; //Sikeresen betöltött kérdés esetén visszatérünk
    }

    /**
     * Kérdéshez tartozó kategória keresése a JSON-ban
     *
     * @return $group
     */
    protected function getQuestionGroup($groups, $questionIndex){
        foreach($groups as $group){
            foreach($group['questions'] as &$groupQuestion){
                if($groupQuestion == $questionIndex){
                    unset($group['questions']); //Kivesszük a 'questions'-t mert k*va sokat foglal és nem kell

                    return $group; //Visszatérünk a talált kategóriával
                }
            }
        }
        return "ERROR"; //Ennek elvileg soha nem szabadna teljesülnie
    }

    /**
     * Kérdés küldése a felhasználónak
     *
     * @return mixed
     */
    public function newQuestion()
    {
        if(!$this->loadQuestion()){
            die("A kérdés betöltése közben hiba történt");
        }

        $choicesFormat = implode(PHP_EOL, array_map(
            function ($v, $k) { return sprintf("(%s) '%s'", $k+1, $v); },
            $this->choices,
            array_keys($this->choices)
        ));

        $question = Question::create($this->question . PHP_EOL . '(' . $this->group['title'] . ' - ' . $this->group['score'] . 'pont)' . PHP_EOL . PHP_EOL . $choicesFormat)
            ->fallback('Nem sikerült a kérdés betöltése :(')
            ->callbackId('ask')
            ->addButtons($this->buttons);
            
        if(!$this->practice){ //Ha teszt, megnézzük van-e futó tesztje
            $user = $this->bot->getUser(); //User
            $userData = $this->bot->userStorage()->find($user->getId()); //Storage
            $activeTestID = $userData->get('activeTestID');

            if($activeTestID === NULL){ //Ha nincs aktív tesztje, létrehozunk egyet
                DB::insert('insert into tests (BotUserID, category, isFinished) values (?, ?, ?)', [
                        $this->userID,
                        $this->category,
                        false
                    ]
                );

                $testID = DB::getPdo()->lastInsertId();
                
                $this->bot->userStorage()->save([
                    'activeTestID' => $testID
                ]);            
            }
            else{ //Ha már van teszt, összesítjük a tesztID kérdéseinek pontszámát
                $points = DB::table('answers')->where('UserID', $user->getId())->where('TestID', $activeTestID)->sum('points');
                $correctPoints = DB::table('answers')->where('UserID', $user->getId())->where('TestID', $activeTestID)->where('correct', 1)->sum('points');

                if($points >= 75){ //Ha a pontszámok összege 75 vagy felette, lezárjuk a tesztet
                    DB::table('tests')->where('TestID', $activeTestID)->update([ //
                            'isFinished' => true
                        ]
                    );

                    //Értesítjük az usert, hogy nincs több kérdés
                    return $this->bot->reply(ButtonTemplate::create( 
                        'A vizsga véget ért! Az eredményeidet az alábbi gombon éred el:')
                            ->addButton(ElementButton::create('Eredmények')
                                ->type('postback')
                                ->payload('Kategória választás')
                            )
                        );
                }
            }
        }
        
        if(isset($this->asset)){
            $attachment = new Image($this->asset, [
                'custom_payload' => true,
            ]);

            $imageAttachment = OutgoingMessage::create('')->withAttachment($attachment);
            $this->bot->reply($imageAttachment); //Kép küldése
        }

        return $this->ask($question, function (Answer $answer) {
            $user = $this->bot->getUser(); //User
            $userData = $this->bot->userStorage()->find($user->getId()); //Storage

            Log::channel('customlog')->info('ANSWER: ' . $answer->getValue());
            DB::insert('insert into answers (UserID, TestID, category, points, correct) values (?, ?, ?, ?, ?)', [
                    $this->userID,
                    ($userData->get('activeTestID')) ? $userData->get('activeTestID') : NULL,
                    $this->category,
                    $this->group['score'],
                    ($answer->getValue() == $this->correctIndex) ? true : false
                ]
            );

            if($this->practice){ //Ha gyakorlás
                if ($answer->isInteractiveMessageReply()) {
                    if ($answer->getValue() == $this->correctIndex) {
                        $question = Question::create('A válaszod helyes! Jöhet a következő kérdés?')
    
                        ->callbackId('nextQuestion')
                        ->addButtons([
                                Button::create('Igen 😎')->value(true),
                                Button::create('Gyakorlás befejezése')->value(false)
                            ]
                        );
    
                        $this->ask($question, function (Answer $answer) {
                            Log::channel('customlog')->info('ANSWER: ' . $answer->getValue());
                
                            if ($answer->isInteractiveMessageReply()) {
                                if ($answer->getValue()) {
                                    $this->newQuestion($this->practice);
                                } else {
                                    return true;
                                }
                            }
                        });
                    } else {
                        $question = Question::create('A válaszod sajnos helytelen! A helyes válasz a(z): ' . $this->correctAnswer . PHP_EOL . 'Jöhet a következő kérdés?')
    
                        ->callbackId('nextQuestion')
                        ->addButtons([
                                Button::create('Igen 😎')->value(true),
                                Button::create('Gyakorlás befejezése 😢')->value(false)
                            ]
                        );
    
                        $this->ask($question, function (Answer $answer) {
                            Log::channel('customlog')->info('ANSWER: ' . $answer->getValue());
                
                            if ($answer->isInteractiveMessageReply()) {
                                if ($answer->getValue()) {
                                    $this->newQuestion($this->practice);
                                } else {
                                    return true;
                                }
                            }
                        });
                    }
                }
            }
            else{ //Ha "teszt" nem mondjuk meg, hogy helyes-e
                if ($answer->isInteractiveMessageReply()) {
                    $question = Question::create('A válaszodat rögzítettük! Jöhet a következő kérdés?')
                    ->callbackId('nextQuestion')
                    ->addButtons([
                            Button::create('Folytatás! 😎')->value(true),
                            Button::create('Teszt befejezése')->value('Teszt befejezése')
                        ]
                    );

                    $this->ask($question, function (Answer $answer) {
                        Log::channel('customlog')->info('ANSWER: ' . $answer->getValue());
            
                        if ($answer->isInteractiveMessageReply()) {
                            if ($answer->getValue() === true) {
                                $this->newQuestion($this->practice);
                            } else {
                                return false;
                            }
                        }
                    });
                }
            }

        });
    }

    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        $this->newQuestion();
    }
}
