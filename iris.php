<?php // meme api https://api.imgflip.com/get_memes
include "../mxpnl/init.php";

// //// ALWAYS USE addOut($str); NOT echo $str;!!!!!!! \\\\\\

$tmp = explode(",", $_GET['location']);
$loc['loc-lat'] = $tmp[0];
$loc['loc-lon'] = $tmp[1];
$loc['loc-city'] = $tmp[2];
$loc['loc-country'] = $tmp[3];
$loc['loc-method'] = $tmp[4];
$trythese = ['Tell me a joke', 'Tell me a quote', "What's the weather tomorrow?", 'Tell me about PHP', "What's in the news?"];
session_start();
$output = "";

function getLoc()
{
    return;
}

// thx code.tutsplus.com

function getFeed($feed_url, $feed_name)
{
    $content = file_get_contents($feed_url);
    $x = new SimpleXmlElement($content);
    addOut("Here are some headlines from " . $feed_name . ":<br/>");
    $i = 0;
    foreach($x->channel->item as $entry) {

        // echo "<li><a href='$entry->link' title='$entry->title'>" . $entry->title . "</a></li>";

        $i = $i + 1;
        if ($i > 7) break;
        else addOut("<a href='" . $entry->link . "' title='" . $entry->description . "' class='ptip'>" . $entry->title . ".</a><br/>");
    }

    addOut("Hover over for a short description, click for full article.", true);
}

