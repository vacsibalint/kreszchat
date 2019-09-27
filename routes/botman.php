<?php
use App\Conversations\Test;

use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\Drivers\Facebook\Extensions\ButtonTemplate;

use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

$botman = resolve('botman');

$botman->hears('START', function ($bot) {
    $user = $bot->getUser();

    $bot->reply(ButtonTemplate::create('Kedves '. $user->getLastName() . ' ' . $user->getFirstName() .'!'. PHP_EOL . 
    'Köszöntelek a kreszChat alkalmazásban!'. PHP_EOL . 
    'A Facebook messengeren keresztül segítek neked a kresz vizsgára felkészülni, vagy a már meglévő tudásodat felfrissíteni.'. PHP_EOL . 
    'Ha most vagy itt először, olvasd el a "Tudnivalók" menüpontot! 😉')
        ->addButton(ElementButton::create('Gyakorlás')
            ->type('postback')
            ->payload('Gyakorlás')
        )
        ->addButton(ElementButton::create('Teszt vizsga')
            ->type('postback')
            ->payload('Vizsga')
        )
        ->addButton(ElementButton::create('Tudnivalók')
            ->url('http://botman.io/')
        )
    );
});

$botman->hears('Vizsga', function ($bot) {
    $bot->userStorage()->save([
        'practice' => false
    ]);

    $bot->reply(
        'A vizsga megegyezik az éles vizsga menetével. Indítás után kb. ~55 kérdést (75 pont) kell megválaszolnod. ' . PHP_EOL . 
        'A szituációk és fontosabb kérdések 3, az egyéb kérdések 1 pontot érnek.' . PHP_EOL . 
        'Minden kérdés megválaszolására 60 mp-ed van. Amennyiben nem sikerül ez idő alatt válaszolni, automatikusan hibásnak jelöljük!');
    $bot->typesAndWaits(3);
    $bot->reply('A kérdések megválaszolása után kiértékelem a válaszaidat és összesítem a pontokat. A sikeres teszthez a max 75-ből legalább 65 pontra lesz szükséged.');
    $bot->typesAndWaits(2);
    $bot->reply(ButtonTemplate::create(
        'Sok szerencsét és sikeres vizsgát kívánok! 😇')
        ->addButton(ElementButton::create('Indulhat a teszt! 😎')
            ->type('postback')
            ->payload('Kategória választás')
        )
        ->addButton(ElementButton::create('Vissza a menübe 😖')
            ->type('postback')
            ->payload('Menü')
        )
    );
});

$botman->hears('Gyakorlás', function ($bot) {
    $bot->userStorage()->save([
        'practice' => true
    ]);

    $bot->reply('A gyakorlás menete a következő; Felteszek egy kérdést amit a válaszod után egyből ki is értékelek és megmondom, hogy helyes volt-e. Amennyiben helytelenül válaszolsz, azt is elárulom, mi lett volna a helyes.');
    $bot->typesAndWaits(5);
    $bot->reply(ButtonTemplate::create(
        'Ha valamiért nem egyértelmű, a "Részletek" gombra kattintva megpróbálom elmagyarázni miért az a helyes válasz. '. PHP_EOL . 
        'Gyakorlás közben nem számolom a pontjaidat és időkorlát sincs egy-egy kérdés megválaszolására.'. PHP_EOL . 
        'Jó tanulást kívánok! 😇')
        ->addButton(ElementButton::create('Jöhetnek a kérdések! 😎')
            ->type('postback')
            ->payload('Kategória választás')
        )
        ->addButton(ElementButton::create('Vissza a menübe 😖')
            ->type('postback')
            ->payload('Menü')
        )
    );
});

$botman->hears('Kategória választás', function ($bot) {
    $bot->reply(Question::create(
        'Ahhoz, hogy a megfelelő kérdéseket tudjam neked adni, válassz kategóriát amin belül gyakorolni/tesztelni szeretnél.'. PHP_EOL .
        'Később ezt természetesen meg tudod majd változtatni! 😊')
            ->addButtons([
                Button::create('"A" kategória')->value('"A" kategória'),
                Button::create('"B" kategória')->value('"B" kategória'),
                Button::create('"C" kategória')->value('"C" kategória'),
                Button::create('"D" kategória')->value('"D" kategória'),
                Button::create('"E" kategória')->value('"E" kategória')
            ])
        );
});

$botman->hears('"([ABCDE])" kategória', function ($bot, $category) {
    $user = $bot->getUser(); //User
    $userData = $bot->userStorage()->find($user->getId()); //Storage

    if($userData->get('practice')){
        $bot->reply('A(z) ' . $category . ' kategóriát választottad! Jó tanulást!');
    }else{
        $bot->reply($category . ' kategóriából fogunk összeállítani neked egy vizsgát. Sok szerencsét kívánunk!');
    }
    
    $bot->startConversation(new Test($category, $bot->getUser(), $bot, $userData->get('practice')));
});

$botman->hears('Gyakorló statisztika', function ($bot) {
    $user = $bot->getUser();

    $bot->reply(ButtonTemplate::create(
        'A gyakorlásaid alatt összegyűjtött pontok összesítését az alábbi gombra kattintva éred el')
        ->addButton(ElementButton::create('Mutasd! ')
            ->url('http://botman.io/')
        )
    );
});

$botman->hears('Teszt befejezése', function ($bot) {
    $bot->reply('Kérlek várj, az eredmények kiértékelése folyamatban van..');
    $bot->typesAndWaits(5);

    $user = $bot->getUser(); //User
    $userData = $bot->userStorage()->find($user->getId()); //Storage
    $activeTestID = $userData->get('activeTestID');
    Log::channel('customlog')->info('ACTQID: ' . $activeTestID);

    DB::table('tests')->where('TestID', $activeTestID)->update([ //
            'isFinished' => true
        ]
    );

    $bot->userStorage()->save([
        'activeTestID' => NULL
    ]);      
    
    $correctPoints = DB::table('answers')->where('UserID', $user->getId())->where('TestID', $activeTestID)->where('correct', 1)->sum('points');

    if($activeTestID != NULL){ //Ha van teszt amit értékelni lehet
        return $bot->reply(ButtonTemplate::create(
            'A ' . $activeTestID . ' számú vizsga véget ért! Pontszámod: ' . $correctPoints)
            ->addButton(ElementButton::create('Mutasd! ')
                ->url('http://kresz.bot/')
            )
        );
    }

    return $bot->reply('Jelenleg nincs aktív teszted, amit kiértékelhetnénk');
});