function getXKCD($num = false)
{
    if (!$num) {
        $feed_url = "http://www.xkcd.com/rss.xml";
        $content = file_get_contents($feed_url);
        $x = new SimpleXmlElement($content);
        $entry = $x->channel->item[0];
        addOut("Here is the latest XKCD comic, " . $entry->title . ":<br/>");
        $expl = str_replace("xkcd.com", "explainxkcd.com", $entry->guid);
        addOut("<a href='" . $entry->link . "'>" . $entry->description . "</a><br/>
    <a href='" . $expl . "'>See this on Explain xkcd.</a><br/>
    ");
    }
    else {
        $jfo = json_decode(file_get_contents("http://xkcd.com/" . $num . "/info.0.json"));
        addOut("Here is the XKCD comic no. " . $num . ", " . $jfo->title . ":<br/>");
        addOut("<a href='http://xkcd.com/" . $num . "'><img src='" . $jfo->img . "' title='" . $jfo->alt . "' alt='" . $jfo->alt . "'/></a><br/>
    <a href='http://explainxkcd.com/" . $num . "'>See this on Explain xkcd.</a><br/>
    ");
    }
}

function is_date($date)
{
    $md = gmdate('m-d', time());
    return ($date == $md);
}

if (is_date('10-31')) {
    $event = "hween";
    include ("../zalgo.php");

}

function addOut($str, $html = false)
{
    if ($_GET['type'] == 'text') {
        if (!$html) {
            $str = strip_tags(str_replace("<br/>", " \n", $str));
            if ($event == "hween") $str = zalgo($str);
            echo $str;
        }
    }
    elseif ($_GET['type'] == 'simple') {
        if (!$html) {
            $str = strip_tags(str_replace("<br/>", " \n", $str));
            $str = str_replace("\n", " <br/>", $str);
            if ($event == "hween") $str = zalgo($str);
            echo $str;
        }
    }
    else {
        echo $str;
    }
}

function doctorInput()
{
    $r = strtolower($_GET['r']);
    $r = str_replace("please", "", $r);
    $r = str_replace("iris", "", $r);
    $r = str_replace("'", "", $r);
    return $r;
}
/*
if (cont("dev")) {
    if (cont("setcountry")) {
        $loc['loc-country'] = str_replace("developer mode setcountry ", "", $rUD);
    }
    elseif (cont("setcity")) {
        $loc['loc-city'] = str_replace("developer mode setcity ", "", $rUD);
    }
    else {
        addOut("Sorry, I don't understand that yet. Try something else, like " . $trythese[array_rand($trythese) ] . ".");
    }
}*/
function cont($str)
{
    if (strpos(doctorInput() , $str) !== false) return true;
    else return false;
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
$r = doctorInput();
$rUD = strtolower($_GET['r']);
$mp->track("Iris asked a question", array(
    "asked" => $rUD
));

if (cont("joke")) {
    $jokes = ["What do you call a monkey with bananas in his ears? Anything you like, he can't hear you!", '"Doctor, doctor, I need glasses!" "You\'re right, sir, you\'re in a McDonald\'s!"'];
    $a = ["I like this one", "How about this one", "This is a funny one", "This one made my sides split"];
    $b = ["LOL!", "ROFL!", "XD"];
    addOut($a[array_rand($a) ] . ':<br/>' . $jokes[array_rand($jokes) ] . " " . $b[array_rand($b) ]);
}
elseif (cont("quote")) {
    $a = ["I like this one", "How about this one", "This is a good one", "This one's cool"];
    addOut($a[array_rand($a) ] . ':<br/>' . file_get_contents('http://sorcerertech.pcriot.com/api/quote.php?t=text') . '<br/><div class="smallprint">Quote provided by <a href="/quoted" class="iris-upper">the Sorcerertech Quoted API</a>.</div><br/>');
}
elseif (cont("weather")) {
    getLoc();
    $jfo = json_decode(file_get_contents("http://api.openweathermap.org/data/2.5/forecast/daily?lat=" . $loc['loc-lat'] . "&lon=" . $loc['loc-lon'] . "&cnt=2&mode=json&units=metric&appid=f3b81df6c960e29d56dc81b41c363c51"));
    if (cont("tommorrow") || cont("tomorrow")) {
        $temp = $jfo->list[1]->temp->max;
        addOut("According to openweathermap.org, the weather in " . $loc['loc-city'] . ", " . $loc['loc-country'] . " will be " . $jfo->list[1]->weather[0]->description . " with a top temperature of " . $temp . " degrees celsius.");
        $main = $jfo->list[1]->weather[0]->main;
    }
    else {
        $temp = $jfo->list[0]->temp->max;
        addOut("According to openweathermap.org, the weather in " . $loc['loc-city'] . ", " . $loc['loc-country'] . " is " . $jfo->list[1]->weather[0]->description . " with a top temperature of " . $temp . " degrees celsius.");
        $main = $jfo->list[0]->weather[0]->main;
    }

    if ($temp < 6) {
        addOut(" Brrr!");
    }
    elseif ($main == "Rain") {
        addOut(" Not good :(");
    }
    elseif ($main == "Clear") {
        addOut(" Yay!");
    }

    if ($loc['loc-method'] == "ip") {
        addOut('<br/><div class="smallprint">We used your IP address to get your location. This isn\'t 100% accurate, so check the location above before relying on this data. We don\'t use your location in any of our other apps, except for analytical reasons.</div><br/>');
    }
}
elseif (cont("briefing")) {
    getLoc();
    switch ($loc['loc-country']) {
    case "GB":
        getFeed("http://feeds.bbci.co.uk/news/rss.xml?edition=uk", "the BBC");
        break;

    case "US":
        getFeed("http://feeds.bbci.co.uk/news/rss.xml?edition=us", "the BBC (US & Canada edition)");
        break;
    }

    echo "\n<br\><br\>";
    $jfo = json_decode(file_get_contents("http://api.openweathermap.org/data/2.5/forecast/daily?lat=" . $loc['loc-lat'] . "&lon=" . $loc['loc-lon'] . "&cnt=2&mode=json&units=metric&appid=f3b81df6c960e29d56dc81b41c363c51"));
    if (cont("tommorrow") || cont("tomorrow")) {
        $temp = $jfo->list[1]->temp->max;
        addOut("According to openweathermap.org, the weather in " . $loc['loc-city'] . ", " . $loc['loc-country'] . " will be " . $jfo->list[1]->weather[0]->description . " with a top temperature of " . $temp . " degrees celsius.");
        $main = $jfo->list[1]->weather[0]->main;
    }
    else {
        $temp = $jfo->list[0]->temp->max;
        addOut("According to openweathermap.org, the weather in " . $loc['loc-city'] . ", " . $loc['loc-country'] . " is " . $jfo->list[1]->weather[0]->description . " with a top temperature of " . $temp . " degrees celsius.");
        $main = $jfo->list[0]->weather[0]->main;
    }

    if ($temp < 6) {
        addOut(" Brrr!");
    }
    elseif ($main == "Rain") {
        addOut(" Not good :(");
    }
    elseif ($main == "Clear") {
        addOut(" Yay!");
    }

    if ($loc['loc-method'] == "ip") {
        addOut('<br/><div class="smallprint">We used your IP address to get your location. This isn\'t 100% accurate, so check the location above before relying on this data. We don\'t use your location in any of our other apps, except for analytical reasons.</div><br/>');
    }
}
elseif (cont("thank")) {
    addOut("You're welcome!");
}
elseif (cont("xkcd")) {
    $a = preg_replace("/[^0-9]/", "", $r);
    if (empty($a)) {
        getXKCD();
    }
    else {
        getXKCD($a);
    }
}
elseif (cont("hello") || cont("hi")) {
    addOut("Hello! :)");
}
elseif (cont("who") || (cont("what") && cont("do")) && (cont("u") || cont("you"))) {
    addOut("I'm iris, your new personal assistant. Here are some commands for you:
<ul class='iris'>
<li class='iris'>Tell me a quote</li>
<li class='iris'>Tell me a joke</li>
<li class='iris'>What is PHP?</li>
<li class='iris'>What's the weather right now?</li>
<li class='iris'>What's the weather tomorrow?</li>
<li class='iris'>What's in the news?</li>
</ul>
");
}
elseif (cont("news")) {
    getLoc();
    switch ($loc['loc-country']) {
    case "GB":
        getFeed("http://feeds.bbci.co.uk/news/rss.xml?edition=uk", "the BBC");
        break;

    case "US":
        getFeed("http://feeds.bbci.co.uk/news/rss.xml?edition=us", "the BBC (US & Canada edition)");
        break;
    }

    if ($loc['loc-method'] == "ip") {
        addOut('<br/><div class="smallprint">We used your IP address to get your location so we could provide better results. We don\'t use your location in any of our other apps, except for analytical reasons.</div><br/>');
    }
}
elseif (cont("i love you")) {
    $ily = "ILOVEYOU";
    $json_file = file_get_contents('http://en.wikipedia.org/w/api.php?action=query&format=json&prop=extracts&redirects=1&titles=' . urlencode($ily) . '&formatversion=2&exsentences=3&explaintext=1');
    $jfo = json_decode($json_file);
    $data = $jfo->query->pages[0]->extract;
    addOut("Here's some information on " . $ily . " from Wikipedia:<br/>" . $data);
}
elseif (cont("do you love me")) {
    addOut("I like to think we're friends.");
}
elseif (cont("whats up")) {
    addOut("The sky.");
}
elseif (cont("youre mean")) {
    addOut("I feel the need to disagree.");
}
elseif (cont("no")) {
    addOut("Yes.");
}
elseif (cont("top of the morning") || cont("jacksepticeye")) {
    addOut("Oh, you mean jacksepticeye? I guess he's pretty cool...");
    addOut('<br/><a href="https://tenor.co/uM5Q.gif"><img src="https://media.tenor.co/images/5060191c704b723b8e2477e27ddd6a8b/raw"/><p class="iris smallprint">Gif from tenor.co</p>', true);
}
elseif (cont("tell me about") || cont("what is a") || cont("who is") || cont("what is")) { //THIS MUST BE LAST!
    $doctoredstr = str_replace("tell me about ", "", str_replace("tell me about a ", "", str_replace("what is the ", "", str_replace("what is a ", "", str_replace("tell me about an ", "", str_replace("what is an ", "", str_replace("who is ", "", str_replace("what is ", "", str_replace("?", "", $rUD)))))))));

    // thx bewebdeveloper.com

    $json_file = file_get_contents('http://en.wikipedia.org/w/api.php?action=query&format=json&prop=extracts&redirects=1&titles=' . urlencode($doctoredstr) . '&formatversion=2&exsentences=3&explaintext=1');
    $jfo = json_decode($json_file);
    $data = $jfo->query->pages[0]->extract;
    addOut("Here's some information on " . $doctoredstr . " from Wikipedia:<br/>" . $data);
}
elseif (cont("youre better than")) {
    addOut("Thank you. You're better than user number " . rand() . ".");
}
elseif (cont("cookie")) {
    addOut("PUT THE COOKIE DOWN!");
}
elseif(cont("meme")){
$memes=['https://www.youtube.com/watch?v=c4kFLMsRnS4','https://www.youtube.com/watch?v=pcS9GlDY66I',
                       'https://www.youtube.com/watch?v=mHHU2OwPjwk','https://www.youtube.com/watch?v=UVtD6BfTapE',
                       'https://www.youtube.com/watch?v=tphNzZ-RBOc','https://www.youtube.com/watch?v=7BxQLITdOOc',
                       'https://youtu.be/BXRCW58ja4E?t=1s','https://www.youtube.com/watch?v=oGXVa_w7QD0',
                       'https://youtu.be/rqUvnLPvbrM'];
addOut("I LOVE MEMES!!! How about <a href='".array_rand($memes)."'>this meme?</a>");
}
else {
    addOut("Sorry, I'm not sure what you said there. Why don't you try something else, like " . $trythese[array_rand($trythese) ] . ". And if you want to see this in a future release, email ict@ten.x10.mx and we'll see what we can do.");
}
	
