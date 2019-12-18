<?php
return array (
  'user_agent_parsers' =>
  array (
    0 =>
    array (
      'regex' => '(CFNetwork)(?:/(\\d+)\\.(\\d+)\\.?(\\d+)?)?',
      'family_replacement' => 'CFNetwork',
    ),
    1 =>
    array (
      'regex' => '(Pingdom.com_bot_version_)(\\d+)\\.(\\d+)',
      'family_replacement' => 'PingdomBot',
    ),
    2 =>
    array (
      'regex' => '(PingdomTMS)/(\\d+)\\.(\\d+)\\.(\\d+)',
      'family_replacement' => 'PingdomBot',
    ),
    3 =>
    array (
      'regex' => '(facebookexternalhit)/(\\d+)\\.(\\d+)',
      'family_replacement' => 'FacebookBot',
    ),
    4 =>
    array (
      'regex' => 'Google.*/\\+/web/snippet',
      'family_replacement' => 'GooglePlusBot',
    ),
    5 =>
    array (
      'regex' => 'via ggpht.com GoogleImageProxy',
      'family_replacement' => 'GmailImageProxy',
    ),
    6 =>
    array (
      'regex' => '(Twitterbot)/(\\d+)\\.(\\d+)',
      'family_replacement' => 'TwitterBot',
    ),
    7 =>
    array (
      'regex' => '/((?:Ant-)?Nutch|[A-z]+[Bb]ot|[A-z]+[Ss]pider|Axtaris|fetchurl|Isara|ShopSalad|Tailsweep)[ \\-](\\d+)(?:\\.(\\d+)(?:\\.(\\d+))?)?',
    ),
    8 =>
    array (
      'regex' => '\\b(008|Altresium|Argus|BaiduMobaider|BoardReader|DNSGroup|DataparkSearch|EDI|Goodzer|Grub|INGRID|Infohelfer|LinkedInBot|LOOQ|Nutch|PathDefender|Peew|PostPost|Steeler|Twitterbot|VSE|WebCrunch|WebZIP|Y!J-BR[A-Z]|YahooSeeker|envolk|sproose|wminer)/(\\d+)(?:\\.(\\d+)(?:\\.(\\d+))?)?',
    ),
    9 =>
    array (
      'regex' => '(MSIE) (\\d+)\\.(\\d+)([a-z]\\d?)?;.* MSIECrawler',
      'family_replacement' => 'MSIECrawler',
    ),
    10 =>
    array (
      'regex' => '(Google-HTTP-Java-Client|Apache-HttpClient|http%20client|Python-urllib|HttpMonitor|TLSProber|WinHTTP|JNLP|okhttp)(?:[ /](\\d+)(?:\\.(\\d+)(?:\\.(\\d+))?)?)?',
    ),
    11 =>
    array (
      'regex' => '(1470\\.net crawler|50\\.nu|8bo Crawler Bot|Aboundex|Accoona-[A-z]+-Agent|AdsBot-Google(?:-[a-z]+)?|altavista|AppEngine-Google|archive.*?\\.org_bot|archiver|Ask Jeeves|[Bb]ai[Dd]u[Ss]pider(?:-[A-Za-z]+)*|bingbot|BingPreview|blitzbot|BlogBridge|BoardReader(?: [A-Za-z]+)*|boitho.com-dc|BotSeer|\\b\\w*favicon\\w*\\b|\\bYeti(?:-[a-z]+)?|Catchpoint bot|[Cc]harlotte|Checklinks|clumboot|Comodo HTTP\\(S\\) Crawler|Comodo-Webinspector-Crawler|ConveraCrawler|CRAWL-E|CrawlConvera|Daumoa(?:-feedfetcher)?|Feed Seeker Bot|findlinks|Flamingo_SearchEngine|FollowSite Bot|furlbot|Genieo|gigabot|GomezAgent|gonzo1|(?:[a-zA-Z]+-)?Googlebot(?:-[a-zA-Z]+)?|Google SketchUp|grub-client|gsa-crawler|heritrix|HiddenMarket|holmes|HooWWWer|htdig|ia_archiver|ICC-Crawler|Icarus6j|ichiro(?:/mobile)?|IconSurf|IlTrovatore(?:-Setaccio)?|InfuzApp|Innovazion Crawler|InternetArchive|IP2[a-z]+Bot|jbot\\b|KaloogaBot|Kraken|Kurzor|larbin|LEIA|LesnikBot|Linguee Bot|LinkAider|LinkedInBot|Lite Bot|Llaut|lycos|Mail\\.RU_Bot|masidani_bot|Mediapartners-Google|Microsoft .*? Bot|mogimogi|mozDex|MJ12bot|msnbot(?:-media *)?|msrbot|netresearch|Netvibes|NewsGator[^/]*|^NING|Nutch[^/]*|Nymesis|ObjectsSearch|Orbiter|OOZBOT|PagePeeker|PagesInventory|PaxleFramework|Peeplo Screenshot Bot|PlantyNet_WebRobot|Pompos|Read%20Later|Reaper|RedCarpet|Retreiver|Riddler|Rival IQ|scooter|Scrapy|Scrubby|searchsight|seekbot|semanticdiscovery|Simpy|SimplePie|SEOstats|SimpleRSS|SiteCon|Slackbot-LinkExpanding|Slack-ImgProxy|Slurp|snappy|Speedy Spider|Squrl Java|TheUsefulbot|ThumbShotsBot|Thumbshots\\.ru|TwitterBot|URL2PNG|Vagabondo|VoilaBot|^vortex|Votay bot|^voyager|WASALive.Bot|Web-sniffer|WebThumb|WeSEE:[A-z]+|WhatWeb|WIRE|WordPress|Wotbox|www\\.almaden\\.ibm\\.com|Xenu(?:.s)? Link Sleuth|Xerka [A-z]+Bot|yacy(?:bot)?|Yahoo[a-z]*Seeker|Yahoo! Slurp|Yandex\\w+|YodaoBot(?:-[A-z]+)?|YottaaMonitor|Yowedo|^Zao|^Zao-Crawler|ZeBot_www\\.ze\\.bz|ZooShot|ZyBorg)(?:[ /]v?(\\d+)(?:\\.(\\d+)(?:\\.(\\d+))?)?)?',
    ),
    12 =>
    array (
      'regex' => '(?:\\/[A-Za-z0-9\\.]+)? *([A-Za-z0-9 \\-_\\!\\[\\]:]*(?:[Aa]rchiver|[Ii]ndexer|[Ss]craper|[Bb]ot|[Ss]pider|[Cc]rawl[a-z]*))/(\\d+)(?:\\.(\\d+)(?:\\.(\\d+))?)?',
    ),
    13 =>
    array (
      'regex' => '(?:\\/[A-Za-z0-9\\.]+)? *([A-Za-z0-9 _\\!\\[\\]:]*(?:[Aa]rchiver|[Ii]ndexer|[Ss]craper|[Bb]ot|[Ss]pider|[Cc]rawl[a-z]*)) (\\d+)(?:\\.(\\d+)(?:\\.(\\d+))?)?',
    ),
    14 =>
    array (
      'regex' => '((?:[A-z0-9]+|[A-z\\-]+ ?)?(?: the )?(?:[Ss][Pp][Ii][Dd][Ee][Rr]|[Ss]crape|[A-Za-z0-9-]*(?:[^C][^Uu])[Bb]ot|[Cc][Rr][Aa][Ww][Ll])[A-z0-9]*)(?:(?:[ /]| v)(\\d+)(?:\\.(\\d+)(?:\\.(\\d+))?)?)?',
    ),
    15 =>
    array (
      'regex' => '(HbbTV)/(\\d+)\\.(\\d+)\\.(\\d+) \\(',
    ),
    16 =>
    array (
      'regex' => '(Chimera|SeaMonkey|Camino)/(\\d+)\\.(\\d+)\\.?([ab]?\\d+[a-z]*)?',
    ),
    17 =>
    array (
      'regex' => '\\[FB.*;(FBAV)/(\\d+)(?:\\.(\\d+)(?:\\.(\\d)+)?)?',
      'family_replacement' => 'Facebook',
    ),
    18 =>
    array (
      'regex' => '\\[(Pinterest)/[^\\]]+\\]',
    ),
    19 =>
    array (
      'regex' => '(Pinterest)(?: for Android(?: Tablet)?)?/(\\d+)(?:\\.(\\d+)(?:\\.(\\d)+)?)?',
    ),
    20 =>
    array (
      'regex' => '(PaleMoon)/(\\d+)\\.(\\d+)\\.?(\\d+)?',
      'family_replacement' => 'Pale Moon',
    ),
    21 =>
    array (
      'regex' => '(Fennec)/(\\d+)\\.(\\d+)\\.?([ab]?\\d+[a-z]*)',
      'family_replacement' => 'Firefox Mobile',
    ),
    22 =>
    array (
      'regex' => '(Fennec)/(\\d+)\\.(\\d+)(pre)',
      'family_replacement' => 'Firefox Mobile',
    ),
    23 =>
    array (
      'regex' => '(Fennec)/(\\d+)\\.(\\d+)',
      'family_replacement' => 'Firefox Mobile',
    ),
    24 =>
    array (
      'regex' => '(?:Mobile|Tablet);.*(Firefox)/(\\d+)\\.(\\d+)',
      'family_replacement' => 'Firefox Mobile',
    ),
    25 =>
    array (
      'regex' => '(Namoroka|Shiretoko|Minefield)/(\\d+)\\.(\\d+)\\.(\\d+(?:pre)?)',
      'family_replacement' => 'Firefox ($1)',
    ),
    26 =>
    array (
      'regex' => '(Firefox)/(\\d+)\\.(\\d+)(a\\d+[a-z]*)',
      'family_replacement' => 'Firefox Alpha',
    ),
    27 =>
    array (
      'regex' => '(Firefox)/(\\d+)\\.(\\d+)(b\\d+[a-z]*)',
      'family_replacement' => 'Firefox Beta',
    ),
    28 =>
    array (
      'regex' => '(Firefox)-(?:\\d+\\.\\d+)?/(\\d+)\\.(\\d+)(a\\d+[a-z]*)',
      'family_replacement' => 'Firefox Alpha',
    ),
    29 =>
    array (
      'regex' => '(Firefox)-(?:\\d+\\.\\d+)?/(\\d+)\\.(\\d+)(b\\d+[a-z]*)',
      'family_replacement' => 'Firefox Beta',
    ),
    30 =>
    array (
      'regex' => '(Namoroka|Shiretoko|Minefield)/(\\d+)\\.(\\d+)([ab]\\d+[a-z]*)?',
      'family_replacement' => 'Firefox ($1)',
    ),
    31 =>
    array (
      'regex' => '(Firefox).*Tablet browser (\\d+)\\.(\\d+)\\.(\\d+)',
      'family_replacement' => 'MicroB',
    ),
    32 =>
    array (
      'regex' => '(MozillaDeveloperPreview)/(\\d+)\\.(\\d+)([ab]\\d+[a-z]*)?',
    ),
    33 =>
    array (
      'regex' => '(FxiOS)/(\\d+)\\.(\\d+)(\\.(\\d+))?(\\.(\\d+))?',
      'family_replacement' => 'Firefox iOS',
    ),
    34 =>
    array (
      'regex' => '(Flock)/(\\d+)\\.(\\d+)(b\\d+?)',
    ),
    35 =>
    array (
      'regex' => '(RockMelt)/(\\d+)\\.(\\d+)\\.(\\d+)',
    ),
    36 =>
    array (
      'regex' => '(Navigator)/(\\d+)\\.(\\d+)\\.(\\d+)',
      'family_replacement' => 'Netscape',
    ),
    37 =>
    array (
      'regex' => '(Navigator)/(\\d+)\\.(\\d+)([ab]\\d+)',
      'family_replacement' => 'Netscape',
    ),
    38 =>
    array (
      'regex' => '(Netscape6)/(\\d+)\\.(\\d+)\\.?([ab]?\\d+)?',
      'family_replacement' => 'Netscape',
    ),
    39 =>
    array (
      'regex' => '(MyIBrow)/(\\d+)\\.(\\d+)',
      'family_replacement' => 'My Internet Browser',
    ),
    40 =>
    array (
      'regex' => '(UC? ?Browser|UCWEB|U3)[ /]?(\\d+)\\.(\\d+)\\.(\\d+)',
      'family_replacement' => 'UC Browser',
    ),
    41 =>
    array (
      'regex' => '(Opera Tablet).*Version/(\\d+)\\.(\\d+)(?:\\.(\\d+))?',
    ),
    42 =>
    array (
      'regex' => '(Opera Mini)(?:/att)?/?(\\d+)?(?:\\.(\\d+))?(?:\\.(\\d+))?',
    ),
    43 =>
    array (
      'regex' => '(Opera)/.+Opera Mobi.+Version/(\\d+)\\.(\\d+)',
      'family_replacement' => 'Opera Mobile',
    ),
    44 =>
    array (
      'regex' => '(Opera)/(\\d+)\\.(\\d+).+Opera Mobi',
      'family_replacement' => 'Opera Mobile',
    ),
    45 =>
    array (
      'regex' => 'Opera Mobi.+(Opera)(?:/|\\s+)(\\d+)\\.(\\d+)',
      'family_replacement' => 'Opera Mobile',
    ),
    46 =>
    array (
      'regex' => 'Opera Mobi',
      'family_replacement' => 'Opera Mobile',
    ),
    47 =>
    array (
      'regex' => '(Opera)/9.80.*Version/(\\d+)\\.(\\d+)(?:\\.(\\d+))?',
    ),
    48 =>
    array (
      'regex' => '(?:Mobile Safari).*(OPR)/(\\d+)\\.(\\d+)\\.(\\d+)',
      'family_replacement' => 'Opera Mobile',
    ),
    49 =>
    array (
      'regex' => '(?:Chrome).*(OPR)/(\\d+)\\.(\\d+)\\.(\\d+)',
      'family_replacement' => 'Opera',
    ),
    50 =>
    array (
      'regex' => '(Coast)/(\\d+).(\\d+).(\\d+)',
      'family_replacement' => 'Opera Coast',
    ),
    51 =>
    array (
      'regex' => '(OPiOS)/(\\d+).(\\d+).(\\d+)',
      'family_replacement' => 'Opera Mini',
    ),
    52 =>
    array (
      'regex' => 'Chrome/.+( MMS)/(\\d+).(\\d+).(\\d+)',
      'family_replacement' => 'Opera Neon',
    ),
    53 =>
    array (
      'regex' => '(hpw|web)OS/(\\d+)\\.(\\d+)(?:\\.(\\d+))?',
      'family_replacement' => 'webOS Browser',
    ),
    54 =>
    array (
      'regex' => '(luakit)',
      'family_replacement' => 'LuaKit',
    ),
    55 =>
    array (
      'regex' => '(Snowshoe)/(\\d+)\\.(\\d+).(\\d+)',
    ),
    56 =>
    array (
      'regex' => 'Gecko/\\d+ (Lightning)/(\\d+)\\.(\\d+)\\.?((?:[ab]?\\d+[a-z]*)|(?:\\d*))',
    ),
    57 =>
    array (
      'regex' => '(Firefox)/(\\d+)\\.(\\d+)\\.(\\d+(?:pre)?) \\(Swiftfox\\)',
      'family_replacement' => 'Swiftfox',
    ),
    58 =>
    array (
      'regex' => '(Firefox)/(\\d+)\\.(\\d+)([ab]\\d+[a-z]*)? \\(Swiftfox\\)',
      'family_replacement' => 'Swiftfox',
    ),
    59 =>
    array (
      'regex' => '(rekonq)/(\\d+)\\.(\\d+)\\.?(\\d+)? Safari',
      'family_replacement' => 'Rekonq',
    ),
    60 =>
    array (
      'regex' => 'rekonq',
      'family_replacement' => 'Rekonq',
    ),
    61 =>
    array (
      'regex' => '(conkeror|Conkeror)/(\\d+)\\.(\\d+)\\.?(\\d+)?',
      'family_replacement' => 'Conkeror',
    ),
    62 =>
    array (
      'regex' => '(konqueror)/(\\d+)\\.(\\d+)\\.(\\d+)',
      'family_replacement' => 'Konqueror',
    ),
    63 =>
    array (
      'regex' => '(WeTab)-Browser',
    ),
    64 =>
    array (
      'regex' => '(Comodo_Dragon)/(\\d+)\\.(\\d+)\\.(\\d+)',
      'family_replacement' => 'Comodo Dragon',
    ),
    65 =>
    array (
      'regex' => '(Symphony) (\\d+).(\\d+)',
    ),
    66 =>
    array (
      'regex' => 'PLAYSTATION 3.+WebKit',
      'family_replacement' => 'NetFront NX',
    ),
    67 =>
    array (
      'regex' => 'PLAYSTATION 3',
      'family_replacement' => 'NetFront',
    ),
    68 =>
    array (
      'regex' => '(PlayStation Portable)',
      'family_replacement' => 'NetFront',
    ),
    69 =>
    array (
      'regex' => '(PlayStation Vita)',
      'family_replacement' => 'NetFront NX',
    ),
    70 =>
    array (
      'regex' => 'AppleWebKit.+ (NX)/(\\d+)\\.(\\d+)\\.(\\d+)',
      'family_replacement' => 'NetFront NX',
    ),
    71 =>
    array (
      'regex' => '(Nintendo 3DS)',
      'family_replacement' => 'NetFront NX',
    ),
    72 =>
    array (
      'regex' => '(Silk)/(\\d+)\\.(\\d+)(?:\\.([0-9\\-]+))?',
      'family_replacement' => 'Amazon Silk',
    ),
    73 =>
    array (
      'regex' => '(Puffin)/(\\d+)\\.(\\d+)(?:\\.(\\d+))?',
    ),
    74 =>
    array (
      'regex' => 'Windows Phone .*(Edge)/(\\d+)\\.(\\d+)',
      'family_replacement' => 'Edge Mobile',
    ),
    75 =>
    array (
      'regex' => '(SamsungBrowser)/(\\d+)\\.(\\d+)',
      'family_replacement' => 'Samsung Internet',
    ),
    76 =>
    array (
      'regex' => '(baidubrowser)[/\\s](\\d+)(?:\\.(\\d+)(?:\\.(\\d+))?)?',
      'family_replacement' => 'Baidu Browser',
    ),
    77 =>
    array (
      'regex' => '(FlyFlow)/(\\d+)\\.(\\d+)',
      'family_replacement' => 'Baidu Explorer',
    ),
    78 =>
    array (
      'regex' => '(CrMo)/(\\d+)\\.(\\d+)\\.(\\d+)\\.(\\d+)',
      'family_replacement' => 'Chrome Mobile',
    ),
    79 =>
    array (
      'regex' => '(CriOS)/(\\d+)\\.(\\d+)\\.(\\d+)\\.(\\d+)',
      'family_replacement' => 'Chrome Mobile iOS',
    ),
    80 =>
    array (
      'regex' => '(Chrome)/(\\d+)\\.(\\d+)\\.(\\d+)\\.(\\d+) Mobile(?:[ /]|$)',
      'family_replacement' => 'Chrome Mobile',
    ),
    81 =>
    array (
      'regex' => ' Mobile .*(Chrome)/(\\d+)\\.(\\d+)\\.(\\d+)\\.(\\d+)',
      'family_replacement' => 'Chrome Mobile',
    ),
    82 =>
    array (
      'regex' => '(chromeframe)/(\\d+)\\.(\\d+)\\.(\\d+)',
      'family_replacement' => 'Chrome Frame',
    ),
    83 =>
    array (
      'regex' => '(SLP Browser)/(\\d+)\\.(\\d+)',
      'family_replacement' => 'Tizen Browser',
    ),
    84 =>
    array (
      'regex' => '(SE 2\\.X) MetaSr (\\d+)\\.(\\d+)',
      'family_replacement' => 'Sogou Explorer',
    ),
    85 =>
    array (
      'regex' => '(MQQBrowser/Mini)(?:(\\d+)(?:\\.(\\d+)(?:\\.(\\d+))?)?)?',
      'family_replacement' => 'QQ Browser Mini',
    ),
    86 =>
    array (
      'regex' => '(MQQBrowser)(?:/(\\d+)(?:\\.(\\d+)(?:\\.(\\d+))?)?)?',
      'family_replacement' => 'QQ Browser Mobile',
    ),
    87 =>
    array (
      'regex' => '(QQBrowser)(?:/(\\d+)(?:\\.(\\d+)\\.(\\d+)(?:\\.(\\d+))?)?)?',
      'family_replacement' => 'QQ Browser',
    ),
    88 =>
    array (
      'regex' => '(Rackspace Monitoring)/(\\d+)\\.(\\d+)',
      'family_replacement' => 'RackspaceBot',
    ),
    89 =>
    array (
      'regex' => '(PyAMF)/(\\d+)\\.(\\d+)\\.(\\d+)',
    ),
    90 =>
    array (
      'regex' => '(YaBrowser)/(\\d+)\\.(\\d+)\\.(\\d+)',
      'family_replacement' => 'Yandex Browser',
    ),
    91 =>
    array (
      'regex' => '(Chrome)/(\\d+)\\.(\\d+)\\.(\\d+).* MRCHROME',
      'family_replacement' => 'Mail.ru Chromium Browser',
    ),
    92 =>
    array (
      'regex' => '(AOL) (\\d+)\\.(\\d+); AOLBuild (\\d+)',
    ),
    93 =>
    array (
      'regex' => '(MxBrowser)/(\\d+)\\.(\\d+)(?:\\.(\\d+))?',
      'family_replacement' => 'Maxthon',
    ),
    94 =>
    array (
      'regex' => '\\b(MobileIron|Crosswalk|FireWeb|Jasmine|ANTGalio|Midori|Fresco|Lobo|PaleMoon|Maxthon|Lynx|OmniWeb|Dillo|Camino|Demeter|Fluid|Fennec|Epiphany|Shiira|Sunrise|Spotify|Flock|Netscape|Lunascape|WebPilot|NetFront|Netfront|Konqueror|SeaMonkey|Kazehakase|Vienna|Iceape|Iceweasel|IceWeasel|Iron|K-Meleon|Sleipnir|Galeon|GranParadiso|Opera Mini|iCab|NetNewsWire|ThunderBrowse|Iris|UP\\.Browser|Bunjalloo|Google Earth|Raven for Mac|Openwave|MacOutlook|Electron)/(\\d+)\\.(\\d+)\\.(\\d+)',
    ),
    95 =>
    array (
      'regex' => 'Microsoft Office Outlook 12\\.\\d+\\.\\d+|MSOffice 12',
      'family_replacement' => 'Outlook',
      'v1_replacement' => '2007',
    ),
    96 =>
    array (
      'regex' => 'Microsoft Outlook 14\\.\\d+\\.\\d+|MSOffice 14',
      'family_replacement' => 'Outlook',
      'v1_replacement' => '2010',
    ),
    97 =>
    array (
      'regex' => 'Microsoft Outlook 15\\.\\d+\\.\\d+',
      'family_replacement' => 'Outlook',
      'v1_replacement' => '2013',
    ),
    98 =>
    array (
      'regex' => 'Microsoft Outlook (?:Mail )?16\\.\\d+\\.\\d+',
      'family_replacement' => 'Outlook',
      'v1_replacement' => '2016',
    ),
    99 =>
    array (
      'regex' => 'Outlook-Express\\/7\\.0.*',
      'family_replacement' => 'Windows Live Mail',
    ),
    100 =>
    array (
      'regex' => '(Airmail) (\\d+)\\.(\\d+)(?:\\.(\\d+))?',
    ),
    101 =>
    array (
      'regex' => '(Thunderbird)/(\\d+)\\.(\\d+)(?:\\.(\\d+(?:pre)?))?',
      'family_replacement' => 'Thunderbird',
    ),
    102 =>
    array (
      'regex' => '(Postbox)/(\\d+)\\.(\\d+)\\.(\\d+)',
      'family_replacement' => 'Postbox',
    ),
    103 =>
    array (
      'regex' => '(Barca(?:Pro)?)/(\\d+)\\.(\\d+)(?:\\.(\\d+))?',
      'family_replacement' => 'Barca',
    ),
    104 =>
    array (
      'regex' => '(Lotus-Notes)/(\\d+)\\.(\\d+)(?:\\.(\\d+))?',
      'family_replacement' => 'Lotus Notes',
    ),
    105 =>
    array (
      'regex' => '(Vivaldi)/(\\d+)\\.(\\d+)\\.(\\d+)',
    ),
    106 =>
    array (
      'regex' => '(Edge)/(\\d+)\\.(\\d+)',
    ),
    107 =>
    array (
      'regex' => '(brave)/(\\d+)\\.(\\d+)\\.(\\d+) Chrome',
      'family_replacement' => 'Brave',
    ),
    108 =>
    array (
      'regex' => '(Chrome)/(\\d+)\\.(\\d+)\\.(\\d+)[\\d.]* Iron[^/]',
      'family_replacement' => 'Iron',
    ),
    109 =>
    array (
      'regex' => '\\b(Dolphin)(?: |HDCN/|/INT\\-)(\\d+)\\.(\\d+)\\.?(\\d+)?',
    ),
    110 =>
    array (
      'regex' => '(bingbot|Bolt|AdobeAIR|Jasmine|IceCat|Skyfire|Midori|Maxthon|Lynx|Arora|IBrowse|Dillo|Camino|Shiira|Fennec|Phoenix|Flock|Netscape|Lunascape|Epiphany|WebPilot|Opera Mini|Opera|NetFront|Netfront|Konqueror|Googlebot|SeaMonkey|Kazehakase|Vienna|Iceape|Iceweasel|IceWeasel|Iron|K-Meleon|Sleipnir|Galeon|GranParadiso|iCab|iTunes|MacAppStore|NetNewsWire|Space Bison|Stainless|Orca|Dolfin|BOLT|Minimo|Tizen Browser|Polaris|Abrowser|Planetweb|ICE Browser|mDolphin|qutebrowser|Otter|QupZilla|MailBar|kmail2|YahooMobileMail|ExchangeWebServices|ExchangeServicesClient|Dragon|Outlook-iOS-Android)/(\\d+)\\.(\\d+)(?:\\.(\\d+))?',
    ),
    111 =>
    array (
      'regex' => '(Chromium|Chrome)/(\\d+)\\.(\\d+)(?:\\.(\\d+))?',
    ),
    112 =>
    array (
      'regex' => '(IEMobile)[ /](\\d+)\\.(\\d+)',
      'family_replacement' => 'IE Mobile',
    ),
    113 =>
    array (
      'regex' => '(BacaBerita App)\\/(\\d+)\\.(\\d+)\\.(\\d+)',
    ),
    114 =>
    array (
      'regex' => '(iRider|Crazy Browser|SkipStone|iCab|Lunascape|Sleipnir|Maemo Browser) (\\d+)\\.(\\d+)\\.(\\d+)',
    ),
    115 =>
    array (
      'regex' => '(iCab|Lunascape|Opera|Android|Jasmine|Polaris|Microsoft SkyDriveSync|The Bat!) (\\d+)\\.(\\d+)\\.?(\\d+)?',
    ),
    116 =>
    array (
      'regex' => '(Kindle)/(\\d+)\\.(\\d+)',
    ),
    117 =>
    array (
      'regex' => '(Android) Donut',
      'v1_replacement' => '1',
      'v2_replacement' => '2',
    ),
    118 =>
    array (
      'regex' => '(Android) Eclair',
      'v1_replacement' => '2',
      'v2_replacement' => '1',
    ),
    119 =>
    array (
      'regex' => '(Android) Froyo',
      'v1_replacement' => '2',
      'v2_replacement' => '2',
    ),
    120 =>
    array (
      'regex' => '(Android) Gingerbread',
      'v1_replacement' => '2',
      'v2_replacement' => '3',
    ),
    121 =>
    array (
      'regex' => '(Android) Honeycomb',
      'v1_replacement' => '3',
    ),
    122 =>
    array (
      'regex' => '(MSIE) (\\d+)\\.(\\d+).*XBLWP7',
      'family_replacement' => 'IE Large Screen',
    ),
    123 =>
    array (
      'regex' => '(Slack_SSB)/(\\d+)\\.(\\d+)\\.(\\d+)',
      'family_replacement' => 'Slack Desktop Client',
    ),
    124 =>
    array (
      'regex' => '(Obigo)InternetBrowser',
    ),
    125 =>
    array (
      'regex' => '(Obigo)\\-Browser',
    ),
    126 =>
    array (
      'regex' => '(Obigo|OBIGO)[^\\d]*(\\d+)(?:.(\\d+))?',
      'family_replacement' => 'Obigo',
    ),
    127 =>
    array (
      'regex' => '(MAXTHON|Maxthon) (\\d+)\\.(\\d+)',
      'family_replacement' => 'Maxthon',
    ),
    128 =>
    array (
      'regex' => '(Maxthon|MyIE2|Uzbl|Shiira)',
      'v1_replacement' => '0',
    ),
    129 =>
    array (
      'regex' => '(BrowseX) \\((\\d+)\\.(\\d+)\\.(\\d+)',
    ),
    130 =>
    array (
      'regex' => '(NCSA_Mosaic)/(\\d+)\\.(\\d+)',
      'family_replacement' => 'NCSA Mosaic',
    ),
    131 =>
    array (
      'regex' => '(POLARIS)/(\\d+)\\.(\\d+)',
      'family_replacement' => 'Polaris',
    ),
    132 =>
    array (
      'regex' => '(Embider)/(\\d+)\\.(\\d+)',
      'family_replacement' => 'Polaris',
    ),
    133 =>
    array (
      'regex' => '(BonEcho)/(\\d+)\\.(\\d+)\\.?([ab]?\\d+)?',
      'family_replacement' => 'Bon Echo',
    ),
    134 =>
    array (
      'regex' => '(iPod|iPhone|iPad).+Version/(\\d+)\\.(\\d+)(?:\\.(\\d+))?.*[ +]Safari',
      'family_replacement' => 'Mobile Safari',
    ),
    135 =>
    array (
      'regex' => '(iPod|iPhone|iPad).+Version/(\\d+)\\.(\\d+)(?:\\.(\\d+))?',
      'family_replacement' => 'Mobile Safari UI/WKWebView',
    ),
    136 =>
    array (
      'regex' => '(iPod|iPod touch|iPhone|iPad);.*CPU.*OS[ +](\\d+)_(\\d+)(?:_(\\d+))?.*Mobile.*[ +]Safari',
      'family_replacement' => 'Mobile Safari',
    ),
    137 =>
    array (
      'regex' => '(iPod|iPod touch|iPhone|iPad);.*CPU.*OS[ +](\\d+)_(\\d+)(?:_(\\d+))?.*Mobile',
      'family_replacement' => 'Mobile Safari UI/WKWebView',
    ),
    138 =>
    array (
      'regex' => '(iPod|iPhone|iPad).* Safari',
      'family_replacement' => 'Mobile Safari',
    ),
    139 =>
    array (
      'regex' => '(iPod|iPhone|iPad)',
      'family_replacement' => 'Mobile Safari UI/WKWebView',
    ),
    140 =>
    array (
      'regex' => '(AvantGo) (\\d+).(\\d+)',
    ),
    141 =>
    array (
      'regex' => '(OneBrowser)/(\\d+).(\\d+)',
      'family_replacement' => 'ONE Browser',
    ),
    142 =>
    array (
      'regex' => '(Avant)',
      'v1_replacement' => '1',
    ),
    143 =>
    array (
      'regex' => '(QtCarBrowser)',
      'v1_replacement' => '1',
    ),
    144 =>
    array (
      'regex' => '^(iBrowser/Mini)(\\d+).(\\d+)',
      'family_replacement' => 'iBrowser Mini',
    ),
    145 =>
    array (
      'regex' => '^(iBrowser|iRAPP)/(\\d+).(\\d+)',
    ),
    146 =>
    array (
      'regex' => '^(Nokia)',
      'family_replacement' => 'Nokia Services (WAP) Browser',
    ),
    147 =>
    array (
      'regex' => '(NokiaBrowser)/(\\d+)\\.(\\d+).(\\d+)\\.(\\d+)',
      'family_replacement' => 'Nokia Browser',
    ),
    148 =>
    array (
      'regex' => '(NokiaBrowser)/(\\d+)\\.(\\d+).(\\d+)',
      'family_replacement' => 'Nokia Browser',
    ),
    149 =>
    array (
      'regex' => '(NokiaBrowser)/(\\d+)\\.(\\d+)',
      'family_replacement' => 'Nokia Browser',
    ),
    150 =>
    array (
      'regex' => '(BrowserNG)/(\\d+)\\.(\\d+).(\\d+)',
      'family_replacement' => 'Nokia Browser',
    ),
    151 =>
    array (
      'regex' => '(Series60)/5\\.0',
      'family_replacement' => 'Nokia Browser',
      'v1_replacement' => '7',
      'v2_replacement' => '0',
    ),
    152 =>
    array (
      'regex' => '(Series60)/(\\d+)\\.(\\d+)',
      'family_replacement' => 'Nokia OSS Browser',
    ),
    153 =>
    array (
      'regex' => '(S40OviBrowser)/(\\d+)\\.(\\d+)\\.(\\d+)\\.(\\d+)',
      'family_replacement' => 'Ovi Browser',
    ),
    154 =>
    array (
      'regex' => '(Nokia)[EN]?(\\d+)',
    ),
    155 =>
    array (
      'regex' => '(PlayBook).+RIM Tablet OS (\\d+)\\.(\\d+)\\.(\\d+)',
      'family_replacement' => 'BlackBerry WebKit',
    ),
    156 =>
    array (
      'regex' => '(Black[bB]erry|BB10).+Version/(\\d+)\\.(\\d+)\\.(\\d+)',
      'family_replacement' => 'BlackBerry WebKit',
    ),
    157 =>
    array (
      'regex' => '(Black[bB]erry)\\s?(\\d+)',
      'family_replacement' => 'BlackBerry',
    ),
    158 =>
    array (
      'regex' => '(OmniWeb)/v(\\d+)\\.(\\d+)',
    ),
    159 =>
    array (
      'regex' => '(Blazer)/(\\d+)\\.(\\d+)',
      'family_replacement' => 'Palm Blazer',
    ),
    160 =>
    array (
      'regex' => '(Pre)/(\\d+)\\.(\\d+)',
      'family_replacement' => 'Palm Pre',
    ),
    161 =>
    array (
      'regex' => '(ELinks)/(\\d+)\\.(\\d+)',
    ),
    162 =>
    array (
      'regex' => '(ELinks) \\((\\d+)\\.(\\d+)',
    ),
    163 =>
    array (
      'regex' => '(Links) \\((\\d+)\\.(\\d+)',
    ),
    164 =>
    array (
      'regex' => '(QtWeb) Internet Browser/(\\d+)\\.(\\d+)',
    ),
    165 =>
    array (
      'regex' => '(PhantomJS)/(\\d+)\\.(\\d+)\\.(\\d+)',
    ),
    166 =>
    array (
      'regex' => '(AppleWebKit)/(\\d+)\\.?(\\d+)?\\+ .* Safari',
      'family_replacement' => 'WebKit Nightly',
    ),
    167 =>
    array (
      'regex' => '(Version)/(\\d+)\\.(\\d+)(?:\\.(\\d+))?.*Safari/',
      'family_replacement' => 'Safari',
    ),
    168 =>
    array (
      'regex' => '(Safari)/\\d+',
    ),
    169 =>
    array (
      'regex' => '(OLPC)/Update(\\d+)\\.(\\d+)',
    ),
    170 =>
    array (
      'regex' => '(OLPC)/Update()\\.(\\d+)',
      'v1_replacement' => '0',
    ),
    171 =>
    array (
      'regex' => '(SEMC\\-Browser)/(\\d+)\\.(\\d+)',
    ),
    172 =>
    array (
      'regex' => '(Teleca)',
      'family_replacement' => 'Teleca Browser',
    ),
    173 =>
    array (
      'regex' => '(Phantom)/V(\\d+)\\.(\\d+)',
      'family_replacement' => 'Phantom Browser',
    ),
    174 =>
    array (
      'regex' => 'Trident(.*)rv.(\\d+)\\.(\\d+)',
      'family_replacement' => 'IE',
    ),
    175 =>
    array (
      'regex' => '(Espial)/(\\d+)(?:\\.(\\d+))?(?:\\.(\\d+))?',
    ),
    176 =>
    array (
      'regex' => '(AppleWebKit)/(\\d+)\\.(\\d+)\\.(\\d+)',
      'family_replacement' => 'Apple Mail',
    ),
    177 =>
    array (
      'regex' => '(Firefox)/(\\d+)\\.(\\d+)\\.(\\d+)',
    ),
    178 =>
    array (
      'regex' => '(Firefox)/(\\d+)\\.(\\d+)(pre|[ab]\\d+[a-z]*)?',
    ),
    179 =>
    array (
      'regex' => '([MS]?IE) (\\d+)\\.(\\d+)',
      'family_replacement' => 'IE',
    ),
    180 =>
    array (
      'regex' => '(python-requests)/(\\d+)\\.(\\d+)',
      'family_replacement' => 'Python Requests',
    ),
    181 =>
    array (
      'regex' => '\\b(Windows-Update-Agent|Microsoft-CryptoAPI|SophosUpdateManager|SophosAgent|Debian APT-HTTP|Ubuntu APT-HTTP|libcurl-agent|libwww-perl|urlgrabber|curl|Wget|OpenBSD ftp|jupdate)(?:[ /](\\d+)(?:\\.(\\d+)(?:\\.(\\d+))?)?)?',
    ),
    182 =>
    array (
      'regex' => '(Java)[/ ]{0,1}\\d+\\.(\\d+)\\.(\\d+)[_-]*([a-zA-Z0-9]+)*',
    ),
    183 =>
    array (
      'regex' => '^(Roku)/DVP-(\\d+)\\.(\\d+)',
    ),
    184 =>
    array (
      'regex' => '(Kurio)\\/(\\d+)\\.(\\d+)\\.(\\d+)',
      'family_replacement' => 'Kurio App',
    ),
  ),
  'os_parsers' =>
  array (
    0 =>
    array (
      'regex' => 'HbbTV/\\d+\\.\\d+\\.\\d+ \\( ;(LG)E ;NetCast 4.0',
      'os_v1_replacement' => '2013',
    ),
    1 =>
    array (
      'regex' => 'HbbTV/\\d+\\.\\d+\\.\\d+ \\( ;(LG)E ;NetCast 3.0',
      'os_v1_replacement' => '2012',
    ),
    2 =>
    array (
      'regex' => 'HbbTV/1.1.1 \\(;;;;;\\) Maple_2011',
      'os_replacement' => 'Samsung',
      'os_v1_replacement' => '2011',
    ),
    3 =>
    array (
      'regex' => 'HbbTV/\\d+\\.\\d+\\.\\d+ \\(;(Samsung);SmartTV([0-9]{4});.*FXPDEUC',
      'os_v2_replacement' => 'UE40F7000',
    ),
    4 =>
    array (
      'regex' => 'HbbTV/\\d+\\.\\d+\\.\\d+ \\(;(Samsung);SmartTV([0-9]{4});.*MST12DEUC',
      'os_v2_replacement' => 'UE32F4500',
    ),
    5 =>
    array (
      'regex' => 'HbbTV/1.1.1 \\(; (Philips);.*NETTV/4',
      'os_v1_replacement' => '2013',
    ),
    6 =>
    array (
      'regex' => 'HbbTV/1.1.1 \\(; (Philips);.*NETTV/3',
      'os_v1_replacement' => '2012',
    ),
    7 =>
    array (
      'regex' => 'HbbTV/1.1.1 \\(; (Philips);.*NETTV/2',
      'os_v1_replacement' => '2011',
    ),
    8 =>
    array (
      'regex' => 'HbbTV/\\d+\\.\\d+\\.\\d+.*(firetv)-firefox-plugin (\\d+).(\\d+).(\\d+)',
      'os_replacement' => 'FireHbbTV',
    ),
    9 =>
    array (
      'regex' => 'HbbTV/\\d+\\.\\d+\\.\\d+ \\(.*; ?([a-zA-Z]+) ?;.*(201[1-9]).*\\)',
    ),
    10 =>
    array (
      'regex' => '(Windows Phone) (?:OS[ /])?(\\d+)\\.(\\d+)',
    ),
    11 =>
    array (
      'regex' => '(CPU[ +]OS|iPhone[ +]OS|CPU[ +]iPhone)[ +]+(\\d+)[_\\.](\\d+)(?:[_\\.](\\d+))?.*Outlook-iOS-Android',
      'os_replacement' => 'iOS',
    ),
    12 =>
    array (
      'regex' => '(Android)[ \\-/](\\d+)\\.(\\d+)(?:[.\\-]([a-z0-9]+))?',
    ),
    13 =>
    array (
      'regex' => '(Android) Donut',
      'os_v1_replacement' => '1',
      'os_v2_replacement' => '2',
    ),
    14 =>
    array (
      'regex' => '(Android) Eclair',
      'os_v1_replacement' => '2',
      'os_v2_replacement' => '1',
    ),
    15 =>
    array (
      'regex' => '(Android) Froyo',
      'os_v1_replacement' => '2',
      'os_v2_replacement' => '2',
    ),
    16 =>
    array (
      'regex' => '(Android) Gingerbread',
      'os_v1_replacement' => '2',
      'os_v2_replacement' => '3',
    ),
    17 =>
    array (
      'regex' => '(Android) Honeycomb',
      'os_v1_replacement' => '3',
    ),
    18 =>
    array (
      'regex' => '^UCWEB.*; (Adr) (\\d+)\\.(\\d+)(?:[.\\-]([a-z0-9]+))?;',
      'os_replacement' => 'Android',
    ),
    19 =>
    array (
      'regex' => '^UCWEB.*; (iPad|iPh|iPd) OS (\\d+)_(\\d+)(?:_(\\d+))?;',
      'os_replacement' => 'iOS',
    ),
    20 =>
    array (
      'regex' => '^UCWEB.*; (wds) (\\d+)\\.(\\d+)(?:\\.(\\d+))?;',
      'os_replacement' => 'Windows Phone',
    ),
    21 =>
    array (
      'regex' => '^(JUC).*; ?U; ?(?:Android)?(\\d+)\\.(\\d+)(?:[\\.\\-]([a-z0-9]+))?',
      'os_replacement' => 'Android',
    ),
    22 =>
    array (
      'regex' => '(Silk-Accelerated=[a-z]{4,5})',
      'os_replacement' => 'Android',
    ),
    23 =>
    array (
      'regex' => '(XBLWP7)',
      'os_replacement' => 'Windows Phone',
    ),
    24 =>
    array (
      'regex' => '(Windows ?Mobile)',
      'os_replacement' => 'Windows Mobile',
    ),
    25 =>
    array (
      'regex' => '(Windows (?:NT 5\\.2|NT 5\\.1))',
      'os_replacement' => 'Windows XP',
    ),
    26 =>
    array (
      'regex' => '(Windows NT 6\\.1)',
      'os_replacement' => 'Windows 7',
    ),
    27 =>
    array (
      'regex' => '(Windows NT 6\\.0)',
      'os_replacement' => 'Windows Vista',
    ),
    28 =>
    array (
      'regex' => '(Win 9x 4\\.90)',
      'os_replacement' => 'Windows ME',
    ),
    29 =>
    array (
      'regex' => '(Windows 98|Windows XP|Windows ME|Windows 95|Windows CE|Windows 7|Windows NT 4\\.0|Windows Vista|Windows 2000|Windows 3.1)',
    ),
    30 =>
    array (
      'regex' => '(Windows NT 6\\.2; ARM;)',
      'os_replacement' => 'Windows RT',
    ),
    31 =>
    array (
      'regex' => '(Windows NT 6\\.2)',
      'os_replacement' => 'Windows 8',
    ),
    32 =>
    array (
      'regex' => '(Windows NT 6\\.3; ARM;)',
      'os_replacement' => 'Windows RT 8.1',
    ),
    33 =>
    array (
      'regex' => '(Windows NT 6\\.3)',
      'os_replacement' => 'Windows 8.1',
    ),
    34 =>
    array (
      'regex' => '(Windows NT 6\\.4)',
      'os_replacement' => 'Windows 10',
    ),
    35 =>
    array (
      'regex' => '(Windows NT 10\\.0)',
      'os_replacement' => 'Windows 10',
    ),
    36 =>
    array (
      'regex' => '(Windows NT 5\\.0)',
      'os_replacement' => 'Windows 2000',
    ),
    37 =>
    array (
      'regex' => '(WinNT4.0)',
      'os_replacement' => 'Windows NT 4.0',
    ),
    38 =>
    array (
      'regex' => '(Windows ?CE)',
      'os_replacement' => 'Windows CE',
    ),
    39 =>
    array (
      'regex' => 'Win ?(95|98|3.1|NT|ME|2000)',
      'os_replacement' => 'Windows $1',
    ),
    40 =>
    array (
      'regex' => 'Win16',
      'os_replacement' => 'Windows 3.1',
    ),
    41 =>
    array (
      'regex' => 'Win32',
      'os_replacement' => 'Windows 95',
    ),
    42 =>
    array (
      'regex' => '(Tizen)[/ ](\\d+)\\.(\\d+)',
    ),
    43 =>
    array (
      'regex' => '((?:Mac[ +]?|; )OS[ +]X)[\\s+/](?:(\\d+)[_.](\\d+)(?:[_.](\\d+))?|Mach-O)',
      'os_replacement' => 'Mac OS X',
    ),
    44 =>
    array (
      'regex' => ' (Dar)(win)/(9).(\\d+).*\\((?:i386|x86_64|Power Macintosh)\\)',
      'os_replacement' => 'Mac OS X',
      'os_v1_replacement' => '10',
      'os_v2_replacement' => '5',
    ),
    45 =>
    array (
      'regex' => ' (Dar)(win)/(10).(\\d+).*\\((?:i386|x86_64)\\)',
      'os_replacement' => 'Mac OS X',
      'os_v1_replacement' => '10',
      'os_v2_replacement' => '6',
    ),
    46 =>
    array (
      'regex' => ' (Dar)(win)/(11).(\\d+).*\\((?:i386|x86_64)\\)',
      'os_replacement' => 'Mac OS X',
      'os_v1_replacement' => '10',
      'os_v2_replacement' => '7',
    ),
    47 =>
    array (
      'regex' => ' (Dar)(win)/(12).(\\d+).*\\((?:i386|x86_64)\\)',
      'os_replacement' => 'Mac OS X',
      'os_v1_replacement' => '10',
      'os_v2_replacement' => '8',
    ),
    48 =>
    array (
      'regex' => ' (Dar)(win)/(13).(\\d+).*\\((?:i386|x86_64)\\)',
      'os_replacement' => 'Mac OS X',
      'os_v1_replacement' => '10',
      'os_v2_replacement' => '9',
    ),
    49 =>
    array (
      'regex' => 'Mac_PowerPC',
      'os_replacement' => 'Mac OS',
    ),
    50 =>
    array (
      'regex' => '(?:PPC|Intel) (Mac OS X)',
    ),
    51 =>
    array (
      'regex' => '(Apple\\s?TV)(?:/(\\d+)\\.(\\d+))?',
      'os_replacement' => 'ATV OS X',
    ),
    52 =>
    array (
      'regex' => '(CPU[ +]OS|iPhone[ +]OS|CPU[ +]iPhone|CPU IPhone OS)[ +]+(\\d+)[_\\.](\\d+)(?:[_\\.](\\d+))?',
      'os_replacement' => 'iOS',
    ),
    53 =>
    array (
      'regex' => '(iPhone|iPad|iPod); Opera',
      'os_replacement' => 'iOS',
    ),
    54 =>
    array (
      'regex' => '(iPhone|iPad|iPod).*Mac OS X.*Version/(\\d+)\\.(\\d+)',
      'os_replacement' => 'iOS',
    ),
    55 =>
    array (
      'regex' => '(CFNetwork)/(5)48\\.0\\.3.* Darwin/11\\.0\\.0',
      'os_replacement' => 'iOS',
    ),
    56 =>
    array (
      'regex' => '(CFNetwork)/(5)48\\.(0)\\.4.* Darwin/(1)1\\.0\\.0',
      'os_replacement' => 'iOS',
    ),
    57 =>
    array (
      'regex' => '(CFNetwork)/(5)48\\.(1)\\.4',
      'os_replacement' => 'iOS',
    ),
    58 =>
    array (
      'regex' => '(CFNetwork)/(4)85\\.1(3)\\.9',
      'os_replacement' => 'iOS',
    ),
    59 =>
    array (
      'regex' => '(CFNetwork)/(6)09\\.(1)\\.4',
      'os_replacement' => 'iOS',
    ),
    60 =>
    array (
      'regex' => '(CFNetwork)/(6)(0)9',
      'os_replacement' => 'iOS',
    ),
    61 =>
    array (
      'regex' => '(CFNetwork)/6(7)2\\.(1)\\.13',
      'os_replacement' => 'iOS',
    ),
    62 =>
    array (
      'regex' => '(CFNetwork)/6(7)2\\.(1)\\.(1)4',
      'os_replacement' => 'iOS',
    ),
    63 =>
    array (
      'regex' => '(CF)(Network)/6(7)(2)\\.1\\.15',
      'os_replacement' => 'iOS',
      'os_v1_replacement' => '7',
      'os_v2_replacement' => '1',
    ),
    64 =>
    array (
      'regex' => '(CFNetwork)/6(7)2\\.(0)\\.(?:2|8)',
      'os_replacement' => 'iOS',
    ),
    65 =>
    array (
      'regex' => '(CFNetwork)/709\\.1',
      'os_replacement' => 'iOS',
      'os_v1_replacement' => '8',
      'os_v2_replacement' => '0.b5',
    ),
    66 =>
    array (
      'regex' => '(CF)(Network)/711\\.(\\d)',
      'os_replacement' => 'iOS',
      'os_v1_replacement' => '8',
    ),
    67 =>
    array (
      'regex' => '(CF)(Network)/(720)\\.(\\d)',
      'os_replacement' => 'Mac OS X',
      'os_v1_replacement' => '10',
      'os_v2_replacement' => '10',
    ),
    68 =>
    array (
      'regex' => '(CF)(Network)/758\\.(\\d)',
      'os_replacement' => 'iOS',
      'os_v1_replacement' => '9',
    ),
    69 =>
    array (
      'regex' => '(CF)(Network)/808\\.(\\d)',
      'os_replacement' => 'iOS',
      'os_v1_replacement' => '10',
    ),
    70 =>
    array (
      'regex' => 'CFNetwork/.* Darwin/(9)\\.\\d+',
      'os_replacement' => 'iOS',
      'os_v1_replacement' => '1',
    ),
    71 =>
    array (
      'regex' => 'CFNetwork/.* Darwin/(10)\\.\\d+',
      'os_replacement' => 'iOS',
      'os_v1_replacement' => '4',
    ),
    72 =>
    array (
      'regex' => 'CFNetwork/.* Darwin/(11)\\.\\d+',
      'os_replacement' => 'iOS',
      'os_v1_replacement' => '5',
    ),
    73 =>
    array (
      'regex' => 'CFNetwork/.* Darwin/(13)\\.\\d+',
      'os_replacement' => 'iOS',
      'os_v1_replacement' => '6',
    ),
    74 =>
    array (
      'regex' => 'CFNetwork/6.* Darwin/(14)\\.\\d+',
      'os_replacement' => 'iOS',
      'os_v1_replacement' => '7',
    ),
    75 =>
    array (
      'regex' => 'CFNetwork/7.* Darwin/(14)\\.\\d+',
      'os_replacement' => 'iOS',
      'os_v1_replacement' => '8',
      'os_v2_replacement' => '0',
    ),
    76 =>
    array (
      'regex' => 'CFNetwork/7.* Darwin/(15)\\.\\d+',
      'os_replacement' => 'iOS',
      'os_v1_replacement' => '9',
      'os_v2_replacement' => '0',
    ),
    77 =>
    array (
      'regex' => 'CFNetwork/8.* Darwin/(16)\\.\\d+',
      'os_replacement' => 'iOS',
      'os_v1_replacement' => '10',
    ),
    78 =>
    array (
      'regex' => '\\b(iOS[ /]|iOS; |iPhone(?:/| v|[ _]OS[/,]|; | OS : |\\d,\\d/|\\d,\\d; )|iPad/)(\\d{1,2})[_\\.](\\d{1,2})(?:[_\\.](\\d+))?',
      'os_replacement' => 'iOS',
    ),
    79 =>
    array (
      'regex' => '\\((iOS);',
    ),
    80 =>
    array (
      'regex' => '(tvOS)/(\\d+).(\\d+)',
      'os_replacement' => 'tvOS',
    ),
    81 =>
    array (
      'regex' => '(CrOS) [a-z0-9_]+ (\\d+)\\.(\\d+)(?:\\.(\\d+))?',
      'os_replacement' => 'Chrome OS',
    ),
    82 =>
    array (
      'regex' => '([Dd]ebian)',
      'os_replacement' => 'Debian',
    ),
    83 =>
    array (
      'regex' => '(Linux Mint)(?:/(\\d+))?',
    ),
    84 =>
    array (
      'regex' => '(Mandriva)(?: Linux)?/(?:[\\d.-]+m[a-z]{2}(\\d+).(\\d))?',
    ),
    85 =>
    array (
      'regex' => '(Symbian[Oo][Ss])[/ ](\\d+)\\.(\\d+)',
      'os_replacement' => 'Symbian OS',
    ),
    86 =>
    array (
      'regex' => '(Symbian/3).+NokiaBrowser/7\\.3',
      'os_replacement' => 'Symbian^3 Anna',
    ),
    87 =>
    array (
      'regex' => '(Symbian/3).+NokiaBrowser/7\\.4',
      'os_replacement' => 'Symbian^3 Belle',
    ),
    88 =>
    array (
      'regex' => '(Symbian/3)',
      'os_replacement' => 'Symbian^3',
    ),
    89 =>
    array (
      'regex' => '\\b(Series 60|SymbOS|S60Version|S60V\\d|S60\\b)',
      'os_replacement' => 'Symbian OS',
    ),
    90 =>
    array (
      'regex' => '(MeeGo)',
    ),
    91 =>
    array (
      'regex' => 'Symbian [Oo][Ss]',
      'os_replacement' => 'Symbian OS',
    ),
    92 =>
    array (
      'regex' => 'Series40;',
      'os_replacement' => 'Nokia Series 40',
    ),
    93 =>
    array (
      'regex' => 'Series30Plus;',
      'os_replacement' => 'Nokia Series 30 Plus',
    ),
    94 =>
    array (
      'regex' => '(BB10);.+Version/(\\d+)\\.(\\d+)\\.(\\d+)',
      'os_replacement' => 'BlackBerry OS',
    ),
    95 =>
    array (
      'regex' => '(Black[Bb]erry)[0-9a-z]+/(\\d+)\\.(\\d+)\\.(\\d+)(?:\\.(\\d+))?',
      'os_replacement' => 'BlackBerry OS',
    ),
    96 =>
    array (
      'regex' => '(Black[Bb]erry).+Version/(\\d+)\\.(\\d+)\\.(\\d+)(?:\\.(\\d+))?',
      'os_replacement' => 'BlackBerry OS',
    ),
    97 =>
    array (
      'regex' => '(RIM Tablet OS) (\\d+)\\.(\\d+)\\.(\\d+)',
      'os_replacement' => 'BlackBerry Tablet OS',
    ),
    98 =>
    array (
      'regex' => '(Play[Bb]ook)',
      'os_replacement' => 'BlackBerry Tablet OS',
    ),
    99 =>
    array (
      'regex' => '(Black[Bb]erry)',
      'os_replacement' => 'BlackBerry OS',
    ),
    100 =>
    array (
      'regex' => '\\((?:Mobile|Tablet);.+Gecko/18.0 Firefox/\\d+\\.\\d+',
      'os_replacement' => 'Firefox OS',
      'os_v1_replacement' => '1',
      'os_v2_replacement' => '0',
      'os_v3_replacement' => '1',
    ),
    101 =>
    array (
      'regex' => '\\((?:Mobile|Tablet);.+Gecko/18.1 Firefox/\\d+\\.\\d+',
      'os_replacement' => 'Firefox OS',
      'os_v1_replacement' => '1',
      'os_v2_replacement' => '1',
    ),
    102 =>
    array (
      'regex' => '\\((?:Mobile|Tablet);.+Gecko/26.0 Firefox/\\d+\\.\\d+',
      'os_replacement' => 'Firefox OS',
      'os_v1_replacement' => '1',
      'os_v2_replacement' => '2',
    ),
    103 =>
    array (
      'regex' => '\\((?:Mobile|Tablet);.+Gecko/28.0 Firefox/\\d+\\.\\d+',
      'os_replacement' => 'Firefox OS',
      'os_v1_replacement' => '1',
      'os_v2_replacement' => '3',
    ),
    104 =>
    array (
      'regex' => '\\((?:Mobile|Tablet);.+Gecko/30.0 Firefox/\\d+\\.\\d+',
      'os_replacement' => 'Firefox OS',
      'os_v1_replacement' => '1',
      'os_v2_replacement' => '4',
    ),
    105 =>
    array (
      'regex' => '\\((?:Mobile|Tablet);.+Gecko/32.0 Firefox/\\d+\\.\\d+',
      'os_replacement' => 'Firefox OS',
      'os_v1_replacement' => '2',
      'os_v2_replacement' => '0',
    ),
    106 =>
    array (
      'regex' => '\\((?:Mobile|Tablet);.+Gecko/34.0 Firefox/\\d+\\.\\d+',
      'os_replacement' => 'Firefox OS',
      'os_v1_replacement' => '2',
      'os_v2_replacement' => '1',
    ),
    107 =>
    array (
      'regex' => '\\((?:Mobile|Tablet);.+Firefox/\\d+\\.\\d+',
      'os_replacement' => 'Firefox OS',
    ),
    108 =>
    array (
      'regex' => '(BREW)[ /](\\d+)\\.(\\d+)\\.(\\d+)',
    ),
    109 =>
    array (
      'regex' => '(BREW);',
    ),
    110 =>
    array (
      'regex' => '(Brew MP|BMP)[ /](\\d+)\\.(\\d+)\\.(\\d+)',
      'os_replacement' => 'Brew MP',
    ),
    111 =>
    array (
      'regex' => 'BMP;',
      'os_replacement' => 'Brew MP',
    ),
    112 =>
    array (
      'regex' => '(GoogleTV)(?: (\\d+)\\.(\\d+)(?:\\.(\\d+))?|/[\\da-z]+)',
    ),
    113 =>
    array (
      'regex' => '(WebTV)/(\\d+).(\\d+)',
    ),
    114 =>
    array (
      'regex' => '(CrKey)(?:[/](\\d+)\\.(\\d+)(?:\\.(\\d+))?)?',
      'os_replacement' => 'Chromecast',
    ),
    115 =>
    array (
      'regex' => '(hpw|web)OS/(\\d+)\\.(\\d+)(?:\\.(\\d+))?',
      'os_replacement' => 'webOS',
    ),
    116 =>
    array (
      'regex' => '(VRE);',
    ),
    117 =>
    array (
      'regex' => '(Fedora|Red Hat|PCLinuxOS|Puppy|Ubuntu|Kindle|Bada|Lubuntu|BackTrack|Slackware|(?:Free|Open|Net|\\b)BSD)[/ ](\\d+)\\.(\\d+)(?:\\.(\\d+)(?:\\.(\\d+))?)?',
    ),
    118 =>
    array (
      'regex' => '(Linux)[ /](\\d+)\\.(\\d+)(?:\\.(\\d+))?.*gentoo',
      'os_replacement' => 'Gentoo',
    ),
    119 =>
    array (
      'regex' => '\\((Bada);',
    ),
    120 =>
    array (
      'regex' => '(Windows|Android|WeTab|Maemo|Web0S)',
    ),
    121 =>
    array (
      'regex' => '(Ubuntu|Kubuntu|Arch Linux|CentOS|Slackware|Gentoo|openSUSE|SUSE|Red Hat|Fedora|PCLinuxOS|Mageia|(?:Free|Open|Net|\\b)BSD)',
    ),
    122 =>
    array (
      'regex' => '(Linux)(?:[ /](\\d+)\\.(\\d+)(?:\\.(\\d+))?)?',
    ),
    123 =>
    array (
      'regex' => 'SunOS',
      'os_replacement' => 'Solaris',
    ),
    124 =>
    array (
      'regex' => '^(Roku)/DVP-(\\d+)\\.(\\d+)',
    ),
  ),
  'device_parsers' =>
  array (
    0 =>
    array (
      'regex' => '(?:(?:iPhone|Windows CE|Windows Phone|Android).*(?:(?:Bot|Yeti)-Mobile|YRSpider|BingPreview|bots?/\\d|(?:bot|spider)\\.html)|AdsBot-Google-Mobile.*iPhone)',
      'regex_flag' => 'i',
      'device_replacement' => 'Spider',
      'brand_replacement' => 'Spider',
      'model_replacement' => 'Smartphone',
    ),
    1 =>
    array (
      'regex' => '(?:DoCoMo|\\bMOT\\b|\\bLG\\b|Nokia|Samsung|SonyEricsson).*(?:(?:Bot|Yeti)-Mobile|bots?/\\d|(?:bot|crawler)\\.html|(?:jump|google|Wukong)bot|ichiro/mobile|/spider|YahooSeeker)',
      'regex_flag' => 'i',
      'device_replacement' => 'Spider',
      'brand_replacement' => 'Spider',
      'model_replacement' => 'Feature Phone',
    ),
    2 =>
    array (
      'regex' => '\\bSmartWatch *\\( *([^;]+) *; *([^;]+) *;',
      'device_replacement' => '$1 $2',
      'brand_replacement' => '$1',
      'model_replacement' => '$2',
    ),
    3 =>
    array (
      'regex' => 'Android Application[^\\-]+ - (Sony) ?(Ericsson)? (.+) \\w+ - ',
      'device_replacement' => '$1 $2',
      'brand_replacement' => '$1$2',
      'model_replacement' => '$3',
    ),
    4 =>
    array (
      'regex' => 'Android Application[^\\-]+ - (?:HTC|HUAWEI|LGE|LENOVO|MEDION|TCT) (HTC|HUAWEI|LG|LENOVO|MEDION|ALCATEL)[ _\\-](.+) \\w+ - ',
      'regex_flag' => 'i',
      'device_replacement' => '$1 $2',
      'brand_replacement' => '$1',
      'model_replacement' => '$2',
    ),
    5 =>
    array (
      'regex' => 'Android Application[^\\-]+ - ([^ ]+) (.+) \\w+ - ',
      'device_replacement' => '$1 $2',
      'brand_replacement' => '$1',
      'model_replacement' => '$2',
    ),
    6 =>
    array (
      'regex' => '; *([BLRQ]C\\d{4}[A-Z]+) +Build/',
      'device_replacement' => '3Q $1',
      'brand_replacement' => '3Q',
      'model_replacement' => '$1',
    ),
    7 =>
    array (
      'regex' => '; *(?:3Q_)([^;/]+) +Build',
      'device_replacement' => '3Q $1',
      'brand_replacement' => '3Q',
      'model_replacement' => '$1',
    ),
    8 =>
    array (
      'regex' => 'Android [34].*; *(A100|A101|A110|A200|A210|A211|A500|A501|A510|A511|A700(?: Lite| 3G)?|A701|B1-A71|A1-\\d{3}|B1-\\d{3}|V360|V370|W500|W500P|W501|W501P|W510|W511|W700|Slider SL101|DA22[^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Acer',
      'model_replacement' => '$1',
    ),
    9 =>
    array (
      'regex' => '; *Acer Iconia Tab ([^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Acer',
      'model_replacement' => '$1',
    ),
    10 =>
    array (
      'regex' => '; *(Z1[1235]0|E320[^/]*|S500|S510|Liquid[^;/]*|Iconia A\\d+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Acer',
      'model_replacement' => '$1',
    ),
    11 =>
    array (
      'regex' => '; *(Acer |ACER )([^;/]+) Build',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'Acer',
      'model_replacement' => '$2',
    ),
    12 =>
    array (
      'regex' => '; *(Advent )?(Vega(?:Bean|Comb)?).* Build',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'Advent',
      'model_replacement' => '$2',
    ),
    13 =>
    array (
      'regex' => '; *(Ainol )?((?:NOVO|[Nn]ovo)[^;/]+) Build',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'Ainol',
      'model_replacement' => '$2',
    ),
    14 =>
    array (
      'regex' => '; *AIRIS[ _\\-]?([^/;\\)]+) *(?:;|\\)|Build)',
      'regex_flag' => 'i',
      'device_replacement' => '$1',
      'brand_replacement' => 'Airis',
      'model_replacement' => '$1',
    ),
    15 =>
    array (
      'regex' => '; *(OnePAD[^;/]+) Build',
      'regex_flag' => 'i',
      'device_replacement' => '$1',
      'brand_replacement' => 'Airis',
      'model_replacement' => '$1',
    ),
    16 =>
    array (
      'regex' => '; *Airpad[ \\-]([^;/]+) Build',
      'device_replacement' => 'Airpad $1',
      'brand_replacement' => 'Airpad',
      'model_replacement' => '$1',
    ),
    17 =>
    array (
      'regex' => '; *(one ?touch) (EVO7|T10|T20) Build',
      'device_replacement' => 'Alcatel One Touch $2',
      'brand_replacement' => 'Alcatel',
      'model_replacement' => 'One Touch $2',
    ),
    18 =>
    array (
      'regex' => '; *(?:alcatel[ _])?(?:(?:one[ _]?touch[ _])|ot[ \\-])([^;/]+);? Build',
      'regex_flag' => 'i',
      'device_replacement' => 'Alcatel One Touch $1',
      'brand_replacement' => 'Alcatel',
      'model_replacement' => 'One Touch $1',
    ),
    19 =>
    array (
      'regex' => '; *(TCL)[ _]([^;/]+) Build',
      'device_replacement' => '$1 $2',
      'brand_replacement' => '$1',
      'model_replacement' => '$2',
    ),
    20 =>
    array (
      'regex' => '; *(Vodafone Smart II|Optimus_Madrid) Build',
      'device_replacement' => 'Alcatel $1',
      'brand_replacement' => 'Alcatel',
      'model_replacement' => '$1',
    ),
    21 =>
    array (
      'regex' => '; *BASE_Lutea_3 Build',
      'device_replacement' => 'Alcatel One Touch 998',
      'brand_replacement' => 'Alcatel',
      'model_replacement' => 'One Touch 998',
    ),
    22 =>
    array (
      'regex' => '; *BASE_Varia Build',
      'device_replacement' => 'Alcatel One Touch 918D',
      'brand_replacement' => 'Alcatel',
      'model_replacement' => 'One Touch 918D',
    ),
    23 =>
    array (
      'regex' => '; *((?:FINE|Fine)\\d[^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Allfine',
      'model_replacement' => '$1',
    ),
    24 =>
    array (
      'regex' => '; *(ALLVIEW[ _]?|Allview[ _]?)((?:Speed|SPEED).*) Build/',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'Allview',
      'model_replacement' => '$2',
    ),
    25 =>
    array (
      'regex' => '; *(ALLVIEW[ _]?|Allview[ _]?)?(AX1_Shine|AX2_Frenzy) Build',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'Allview',
      'model_replacement' => '$2',
    ),
    26 =>
    array (
      'regex' => '; *(ALLVIEW[ _]?|Allview[ _]?)([^;/]*) Build',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'Allview',
      'model_replacement' => '$2',
    ),
    27 =>
    array (
      'regex' => '; *(A13-MID) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Allwinner',
      'model_replacement' => '$1',
    ),
    28 =>
    array (
      'regex' => '; *(Allwinner)[ _\\-]?([^;/]+) Build',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Allwinner',
      'model_replacement' => '$1',
    ),
    29 =>
    array (
      'regex' => '; *(A651|A701B?|A702|A703|A705|A706|A707|A711|A712|A713|A717|A722|A785|A801|A802|A803|A901|A902|A1002|A1003|A1006|A1007|A9701|A9703|Q710|Q80) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Amaway',
      'model_replacement' => '$1',
    ),
    30 =>
    array (
      'regex' => '; *(?:AMOI|Amoi)[ _]([^;/]+) Build',
      'device_replacement' => 'Amoi $1',
      'brand_replacement' => 'Amoi',
      'model_replacement' => '$1',
    ),
    31 =>
    array (
      'regex' => '^(?:AMOI|Amoi)[ _]([^;/]+) Linux',
      'device_replacement' => 'Amoi $1',
      'brand_replacement' => 'Amoi',
      'model_replacement' => '$1',
    ),
    32 =>
    array (
      'regex' => '; *(MW(?:0[789]|10)[^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Aoc',
      'model_replacement' => '$1',
    ),
    33 =>
    array (
      'regex' => '; *(G7|M1013|M1015G|M11[CG]?|M-?12[B]?|M15|M19[G]?|M30[ACQ]?|M31[GQ]|M32|M33[GQ]|M36|M37|M38|M701T|M710|M712B|M713|M715G|M716G|M71(?:G|GS|T)?|M72[T]?|M73[T]?|M75[GT]?|M77G|M79T|M7L|M7LN|M81|M810|M81T|M82|M92|M92KS|M92S|M717G|M721|M722G|M723|M725G|M739|M785|M791|M92SK|M93D) Build',
      'device_replacement' => 'Aoson $1',
      'brand_replacement' => 'Aoson',
      'model_replacement' => '$1',
    ),
    34 =>
    array (
      'regex' => '; *Aoson ([^;/]+) Build',
      'regex_flag' => 'i',
      'device_replacement' => 'Aoson $1',
      'brand_replacement' => 'Aoson',
      'model_replacement' => '$1',
    ),
    35 =>
    array (
      'regex' => '; *[Aa]panda[ _\\-]([^;/]+) Build',
      'device_replacement' => 'Apanda $1',
      'brand_replacement' => 'Apanda',
      'model_replacement' => '$1',
    ),
    36 =>
    array (
      'regex' => '; *(?:ARCHOS|Archos) ?(GAMEPAD.*?)(?: Build|[;/\\(\\)\\-])',
      'device_replacement' => 'Archos $1',
      'brand_replacement' => 'Archos',
      'model_replacement' => '$1',
    ),
    37 =>
    array (
      'regex' => 'ARCHOS; GOGI; ([^;]+);',
      'device_replacement' => 'Archos $1',
      'brand_replacement' => 'Archos',
      'model_replacement' => '$1',
    ),
    38 =>
    array (
      'regex' => '(?:ARCHOS|Archos)[ _]?(.*?)(?: Build|[;/\\(\\)\\-]|$)',
      'device_replacement' => 'Archos $1',
      'brand_replacement' => 'Archos',
      'model_replacement' => '$1',
    ),
    39 =>
    array (
      'regex' => '; *(AN(?:7|8|9|10|13)[A-Z0-9]{1,4}) Build',
      'device_replacement' => 'Archos $1',
      'brand_replacement' => 'Archos',
      'model_replacement' => '$1',
    ),
    40 =>
    array (
      'regex' => '; *(A28|A32|A43|A70(?:BHT|CHT|HB|S|X)|A101(?:B|C|IT)|A7EB|A7EB-WK|101G9|80G9) Build',
      'device_replacement' => 'Archos $1',
      'brand_replacement' => 'Archos',
      'model_replacement' => '$1',
    ),
    41 =>
    array (
      'regex' => '; *(PAD-FMD[^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Arival',
      'model_replacement' => '$1',
    ),
    42 =>
    array (
      'regex' => '; *(BioniQ) ?([^;/]+) Build',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Arival',
      'model_replacement' => '$1 $2',
    ),
    43 =>
    array (
      'regex' => '; *(AN\\d[^;/]+|ARCHM\\d+) Build',
      'device_replacement' => 'Arnova $1',
      'brand_replacement' => 'Arnova',
      'model_replacement' => '$1',
    ),
    44 =>
    array (
      'regex' => '; *(?:ARNOVA|Arnova) ?([^;/]+) Build',
      'device_replacement' => 'Arnova $1',
      'brand_replacement' => 'Arnova',
      'model_replacement' => '$1',
    ),
    45 =>
    array (
      'regex' => '; *(?:ASSISTANT )?(AP)-?([1789]\\d{2}[A-Z]{0,2}|80104) Build',
      'device_replacement' => 'Assistant $1-$2',
      'brand_replacement' => 'Assistant',
      'model_replacement' => '$1-$2',
    ),
    46 =>
    array (
      'regex' => '; *(ME17\\d[^;/]*|ME3\\d{2}[^;/]+|K00[A-Z]|Nexus 10|Nexus 7(?: 2013)?|PadFone[^;/]*|Transformer[^;/]*|TF\\d{3}[^;/]*|eeepc) Build',
      'device_replacement' => 'Asus $1',
      'brand_replacement' => 'Asus',
      'model_replacement' => '$1',
    ),
    47 =>
    array (
      'regex' => '; *ASUS[ _]*([^;/]+) Build',
      'device_replacement' => 'Asus $1',
      'brand_replacement' => 'Asus',
      'model_replacement' => '$1',
    ),
    48 =>
    array (
      'regex' => '; *Garmin-Asus ([^;/]+) Build',
      'device_replacement' => 'Garmin-Asus $1',
      'brand_replacement' => 'Garmin-Asus',
      'model_replacement' => '$1',
    ),
    49 =>
    array (
      'regex' => '; *(Garminfone) Build',
      'device_replacement' => 'Garmin $1',
      'brand_replacement' => 'Garmin-Asus',
      'model_replacement' => '$1',
    ),
    50 =>
    array (
      'regex' => '; (\\@TAB-[^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Attab',
      'model_replacement' => '$1',
    ),
    51 =>
    array (
      'regex' => '; *(T-(?:07|[^0]\\d)[^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Audiosonic',
      'model_replacement' => '$1',
    ),
    52 =>
    array (
      'regex' => '; *(?:Axioo[ _\\-]([^;/]+)|(picopad)[ _\\-]([^;/]+)) Build',
      'regex_flag' => 'i',
      'device_replacement' => 'Axioo $1$2 $3',
      'brand_replacement' => 'Axioo',
      'model_replacement' => '$1$2 $3',
    ),
    53 =>
    array (
      'regex' => '; *(V(?:100|700|800)[^;/]*) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Azend',
      'model_replacement' => '$1',
    ),
    54 =>
    array (
      'regex' => '; *(IBAK\\-[^;/]*) Build',
      'regex_flag' => 'i',
      'device_replacement' => '$1',
      'brand_replacement' => 'Bak',
      'model_replacement' => '$1',
    ),
    55 =>
    array (
      'regex' => '; *(HY5001|HY6501|X12|X21|I5) Build',
      'device_replacement' => 'Bedove $1',
      'brand_replacement' => 'Bedove',
      'model_replacement' => '$1',
    ),
    56 =>
    array (
      'regex' => '; *(JC-[^;/]*) Build',
      'device_replacement' => 'Benss $1',
      'brand_replacement' => 'Benss',
      'model_replacement' => '$1',
    ),
    57 =>
    array (
      'regex' => '; *(BB) ([^;/]+) Build',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Blackberry',
      'model_replacement' => '$2',
    ),
    58 =>
    array (
      'regex' => '; *(BlackBird)[ _](I8.*) Build',
      'device_replacement' => '$1 $2',
      'brand_replacement' => '$1',
      'model_replacement' => '$2',
    ),
    59 =>
    array (
      'regex' => '; *(BlackBird)[ _](.*) Build',
      'device_replacement' => '$1 $2',
      'brand_replacement' => '$1',
      'model_replacement' => '$2',
    ),
    60 =>
    array (
      'regex' => '; *([0-9]+BP[EM][^;/]*|Endeavour[^;/]+) Build',
      'device_replacement' => 'Blaupunkt $1',
      'brand_replacement' => 'Blaupunkt',
      'model_replacement' => '$1',
    ),
    61 =>
    array (
      'regex' => '; *((?:BLU|Blu)[ _\\-])([^;/]+) Build',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'Blu',
      'model_replacement' => '$2',
    ),
    62 =>
    array (
      'regex' => '; *(?:BMOBILE )?(Blu|BLU|DASH [^;/]+|VIVO 4\\.3|TANK 4\\.5) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Blu',
      'model_replacement' => '$1',
    ),
    63 =>
    array (
      'regex' => '; *(TOUCH\\d[^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Blusens',
      'model_replacement' => '$1',
    ),
    64 =>
    array (
      'regex' => '; *(AX5\\d+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Bmobile',
      'model_replacement' => '$1',
    ),
    65 =>
    array (
      'regex' => '; *([Bb]q) ([^;/]+);? Build',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'bq',
      'model_replacement' => '$2',
    ),
    66 =>
    array (
      'regex' => '; *(Maxwell [^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'bq',
      'model_replacement' => '$1',
    ),
    67 =>
    array (
      'regex' => '; *((?:B-Tab|B-TAB) ?\\d[^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Braun',
      'model_replacement' => '$1',
    ),
    68 =>
    array (
      'regex' => '; *(Broncho) ([^;/]+) Build',
      'device_replacement' => '$1 $2',
      'brand_replacement' => '$1',
      'model_replacement' => '$2',
    ),
    69 =>
    array (
      'regex' => '; *CAPTIVA ([^;/]+) Build',
      'device_replacement' => 'Captiva $1',
      'brand_replacement' => 'Captiva',
      'model_replacement' => '$1',
    ),
    70 =>
    array (
      'regex' => '; *(C771|CAL21|IS11CA) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Casio',
      'model_replacement' => '$1',
    ),
    71 =>
    array (
      'regex' => '; *(?:Cat|CAT) ([^;/]+) Build',
      'device_replacement' => 'Cat $1',
      'brand_replacement' => 'Cat',
      'model_replacement' => '$1',
    ),
    72 =>
    array (
      'regex' => '; *(?:Cat)(Nova.*) Build',
      'device_replacement' => 'Cat $1',
      'brand_replacement' => 'Cat',
      'model_replacement' => '$1',
    ),
    73 =>
    array (
      'regex' => '; *(INM8002KP|ADM8000KP_[AB]) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Cat',
      'model_replacement' => 'Tablet PHOENIX 8.1J0',
    ),
    74 =>
    array (
      'regex' => '; *(?:[Cc]elkon[ _\\*]|CELKON[ _\\*])([^;/\\)]+) ?(?:Build|;|\\))',
      'device_replacement' => '$1',
      'brand_replacement' => 'Celkon',
      'model_replacement' => '$1',
    ),
    75 =>
    array (
      'regex' => 'Build/(?:[Cc]elkon)+_?([^;/_\\)]+)',
      'device_replacement' => '$1',
      'brand_replacement' => 'Celkon',
      'model_replacement' => '$1',
    ),
    76 =>
    array (
      'regex' => '; *(CT)-?(\\d+) Build',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'Celkon',
      'model_replacement' => '$1$2',
    ),
    77 =>
    array (
      'regex' => '; *(A19|A19Q|A105|A107[^;/\\)]*) ?(?:Build|;|\\))',
      'device_replacement' => '$1',
      'brand_replacement' => 'Celkon',
      'model_replacement' => '$1',
    ),
    78 =>
    array (
      'regex' => '; *(TPC[0-9]{4,5}) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'ChangJia',
      'model_replacement' => '$1',
    ),
    79 =>
    array (
      'regex' => '; *(Cloudfone)[ _](Excite)([^ ][^;/]+) Build',
      'device_replacement' => '$1 $2 $3',
      'brand_replacement' => 'Cloudfone',
      'model_replacement' => '$1 $2 $3',
    ),
    80 =>
    array (
      'regex' => '; *(Excite|ICE)[ _](\\d+[^;/]+) Build',
      'device_replacement' => 'Cloudfone $1 $2',
      'brand_replacement' => 'Cloudfone',
      'model_replacement' => 'Cloudfone $1 $2',
    ),
    81 =>
    array (
      'regex' => '; *(Cloudfone|CloudPad)[ _]([^;/]+) Build',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Cloudfone',
      'model_replacement' => '$1 $2',
    ),
    82 =>
    array (
      'regex' => '; *((?:Aquila|Clanga|Rapax)[^;/]+) Build',
      'regex_flag' => 'i',
      'device_replacement' => '$1',
      'brand_replacement' => 'Cmx',
      'model_replacement' => '$1',
    ),
    83 =>
    array (
      'regex' => '; *(?:CFW-|Kyros )?(MID[0-9]{4}(?:[ABC]|SR|TV)?)(\\(3G\\)-4G| GB 8K| 3G| 8K| GB)? *(?:Build|[;\\)])',
      'device_replacement' => 'CobyKyros $1$2',
      'brand_replacement' => 'CobyKyros',
      'model_replacement' => '$1$2',
    ),
    84 =>
    array (
      'regex' => '; *([^;/]*)Coolpad[ _]([^;/]+) Build',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'Coolpad',
      'model_replacement' => '$1$2',
    ),
    85 =>
    array (
      'regex' => '; *(CUBE[ _])?([KU][0-9]+ ?GT.*|A5300) Build',
      'regex_flag' => 'i',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'Cube',
      'model_replacement' => '$2',
    ),
    86 =>
    array (
      'regex' => '; *CUBOT ([^;/]+) Build',
      'regex_flag' => 'i',
      'device_replacement' => '$1',
      'brand_replacement' => 'Cubot',
      'model_replacement' => '$1',
    ),
    87 =>
    array (
      'regex' => '; *(BOBBY) Build',
      'regex_flag' => 'i',
      'device_replacement' => '$1',
      'brand_replacement' => 'Cubot',
      'model_replacement' => '$1',
    ),
    88 =>
    array (
      'regex' => '; *(Dslide [^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Danew',
      'model_replacement' => '$1',
    ),
    89 =>
    array (
      'regex' => '; *(XCD)[ _]?(28|35) Build',
      'device_replacement' => 'Dell $1$2',
      'brand_replacement' => 'Dell',
      'model_replacement' => '$1$2',
    ),
    90 =>
    array (
      'regex' => '; *(001DL) Build',
      'device_replacement' => 'Dell $1',
      'brand_replacement' => 'Dell',
      'model_replacement' => 'Streak',
    ),
    91 =>
    array (
      'regex' => '; *(?:Dell|DELL) (Streak) Build',
      'device_replacement' => 'Dell $1',
      'brand_replacement' => 'Dell',
      'model_replacement' => 'Streak',
    ),
    92 =>
    array (
      'regex' => '; *(101DL|GS01|Streak Pro[^;/]*) Build',
      'device_replacement' => 'Dell $1',
      'brand_replacement' => 'Dell',
      'model_replacement' => 'Streak Pro',
    ),
    93 =>
    array (
      'regex' => '; *([Ss]treak ?7) Build',
      'device_replacement' => 'Dell $1',
      'brand_replacement' => 'Dell',
      'model_replacement' => 'Streak 7',
    ),
    94 =>
    array (
      'regex' => '; *(Mini-3iX) Build',
      'device_replacement' => 'Dell $1',
      'brand_replacement' => 'Dell',
      'model_replacement' => '$1',
    ),
    95 =>
    array (
      'regex' => '; *(?:Dell|DELL)[ _](Aero|Venue|Thunder|Mini.*|Streak[ _]Pro) Build',
      'device_replacement' => 'Dell $1',
      'brand_replacement' => 'Dell',
      'model_replacement' => '$1',
    ),
    96 =>
    array (
      'regex' => '; *Dell[ _]([^;/]+) Build',
      'device_replacement' => 'Dell $1',
      'brand_replacement' => 'Dell',
      'model_replacement' => '$1',
    ),
    97 =>
    array (
      'regex' => '; *Dell ([^;/]+) Build',
      'device_replacement' => 'Dell $1',
      'brand_replacement' => 'Dell',
      'model_replacement' => '$1',
    ),
    98 =>
    array (
      'regex' => '; *(TA[CD]-\\d+[^;/]*) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Denver',
      'model_replacement' => '$1',
    ),
    99 =>
    array (
      'regex' => '; *(iP[789]\\d{2}(?:-3G)?|IP10\\d{2}(?:-8GB)?) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Dex',
      'model_replacement' => '$1',
    ),
    100 =>
    array (
      'regex' => '; *(AirTab)[ _\\-]([^;/]+) Build',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'DNS',
      'model_replacement' => '$1 $2',
    ),
    101 =>
    array (
      'regex' => '; *(F\\-\\d[^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Fujitsu',
      'model_replacement' => '$1',
    ),
    102 =>
    array (
      'regex' => '; *(HT-03A) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'HTC',
      'model_replacement' => 'Magic',
    ),
    103 =>
    array (
      'regex' => '; *(HT\\-\\d[^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'HTC',
      'model_replacement' => '$1',
    ),
    104 =>
    array (
      'regex' => '; *(L\\-\\d[^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'LG',
      'model_replacement' => '$1',
    ),
    105 =>
    array (
      'regex' => '; *(N\\-\\d[^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Nec',
      'model_replacement' => '$1',
    ),
    106 =>
    array (
      'regex' => '; *(P\\-\\d[^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Panasonic',
      'model_replacement' => '$1',
    ),
    107 =>
    array (
      'regex' => '; *(SC\\-\\d[^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Samsung',
      'model_replacement' => '$1',
    ),
    108 =>
    array (
      'regex' => '; *(SH\\-\\d[^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Sharp',
      'model_replacement' => '$1',
    ),
    109 =>
    array (
      'regex' => '; *(SO\\-\\d[^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'SonyEricsson',
      'model_replacement' => '$1',
    ),
    110 =>
    array (
      'regex' => '; *(T\\-0[12][^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Toshiba',
      'model_replacement' => '$1',
    ),
    111 =>
    array (
      'regex' => '; *(DOOV)[ _]([^;/]+) Build',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'DOOV',
      'model_replacement' => '$2',
    ),
    112 =>
    array (
      'regex' => '; *(Enot|ENOT)[ -]?([^;/]+) Build',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Enot',
      'model_replacement' => '$2',
    ),
    113 =>
    array (
      'regex' => '; *[^;/]+ Build/(?:CROSS|Cross)+[ _\\-]([^\\)]+)',
      'device_replacement' => 'CROSS $1',
      'brand_replacement' => 'Evercoss',
      'model_replacement' => 'Cross $1',
    ),
    114 =>
    array (
      'regex' => '; *(CROSS|Cross)[ _\\-]([^;/]+) Build',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Evercoss',
      'model_replacement' => 'Cross $2',
    ),
    115 =>
    array (
      'regex' => '; *Explay[_ ](.+?)(?:[\\)]| Build)',
      'device_replacement' => '$1',
      'brand_replacement' => 'Explay',
      'model_replacement' => '$1',
    ),
    116 =>
    array (
      'regex' => '; *(IQ.*) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Fly',
      'model_replacement' => '$1',
    ),
    117 =>
    array (
      'regex' => '; *(Fly|FLY)[ _](IQ[^;]+|F[34]\\d+[^;]*);? Build',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Fly',
      'model_replacement' => '$2',
    ),
    118 =>
    array (
      'regex' => '; *(M532|Q572|FJL21) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'Fujitsu',
      'model_replacement' => '$1',
    ),
    119 =>
    array (
      'regex' => '; *(G1) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Galapad',
      'model_replacement' => '$1',
    ),
    120 =>
    array (
      'regex' => '; *(Geeksphone) ([^;/]+) Build',
      'device_replacement' => '$1 $2',
      'brand_replacement' => '$1',
      'model_replacement' => '$2',
    ),
    121 =>
    array (
      'regex' => '; *(G[^F]?FIVE) ([^;/]+) Build',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Gfive',
      'model_replacement' => '$2',
    ),
    122 =>
    array (
      'regex' => '; *(Gionee)[ _\\-]([^;/]+)(?:/[^;/]+)? Build',
      'regex_flag' => 'i',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Gionee',
      'model_replacement' => '$2',
    ),
    123 =>
    array (
      'regex' => '; *(GN\\d+[A-Z]?|INFINITY_PASSION|Ctrl_V1) Build',
      'device_replacement' => 'Gionee $1',
      'brand_replacement' => 'Gionee',
      'model_replacement' => '$1',
    ),
    124 =>
    array (
      'regex' => '; *(E3) Build/JOP40D',
      'device_replacement' => 'Gionee $1',
      'brand_replacement' => 'Gionee',
      'model_replacement' => '$1',
    ),
    125 =>
    array (
      'regex' => '; *((?:FONE|QUANTUM|INSIGNIA) \\d+[^;/]*|PLAYTAB) Build',
      'device_replacement' => 'GoClever $1',
      'brand_replacement' => 'GoClever',
      'model_replacement' => '$1',
    ),
    126 =>
    array (
      'regex' => '; *GOCLEVER ([^;/]+) Build',
      'device_replacement' => 'GoClever $1',
      'brand_replacement' => 'GoClever',
      'model_replacement' => '$1',
    ),
    127 =>
    array (
      'regex' => '; *(Glass \\d+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Google',
      'model_replacement' => '$1',
    ),
    128 =>
    array (
      'regex' => '; *(GSmart)[ -]([^/]+) Build',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Gigabyte',
      'model_replacement' => '$1 $2',
    ),
    129 =>
    array (
      'regex' => '; *(imx5[13]_[^/]+) Build',
      'device_replacement' => 'Freescale $1',
      'brand_replacement' => 'Freescale',
      'model_replacement' => '$1',
    ),
    130 =>
    array (
      'regex' => '; *Haier[ _\\-]([^/]+) Build',
      'device_replacement' => 'Haier $1',
      'brand_replacement' => 'Haier',
      'model_replacement' => '$1',
    ),
    131 =>
    array (
      'regex' => '; *(PAD1016) Build',
      'device_replacement' => 'Haipad $1',
      'brand_replacement' => 'Haipad',
      'model_replacement' => '$1',
    ),
    132 =>
    array (
      'regex' => '; *(M701|M7|M8|M9) Build',
      'device_replacement' => 'Haipad $1',
      'brand_replacement' => 'Haipad',
      'model_replacement' => '$1',
    ),
    133 =>
    array (
      'regex' => '; *(SN\\d+T[^;\\)/]*)(?: Build|[;\\)])',
      'device_replacement' => 'Hannspree $1',
      'brand_replacement' => 'Hannspree',
      'model_replacement' => '$1',
    ),
    134 =>
    array (
      'regex' => 'Build/HCL ME Tablet ([^;\\)]+)[\\);]',
      'device_replacement' => 'HCLme $1',
      'brand_replacement' => 'HCLme',
      'model_replacement' => '$1',
    ),
    135 =>
    array (
      'regex' => '; *([^;\\/]+) Build/HCL',
      'device_replacement' => 'HCLme $1',
      'brand_replacement' => 'HCLme',
      'model_replacement' => '$1',
    ),
    136 =>
    array (
      'regex' => '; *(MID-?\\d{4}C[EM]) Build',
      'device_replacement' => 'Hena $1',
      'brand_replacement' => 'Hena',
      'model_replacement' => '$1',
    ),
    137 =>
    array (
      'regex' => '; *(EG\\d{2,}|HS-[^;/]+|MIRA[^;/]+) Build',
      'device_replacement' => 'Hisense $1',
      'brand_replacement' => 'Hisense',
      'model_replacement' => '$1',
    ),
    138 =>
    array (
      'regex' => '; *(andromax[^;/]+) Build',
      'regex_flag' => 'i',
      'device_replacement' => 'Hisense $1',
      'brand_replacement' => 'Hisense',
      'model_replacement' => '$1',
    ),
    139 =>
    array (
      'regex' => '; *(?:AMAZE[ _](S\\d+)|(S\\d+)[ _]AMAZE) Build',
      'device_replacement' => 'AMAZE $1$2',
      'brand_replacement' => 'hitech',
      'model_replacement' => 'AMAZE $1$2',
    ),
    140 =>
    array (
      'regex' => '; *(PlayBook) Build',
      'device_replacement' => 'HP $1',
      'brand_replacement' => 'HP',
      'model_replacement' => '$1',
    ),
    141 =>
    array (
      'regex' => '; *HP ([^/]+) Build',
      'device_replacement' => 'HP $1',
      'brand_replacement' => 'HP',
      'model_replacement' => '$1',
    ),
    142 =>
    array (
      'regex' => '; *([^/]+_tenderloin) Build',
      'device_replacement' => 'HP TouchPad',
      'brand_replacement' => 'HP',
      'model_replacement' => 'TouchPad',
    ),
    143 =>
    array (
      'regex' => '; *(HUAWEI |Huawei-)?([UY][^;/]+) Build/(?:Huawei|HUAWEI)([UY][^\\);]+)\\)',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'Huawei',
      'model_replacement' => '$2',
    ),
    144 =>
    array (
      'regex' => '; *([^;/]+) Build[/ ]Huawei(MT1-U06|[A-Z]+\\d+[^\\);]+)[^\\);]*\\)',
      'device_replacement' => '$1',
      'brand_replacement' => 'Huawei',
      'model_replacement' => '$2',
    ),
    145 =>
    array (
      'regex' => '; *(S7|M860) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Huawei',
      'model_replacement' => '$1',
    ),
    146 =>
    array (
      'regex' => '; *((?:HUAWEI|Huawei)[ \\-]?)(MediaPad) Build',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'Huawei',
      'model_replacement' => '$2',
    ),
    147 =>
    array (
      'regex' => '; *((?:HUAWEI[ _]?|Huawei[ _])?Ascend[ _])([^;/]+) Build',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'Huawei',
      'model_replacement' => '$2',
    ),
    148 =>
    array (
      'regex' => '; *((?:HUAWEI|Huawei)[ _\\-]?)((?:G700-|MT-)[^;/]+) Build',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'Huawei',
      'model_replacement' => '$2',
    ),
    149 =>
    array (
      'regex' => '; *((?:HUAWEI|Huawei)[ _\\-]?)([^;/]+) Build',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'Huawei',
      'model_replacement' => '$2',
    ),
    150 =>
    array (
      'regex' => '; *(MediaPad[^;]+|SpringBoard) Build/Huawei',
      'device_replacement' => '$1',
      'brand_replacement' => 'Huawei',
      'model_replacement' => '$1',
    ),
    151 =>
    array (
      'regex' => '; *([^;]+) Build/Huawei',
      'device_replacement' => '$1',
      'brand_replacement' => 'Huawei',
      'model_replacement' => '$1',
    ),
    152 =>
    array (
      'regex' => '; *([Uu])([89]\\d{3}) Build',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'Huawei',
      'model_replacement' => 'U$2',
    ),
    153 =>
    array (
      'regex' => '; *(?:Ideos |IDEOS )(S7) Build',
      'device_replacement' => 'Huawei Ideos$1',
      'brand_replacement' => 'Huawei',
      'model_replacement' => 'Ideos$1',
    ),
    154 =>
    array (
      'regex' => '; *(?:Ideos |IDEOS )([^;/]+\\s*|\\s*)Build',
      'device_replacement' => 'Huawei Ideos$1',
      'brand_replacement' => 'Huawei',
      'model_replacement' => 'Ideos$1',
    ),
    155 =>
    array (
      'regex' => '; *(Orange Daytona|Pulse|Pulse Mini|Vodafone 858|C8500|C8600|C8650|C8660|Nexus 6P|ATH-.+?) Build[/ ]',
      'device_replacement' => 'Huawei $1',
      'brand_replacement' => 'Huawei',
      'model_replacement' => '$1',
    ),
    156 =>
    array (
      'regex' => '; *HTC[ _]([^;]+); Windows Phone',
      'device_replacement' => 'HTC $1',
      'brand_replacement' => 'HTC',
      'model_replacement' => '$1',
    ),
    157 =>
    array (
      'regex' => '; *(?:HTC[ _/])+([^ _/]+)(?:[/\\\\]1\\.0 | V|/| +)\\d+\\.\\d[\\d\\.]*(?: *Build|\\))',
      'device_replacement' => 'HTC $1',
      'brand_replacement' => 'HTC',
      'model_replacement' => '$1',
    ),
    158 =>
    array (
      'regex' => '; *(?:HTC[ _/])+([^ _/]+)(?:[ _/]([^ _/]+))?(?:[/\\\\]1\\.0 | V|/| +)\\d+\\.\\d[\\d\\.]*(?: *Build|\\))',
      'device_replacement' => 'HTC $1 $2',
      'brand_replacement' => 'HTC',
      'model_replacement' => '$1 $2',
    ),
    159 =>
    array (
      'regex' => '; *(?:HTC[ _/])+([^ _/]+)(?:[ _/]([^ _/]+)(?:[ _/]([^ _/]+))?)?(?:[/\\\\]1\\.0 | V|/| +)\\d+\\.\\d[\\d\\.]*(?: *Build|\\))',
      'device_replacement' => 'HTC $1 $2 $3',
      'brand_replacement' => 'HTC',
      'model_replacement' => '$1 $2 $3',
    ),
    160 =>
    array (
      'regex' => '; *(?:HTC[ _/])+([^ _/]+)(?:[ _/]([^ _/]+)(?:[ _/]([^ _/]+)(?:[ _/]([^ _/]+))?)?)?(?:[/\\\\]1\\.0 | V|/| +)\\d+\\.\\d[\\d\\.]*(?: *Build|\\))',
      'device_replacement' => 'HTC $1 $2 $3 $4',
      'brand_replacement' => 'HTC',
      'model_replacement' => '$1 $2 $3 $4',
    ),
    161 =>
    array (
      'regex' => '; *(?:(?:HTC|htc)(?:_blocked)*[ _/])+([^ _/;]+)(?: *Build|[;\\)]| - )',
      'device_replacement' => 'HTC $1',
      'brand_replacement' => 'HTC',
      'model_replacement' => '$1',
    ),
    162 =>
    array (
      'regex' => '; *(?:(?:HTC|htc)(?:_blocked)*[ _/])+([^ _/]+)(?:[ _/]([^ _/;\\)]+))?(?: *Build|[;\\)]| - )',
      'device_replacement' => 'HTC $1 $2',
      'brand_replacement' => 'HTC',
      'model_replacement' => '$1 $2',
    ),
    163 =>
    array (
      'regex' => '; *(?:(?:HTC|htc)(?:_blocked)*[ _/])+([^ _/]+)(?:[ _/]([^ _/]+)(?:[ _/]([^ _/;\\)]+))?)?(?: *Build|[;\\)]| - )',
      'device_replacement' => 'HTC $1 $2 $3',
      'brand_replacement' => 'HTC',
      'model_replacement' => '$1 $2 $3',
    ),
    164 =>
    array (
      'regex' => '; *(?:(?:HTC|htc)(?:_blocked)*[ _/])+([^ _/]+)(?:[ _/]([^ _/]+)(?:[ _/]([^ _/]+)(?:[ _/]([^ /;]+))?)?)?(?: *Build|[;\\)]| - )',
      'device_replacement' => 'HTC $1 $2 $3 $4',
      'brand_replacement' => 'HTC',
      'model_replacement' => '$1 $2 $3 $4',
    ),
    165 =>
    array (
      'regex' => 'HTC Streaming Player [^\\/]*/[^\\/]*/ htc_([^/]+) /',
      'device_replacement' => 'HTC $1',
      'brand_replacement' => 'HTC',
      'model_replacement' => '$1',
    ),
    166 =>
    array (
      'regex' => '(?:[;,] *|^)(?:htccn_chs-)?HTC[ _-]?([^;]+?)(?: *Build|clay|Android|-?Mozilla| Opera| Profile| UNTRUSTED|[;/\\(\\)]|$)',
      'regex_flag' => 'i',
      'device_replacement' => 'HTC $1',
      'brand_replacement' => 'HTC',
      'model_replacement' => '$1',
    ),
    167 =>
    array (
      'regex' => '; *(A6277|ADR6200|ADR6300|ADR6350|ADR6400[A-Z]*|ADR6425[A-Z]*|APX515CKT|ARIA|Desire[^_ ]*|Dream|EndeavorU|Eris|Evo|Flyer|HD2|Hero|HERO200|Hero CDMA|HTL21|Incredible|Inspire[A-Z0-9]*|Legend|Liberty|Nexus ?(?:One|HD2)|One|One S C2|One[ _]?(?:S|V|X\\+?)\\w*|PC36100|PG06100|PG86100|S31HT|Sensation|Wildfire)(?: Build|[/;\\(\\)])',
      'regex_flag' => 'i',
      'device_replacement' => 'HTC $1',
      'brand_replacement' => 'HTC',
      'model_replacement' => '$1',
    ),
    168 =>
    array (
      'regex' => '; *(ADR6200|ADR6400L|ADR6425LVW|Amaze|DesireS?|EndeavorU|Eris|EVO|Evo\\d[A-Z]+|HD2|IncredibleS?|Inspire[A-Z0-9]*|Inspire[A-Z0-9]*|Sensation[A-Z0-9]*|Wildfire)[ _-](.+?)(?:[/;\\)]|Build|MIUI|1\\.0)',
      'regex_flag' => 'i',
      'device_replacement' => 'HTC $1 $2',
      'brand_replacement' => 'HTC',
      'model_replacement' => '$1 $2',
    ),
    169 =>
    array (
      'regex' => '; *HYUNDAI (T\\d[^/]*) Build',
      'device_replacement' => 'Hyundai $1',
      'brand_replacement' => 'Hyundai',
      'model_replacement' => '$1',
    ),
    170 =>
    array (
      'regex' => '; *HYUNDAI ([^;/]+) Build',
      'device_replacement' => 'Hyundai $1',
      'brand_replacement' => 'Hyundai',
      'model_replacement' => '$1',
    ),
    171 =>
    array (
      'regex' => '; *(X700|Hold X|MB-6900) Build',
      'device_replacement' => 'Hyundai $1',
      'brand_replacement' => 'Hyundai',
      'model_replacement' => '$1',
    ),
    172 =>
    array (
      'regex' => '; *(?:iBall[ _\\-])?(Andi)[ _]?(\\d[^;/]*) Build',
      'regex_flag' => 'i',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'iBall',
      'model_replacement' => '$1 $2',
    ),
    173 =>
    array (
      'regex' => '; *(IBall)(?:[ _]([^;/]+)|) Build',
      'regex_flag' => 'i',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'iBall',
      'model_replacement' => '$2',
    ),
    174 =>
    array (
      'regex' => '; *(NT-\\d+[^ ;/]*|Net[Tt]AB [^;/]+|Mercury [A-Z]+|iconBIT)(?: S/N:[^;/]+)? Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'IconBIT',
      'model_replacement' => '$1',
    ),
    175 =>
    array (
      'regex' => '; *(IMO)[ _]([^;/]+) Build',
      'regex_flag' => 'i',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'IMO',
      'model_replacement' => '$2',
    ),
    176 =>
    array (
      'regex' => '; *i-?mobile[ _]([^/]+) Build/',
      'regex_flag' => 'i',
      'device_replacement' => 'i-mobile $1',
      'brand_replacement' => 'imobile',
      'model_replacement' => '$1',
    ),
    177 =>
    array (
      'regex' => '; *(i-(?:style|note)[^/]*) Build/',
      'regex_flag' => 'i',
      'device_replacement' => 'i-mobile $1',
      'brand_replacement' => 'imobile',
      'model_replacement' => '$1',
    ),
    178 =>
    array (
      'regex' => '; *(ImPAD) ?(\\d+(?:.)*) Build',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Impression',
      'model_replacement' => '$1 $2',
    ),
    179 =>
    array (
      'regex' => '; *(Infinix)[ _]([^;/]+) Build',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Infinix',
      'model_replacement' => '$2',
    ),
    180 =>
    array (
      'regex' => '; *(Informer)[ \\-]([^;/]+) Build',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Informer',
      'model_replacement' => '$2',
    ),
    181 =>
    array (
      'regex' => '; *(TAB) ?([78][12]4) Build',
      'device_replacement' => 'Intenso $1',
      'brand_replacement' => 'Intenso',
      'model_replacement' => '$1 $2',
    ),
    182 =>
    array (
      'regex' => '; *(?:Intex[ _])?(AQUA|Aqua)([ _\\.\\-])([^;/]+) *(?:Build|;)',
      'device_replacement' => '$1$2$3',
      'brand_replacement' => 'Intex',
      'model_replacement' => '$1 $3',
    ),
    183 =>
    array (
      'regex' => '; *(?:INTEX|Intex)(?:[_ ]([^\\ _;/]+))(?:[_ ]([^\\ _;/]+))? *(?:Build|;)',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Intex',
      'model_replacement' => '$1 $2',
    ),
    184 =>
    array (
      'regex' => '; *([iI]Buddy)[ _]?(Connect)(?:_|\\?_| )?([^;/]*) *(?:Build|;)',
      'device_replacement' => '$1 $2 $3',
      'brand_replacement' => 'Intex',
      'model_replacement' => 'iBuddy $2 $3',
    ),
    185 =>
    array (
      'regex' => '; *(I-Buddy)[ _]([^;/]+) *(?:Build|;)',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Intex',
      'model_replacement' => 'iBuddy $2',
    ),
    186 =>
    array (
      'regex' => '; *(iOCEAN) ([^/]+) Build',
      'regex_flag' => 'i',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'iOCEAN',
      'model_replacement' => '$2',
    ),
    187 =>
    array (
      'regex' => '; *(TP\\d+(?:\\.\\d+)?\\-\\d[^;/]+) Build',
      'device_replacement' => 'ionik $1',
      'brand_replacement' => 'ionik',
      'model_replacement' => '$1',
    ),
    188 =>
    array (
      'regex' => '; *(M702pro) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Iru',
      'model_replacement' => '$1',
    ),
    189 =>
    array (
      'regex' => '; *(DE88Plus|MD70) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Ivio',
      'model_replacement' => '$1',
    ),
    190 =>
    array (
      'regex' => '; *IVIO[_\\-]([^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Ivio',
      'model_replacement' => '$1',
    ),
    191 =>
    array (
      'regex' => '; *(TPC-\\d+|JAY-TECH) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Jaytech',
      'model_replacement' => '$1',
    ),
    192 =>
    array (
      'regex' => '; *(JY-[^;/]+|G[234]S?) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Jiayu',
      'model_replacement' => '$1',
    ),
    193 =>
    array (
      'regex' => '; *(JXD)[ _\\-]([^;/]+) Build',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'JXD',
      'model_replacement' => '$2',
    ),
    194 =>
    array (
      'regex' => '; *Karbonn[ _]?([^;/]+) *(?:Build|;)',
      'regex_flag' => 'i',
      'device_replacement' => '$1',
      'brand_replacement' => 'Karbonn',
      'model_replacement' => '$1',
    ),
    195 =>
    array (
      'regex' => '; *([^;]+) Build/Karbonn',
      'device_replacement' => '$1',
      'brand_replacement' => 'Karbonn',
      'model_replacement' => '$1',
    ),
    196 =>
    array (
      'regex' => '; *(A11|A39|A37|A34|ST8|ST10|ST7|Smart Tab3|Smart Tab2|Titanium S\\d) +Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Karbonn',
      'model_replacement' => '$1',
    ),
    197 =>
    array (
      'regex' => '; *(IS01|IS03|IS05|IS\\d{2}SH) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Sharp',
      'model_replacement' => '$1',
    ),
    198 =>
    array (
      'regex' => '; *(IS04) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Regza',
      'model_replacement' => '$1',
    ),
    199 =>
    array (
      'regex' => '; *(IS06|IS\\d{2}PT) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Pantech',
      'model_replacement' => '$1',
    ),
    200 =>
    array (
      'regex' => '; *(IS11S) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'SonyEricsson',
      'model_replacement' => 'Xperia Acro',
    ),
    201 =>
    array (
      'regex' => '; *(IS11CA) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Casio',
      'model_replacement' => 'GzOne $1',
    ),
    202 =>
    array (
      'regex' => '; *(IS11LG) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'LG',
      'model_replacement' => 'Optimus X',
    ),
    203 =>
    array (
      'regex' => '; *(IS11N) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Medias',
      'model_replacement' => '$1',
    ),
    204 =>
    array (
      'regex' => '; *(IS11PT) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Pantech',
      'model_replacement' => 'MIRACH',
    ),
    205 =>
    array (
      'regex' => '; *(IS12F) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Fujitsu',
      'model_replacement' => 'Arrows ES',
    ),
    206 =>
    array (
      'regex' => '; *(IS12M) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Motorola',
      'model_replacement' => 'XT909',
    ),
    207 =>
    array (
      'regex' => '; *(IS12S) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'SonyEricsson',
      'model_replacement' => 'Xperia Acro HD',
    ),
    208 =>
    array (
      'regex' => '; *(ISW11F) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Fujitsu',
      'model_replacement' => 'Arrowz Z',
    ),
    209 =>
    array (
      'regex' => '; *(ISW11HT) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'HTC',
      'model_replacement' => 'EVO',
    ),
    210 =>
    array (
      'regex' => '; *(ISW11K) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Kyocera',
      'model_replacement' => 'DIGNO',
    ),
    211 =>
    array (
      'regex' => '; *(ISW11M) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Motorola',
      'model_replacement' => 'Photon',
    ),
    212 =>
    array (
      'regex' => '; *(ISW11SC) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Samsung',
      'model_replacement' => 'GALAXY S II WiMAX',
    ),
    213 =>
    array (
      'regex' => '; *(ISW12HT) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'HTC',
      'model_replacement' => 'EVO 3D',
    ),
    214 =>
    array (
      'regex' => '; *(ISW13HT) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'HTC',
      'model_replacement' => 'J',
    ),
    215 =>
    array (
      'regex' => '; *(ISW?[0-9]{2}[A-Z]{0,2}) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'KDDI',
      'model_replacement' => '$1',
    ),
    216 =>
    array (
      'regex' => '; *(INFOBAR [^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'KDDI',
      'model_replacement' => '$1',
    ),
    217 =>
    array (
      'regex' => '; *(JOYPAD|Joypad)[ _]([^;/]+) Build/',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Kingcom',
      'model_replacement' => '$1 $2',
    ),
    218 =>
    array (
      'regex' => '; *(Vox|VOX|Arc|K080) Build/',
      'regex_flag' => 'i',
      'device_replacement' => '$1',
      'brand_replacement' => 'Kobo',
      'model_replacement' => '$1',
    ),
    219 =>
    array (
      'regex' => '\\b(Kobo Touch)\\b',
      'device_replacement' => '$1',
      'brand_replacement' => 'Kobo',
      'model_replacement' => '$1',
    ),
    220 =>
    array (
      'regex' => '; *(K-Touch)[ _]([^;/]+) Build',
      'regex_flag' => 'i',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Ktouch',
      'model_replacement' => '$2',
    ),
    221 =>
    array (
      'regex' => '; *((?:EV|KM)-S\\d+[A-Z]?) Build',
      'regex_flag' => 'i',
      'device_replacement' => '$1',
      'brand_replacement' => 'KTtech',
      'model_replacement' => '$1',
    ),
    222 =>
    array (
      'regex' => '; *(Zio|Hydro|Torque|Event|EVENT|Echo|Milano|Rise|URBANO PROGRESSO|WX04K|WX06K|WX10K|KYL21|101K|C5[12]\\d{2}) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'Kyocera',
      'model_replacement' => '$1',
    ),
    223 =>
    array (
      'regex' => '; *(?:LAVA[ _])?IRIS[ _\\-]?([^/;\\)]+) *(?:;|\\)|Build)',
      'regex_flag' => 'i',
      'device_replacement' => 'Iris $1',
      'brand_replacement' => 'Lava',
      'model_replacement' => 'Iris $1',
    ),
    224 =>
    array (
      'regex' => '; *LAVA[ _]([^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Lava',
      'model_replacement' => '$1',
    ),
    225 =>
    array (
      'regex' => '; *(?:(Aspire A1)|(?:LEMON|Lemon)[ _]([^;/]+))_? Build',
      'device_replacement' => 'Lemon $1$2',
      'brand_replacement' => 'Lemon',
      'model_replacement' => '$1$2',
    ),
    226 =>
    array (
      'regex' => '; *(TAB-1012) Build/',
      'device_replacement' => 'Lenco $1',
      'brand_replacement' => 'Lenco',
      'model_replacement' => '$1',
    ),
    227 =>
    array (
      'regex' => '; Lenco ([^;/]+) Build/',
      'device_replacement' => 'Lenco $1',
      'brand_replacement' => 'Lenco',
      'model_replacement' => '$1',
    ),
    228 =>
    array (
      'regex' => '; *(A1_07|A2107A-H|S2005A-H|S1-37AH0) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Lenovo',
      'model_replacement' => '$1',
    ),
    229 =>
    array (
      'regex' => '; *(Idea[Tp]ab)[ _]([^;/]+);? Build',
      'device_replacement' => 'Lenovo $1 $2',
      'brand_replacement' => 'Lenovo',
      'model_replacement' => '$1 $2',
    ),
    230 =>
    array (
      'regex' => '; *(Idea(?:Tab|pad)) ?([^;/]+) Build',
      'device_replacement' => 'Lenovo $1 $2',
      'brand_replacement' => 'Lenovo',
      'model_replacement' => '$1 $2',
    ),
    231 =>
    array (
      'regex' => '; *(ThinkPad) ?(Tablet) Build/',
      'device_replacement' => 'Lenovo $1 $2',
      'brand_replacement' => 'Lenovo',
      'model_replacement' => '$1 $2',
    ),
    232 =>
    array (
      'regex' => '; *(?:LNV-)?(?:=?[Ll]enovo[ _\\-]?|LENOVO[ _])+(.+?)(?:Build|[;/\\)])',
      'device_replacement' => 'Lenovo $1',
      'brand_replacement' => 'Lenovo',
      'model_replacement' => '$1',
    ),
    233 =>
    array (
      'regex' => '[;,] (?:Vodafone )?(SmartTab) ?(II) ?(\\d+) Build/',
      'device_replacement' => 'Lenovo $1 $2 $3',
      'brand_replacement' => 'Lenovo',
      'model_replacement' => '$1 $2 $3',
    ),
    234 =>
    array (
      'regex' => '; *(?:Ideapad )?K1 Build/',
      'device_replacement' => 'Lenovo Ideapad K1',
      'brand_replacement' => 'Lenovo',
      'model_replacement' => 'Ideapad K1',
    ),
    235 =>
    array (
      'regex' => '; *(3GC101|3GW10[01]|A390) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'Lenovo',
      'model_replacement' => '$1',
    ),
    236 =>
    array (
      'regex' => '\\b(?:Lenovo|LENOVO)+[ _\\-]?([^,;:/ ]+)',
      'device_replacement' => 'Lenovo $1',
      'brand_replacement' => 'Lenovo',
      'model_replacement' => '$1',
    ),
    237 =>
    array (
      'regex' => '; *(MFC\\d+)[A-Z]{2}([^;,/]*),? Build',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'Lexibook',
      'model_replacement' => '$1$2',
    ),
    238 =>
    array (
      'regex' => '; *(E[34][0-9]{2}|LS[6-8][0-9]{2}|VS[6-9][0-9]+[^;/]+|Nexus 4|Nexus 5X?|GT540f?|Optimus (?:2X|G|4X HD)|OptimusX4HD) *(?:Build|;)',
      'device_replacement' => '$1',
      'brand_replacement' => 'LG',
      'model_replacement' => '$1',
    ),
    239 =>
    array (
      'regex' => '[;:] *(L-\\d+[A-Z]|LGL\\d+[A-Z]?)(?:/V\\d+)? *(?:Build|[;\\)])',
      'device_replacement' => '$1',
      'brand_replacement' => 'LG',
      'model_replacement' => '$1',
    ),
    240 =>
    array (
      'regex' => '; *(LG-)([A-Z]{1,2}\\d{2,}[^,;/\\)\\(]*?)(?:Build| V\\d+|[,;/\\)\\(]|$)',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'LG',
      'model_replacement' => '$2',
    ),
    241 =>
    array (
      'regex' => '; *(LG[ \\-]|LG)([^;/]+)[;/]? Build',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'LG',
      'model_replacement' => '$2',
    ),
    242 =>
    array (
      'regex' => '^(LG)-([^;/]+)/ Mozilla/.*; Android',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'LG',
      'model_replacement' => '$2',
    ),
    243 =>
    array (
      'regex' => '(Web0S); Linux/(SmartTV)',
      'device_replacement' => 'LG $1 $2',
      'brand_replacement' => 'LG',
      'model_replacement' => '$1 $2',
    ),
    244 =>
    array (
      'regex' => '; *((?:SMB|smb)[^;/]+) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'Malata',
      'model_replacement' => '$1',
    ),
    245 =>
    array (
      'regex' => '; *(?:Malata|MALATA) ([^;/]+) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'Malata',
      'model_replacement' => '$1',
    ),
    246 =>
    array (
      'regex' => '; *(MS[45][0-9]{3}|MID0[568][NS]?|MID[1-9]|MID[78]0[1-9]|MID970[1-9]|MID100[1-9]) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'Manta',
      'model_replacement' => '$1',
    ),
    247 =>
    array (
      'regex' => '; *(M1052|M806|M9000|M9100|M9701|MID100|MID120|MID125|MID130|MID135|MID140|MID701|MID710|MID713|MID727|MID728|MID731|MID732|MID733|MID735|MID736|MID737|MID760|MID800|MID810|MID820|MID830|MID833|MID835|MID860|MID900|MID930|MID933|MID960|MID980) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'Match',
      'model_replacement' => '$1',
    ),
    248 =>
    array (
      'regex' => '; *(GenxDroid7|MSD7.*|AX\\d.*|Tab 701|Tab 722) Build/',
      'device_replacement' => 'Maxx $1',
      'brand_replacement' => 'Maxx',
      'model_replacement' => '$1',
    ),
    249 =>
    array (
      'regex' => '; *(M-PP[^;/]+|PhonePad ?\\d{2,}[^;/]+) Build',
      'device_replacement' => 'Mediacom $1',
      'brand_replacement' => 'Mediacom',
      'model_replacement' => '$1',
    ),
    250 =>
    array (
      'regex' => '; *(M-MP[^;/]+|SmartPad ?\\d{2,}[^;/]+) Build',
      'device_replacement' => 'Mediacom $1',
      'brand_replacement' => 'Mediacom',
      'model_replacement' => '$1',
    ),
    251 =>
    array (
      'regex' => '; *(?:MD_)?LIFETAB[ _]([^;/]+) Build',
      'regex_flag' => 'i',
      'device_replacement' => 'Medion Lifetab $1',
      'brand_replacement' => 'Medion',
      'model_replacement' => 'Lifetab $1',
    ),
    252 =>
    array (
      'regex' => '; *MEDION ([^;/]+) Build',
      'device_replacement' => 'Medion $1',
      'brand_replacement' => 'Medion',
      'model_replacement' => '$1',
    ),
    253 =>
    array (
      'regex' => '; *(M030|M031|M035|M040|M065|m9) Build',
      'device_replacement' => 'Meizu $1',
      'brand_replacement' => 'Meizu',
      'model_replacement' => '$1',
    ),
    254 =>
    array (
      'regex' => '; *(?:meizu_|MEIZU )(.+?) *(?:Build|[;\\)])',
      'device_replacement' => 'Meizu $1',
      'brand_replacement' => 'Meizu',
      'model_replacement' => '$1',
    ),
    255 =>
    array (
      'regex' => '; *(?:Micromax[ _](A111|A240)|(A111|A240)) Build',
      'regex_flag' => 'i',
      'device_replacement' => 'Micromax $1$2',
      'brand_replacement' => 'Micromax',
      'model_replacement' => '$1$2',
    ),
    256 =>
    array (
      'regex' => '; *Micromax[ _](A\\d{2,3}[^;/]*) Build',
      'regex_flag' => 'i',
      'device_replacement' => 'Micromax $1',
      'brand_replacement' => 'Micromax',
      'model_replacement' => '$1',
    ),
    257 =>
    array (
      'regex' => '; *(A\\d{2}|A[12]\\d{2}|A90S|A110Q) Build',
      'regex_flag' => 'i',
      'device_replacement' => 'Micromax $1',
      'brand_replacement' => 'Micromax',
      'model_replacement' => '$1',
    ),
    258 =>
    array (
      'regex' => '; *Micromax[ _](P\\d{3}[^;/]*) Build',
      'regex_flag' => 'i',
      'device_replacement' => 'Micromax $1',
      'brand_replacement' => 'Micromax',
      'model_replacement' => '$1',
    ),
    259 =>
    array (
      'regex' => '; *(P\\d{3}|P\\d{3}\\(Funbook\\)) Build',
      'regex_flag' => 'i',
      'device_replacement' => 'Micromax $1',
      'brand_replacement' => 'Micromax',
      'model_replacement' => '$1',
    ),
    260 =>
    array (
      'regex' => '; *(MITO)[ _\\-]?([^;/]+) Build',
      'regex_flag' => 'i',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Mito',
      'model_replacement' => '$2',
    ),
    261 =>
    array (
      'regex' => '; *(Cynus)[ _](F5|T\\d|.+?) *(?:Build|[;/\\)])',
      'regex_flag' => 'i',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Mobistel',
      'model_replacement' => '$1 $2',
    ),
    262 =>
    array (
      'regex' => '; *(MODECOM )?(FreeTab) ?([^;/]+) Build',
      'regex_flag' => 'i',
      'device_replacement' => '$1$2 $3',
      'brand_replacement' => 'Modecom',
      'model_replacement' => '$2 $3',
    ),
    263 =>
    array (
      'regex' => '; *(MODECOM )([^;/]+) Build',
      'regex_flag' => 'i',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Modecom',
      'model_replacement' => '$2',
    ),
    264 =>
    array (
      'regex' => '; *(MZ\\d{3}\\+?|MZ\\d{3} 4G|Xoom|XOOM[^;/]*) Build',
      'device_replacement' => 'Motorola $1',
      'brand_replacement' => 'Motorola',
      'model_replacement' => '$1',
    ),
    265 =>
    array (
      'regex' => '; *(Milestone )(XT[^;/]*) Build',
      'device_replacement' => 'Motorola $1$2',
      'brand_replacement' => 'Motorola',
      'model_replacement' => '$2',
    ),
    266 =>
    array (
      'regex' => '; *(Motoroi ?x|Droid X|DROIDX) Build',
      'regex_flag' => 'i',
      'device_replacement' => 'Motorola $1',
      'brand_replacement' => 'Motorola',
      'model_replacement' => 'DROID X',
    ),
    267 =>
    array (
      'regex' => '; *(Droid[^;/]*|DROID[^;/]*|Milestone[^;/]*|Photon|Triumph|Devour|Titanium) Build',
      'device_replacement' => 'Motorola $1',
      'brand_replacement' => 'Motorola',
      'model_replacement' => '$1',
    ),
    268 =>
    array (
      'regex' => '; *(A555|A85[34][^;/]*|A95[356]|ME[58]\\d{2}\\+?|ME600|ME632|ME722|MB\\d{3}\\+?|MT680|MT710|MT870|MT887|MT917|WX435|WX453|WX44[25]|XT\\d{3,4}[A-Z\\+]*|CL[iI]Q|CL[iI]Q XT) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Motorola',
      'model_replacement' => '$1',
    ),
    269 =>
    array (
      'regex' => '; *(Motorola MOT-|Motorola[ _\\-]|MOT\\-?)([^;/]+) Build',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'Motorola',
      'model_replacement' => '$2',
    ),
    270 =>
    array (
      'regex' => '; *(Moto[_ ]?|MOT\\-)([^;/]+) Build',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'Motorola',
      'model_replacement' => '$2',
    ),
    271 =>
    array (
      'regex' => '; *((?:MP[DQ]C|MPG\\d{1,4}|MP\\d{3,4}|MID(?:(?:10[234]|114|43|7[247]|8[24]|7)C|8[01]1))[^;/]*) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Mpman',
      'model_replacement' => '$1',
    ),
    272 =>
    array (
      'regex' => '; *(?:MSI[ _])?(Primo\\d+|Enjoy[ _\\-][^;/]+) Build',
      'regex_flag' => 'i',
      'device_replacement' => '$1',
      'brand_replacement' => 'Msi',
      'model_replacement' => '$1',
    ),
    273 =>
    array (
      'regex' => '; *Multilaser[ _]([^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Multilaser',
      'model_replacement' => '$1',
    ),
    274 =>
    array (
      'regex' => '; *(My)[_]?(Pad)[ _]([^;/]+) Build',
      'device_replacement' => '$1$2 $3',
      'brand_replacement' => 'MyPhone',
      'model_replacement' => '$1$2 $3',
    ),
    275 =>
    array (
      'regex' => '; *(My)\\|?(Phone)[ _]([^;/]+) Build',
      'device_replacement' => '$1$2 $3',
      'brand_replacement' => 'MyPhone',
      'model_replacement' => '$3',
    ),
    276 =>
    array (
      'regex' => '; *(A\\d+)[ _](Duo)? Build',
      'regex_flag' => 'i',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'MyPhone',
      'model_replacement' => '$1 $2',
    ),
    277 =>
    array (
      'regex' => '; *(myTab[^;/]*) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Mytab',
      'model_replacement' => '$1',
    ),
    278 =>
    array (
      'regex' => '; *(NABI2?-)([^;/]+) Build/',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'Nabi',
      'model_replacement' => '$2',
    ),
    279 =>
    array (
      'regex' => '; *(N-\\d+[CDE]) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'Nec',
      'model_replacement' => '$1',
    ),
    280 =>
    array (
      'regex' => '; ?(NEC-)(.*) Build/',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'Nec',
      'model_replacement' => '$2',
    ),
    281 =>
    array (
      'regex' => '; *(LT-NA7) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'Nec',
      'model_replacement' => 'Lifetouch Note',
    ),
    282 =>
    array (
      'regex' => '; *(NXM\\d+[A-z0-9_]*|Next\\d[A-z0-9_ \\-]*|NEXT\\d[A-z0-9_ \\-]*|Nextbook [A-z0-9_ ]*|DATAM803HC|M805)(?: Build|[\\);])',
      'device_replacement' => '$1',
      'brand_replacement' => 'Nextbook',
      'model_replacement' => '$1',
    ),
    283 =>
    array (
      'regex' => '; *(Nokia)([ _\\-]*)([^;/]*) Build',
      'regex_flag' => 'i',
      'device_replacement' => '$1$2$3',
      'brand_replacement' => 'Nokia',
      'model_replacement' => '$3',
    ),
    284 =>
    array (
      'regex' => '; *(Nook ?|Barnes & Noble Nook |BN )([^;/]+) Build',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'Nook',
      'model_replacement' => '$2',
    ),
    285 =>
    array (
      'regex' => '; *(NOOK )?(BNRV200|BNRV200A|BNTV250|BNTV250A|BNTV400|BNTV600|LogicPD Zoom2) Build',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'Nook',
      'model_replacement' => '$2',
    ),
    286 =>
    array (
      'regex' => '; Build/(Nook)',
      'device_replacement' => '$1',
      'brand_replacement' => 'Nook',
      'model_replacement' => 'Tablet',
    ),
    287 =>
    array (
      'regex' => '; *(OP110|OliPad[^;/]+) Build',
      'device_replacement' => 'Olivetti $1',
      'brand_replacement' => 'Olivetti',
      'model_replacement' => '$1',
    ),
    288 =>
    array (
      'regex' => '; *OMEGA[ _\\-](MID[^;/]+) Build',
      'device_replacement' => 'Omega $1',
      'brand_replacement' => 'Omega',
      'model_replacement' => '$1',
    ),
    289 =>
    array (
      'regex' => '^(MID7500|MID\\d+) Mozilla/5\\.0 \\(iPad;',
      'device_replacement' => 'Omega $1',
      'brand_replacement' => 'Omega',
      'model_replacement' => '$1',
    ),
    290 =>
    array (
      'regex' => '; *((?:CIUS|cius)[^;/]*) Build',
      'device_replacement' => 'Openpeak $1',
      'brand_replacement' => 'Openpeak',
      'model_replacement' => '$1',
    ),
    291 =>
    array (
      'regex' => '; *(Find ?(?:5|7a)|R8[012]\\d{1,2}|T703\\d{0,1}|U70\\d{1,2}T?|X90\\d{1,2}) Build',
      'device_replacement' => 'Oppo $1',
      'brand_replacement' => 'Oppo',
      'model_replacement' => '$1',
    ),
    292 =>
    array (
      'regex' => '; *OPPO ?([^;/]+) Build/',
      'device_replacement' => 'Oppo $1',
      'brand_replacement' => 'Oppo',
      'model_replacement' => '$1',
    ),
    293 =>
    array (
      'regex' => '; *(?:Odys\\-|ODYS\\-|ODYS )([^;/]+) Build',
      'device_replacement' => 'Odys $1',
      'brand_replacement' => 'Odys',
      'model_replacement' => '$1',
    ),
    294 =>
    array (
      'regex' => '; *(SELECT) ?(7) Build',
      'device_replacement' => 'Odys $1 $2',
      'brand_replacement' => 'Odys',
      'model_replacement' => '$1 $2',
    ),
    295 =>
    array (
      'regex' => '; *(PEDI)_(PLUS)_(W) Build',
      'device_replacement' => 'Odys $1 $2 $3',
      'brand_replacement' => 'Odys',
      'model_replacement' => '$1 $2 $3',
    ),
    296 =>
    array (
      'regex' => '; *(AEON|BRAVIO|FUSION|FUSION2IN1|Genio|EOS10|IEOS[^;/]*|IRON|Loox|LOOX|LOOX Plus|Motion|NOON|NOON_PRO|NEXT|OPOS|PEDI[^;/]*|PRIME[^;/]*|STUDYTAB|TABLO|Tablet-PC-4|UNO_X8|XELIO[^;/]*|Xelio ?\\d+ ?[Pp]ro|XENO10|XPRESS PRO) Build',
      'device_replacement' => 'Odys $1',
      'brand_replacement' => 'Odys',
      'model_replacement' => '$1',
    ),
    297 =>
    array (
      'regex' => '; *(TP-\\d+) Build/',
      'device_replacement' => 'Orion $1',
      'brand_replacement' => 'Orion',
      'model_replacement' => '$1',
    ),
    298 =>
    array (
      'regex' => '; *(G100W?) Build/',
      'device_replacement' => 'PackardBell $1',
      'brand_replacement' => 'PackardBell',
      'model_replacement' => '$1',
    ),
    299 =>
    array (
      'regex' => '; *(Panasonic)[_ ]([^;/]+) Build',
      'device_replacement' => '$1 $2',
      'brand_replacement' => '$1',
      'model_replacement' => '$2',
    ),
    300 =>
    array (
      'regex' => '; *(FZ-A1B|JT-B1) Build',
      'device_replacement' => 'Panasonic $1',
      'brand_replacement' => 'Panasonic',
      'model_replacement' => '$1',
    ),
    301 =>
    array (
      'regex' => '; *(dL1|DL1) Build',
      'device_replacement' => 'Panasonic $1',
      'brand_replacement' => 'Panasonic',
      'model_replacement' => '$1',
    ),
    302 =>
    array (
      'regex' => '; *(SKY[ _])?(IM\\-[AT]\\d{3}[^;/]+).* Build/',
      'device_replacement' => 'Pantech $1$2',
      'brand_replacement' => 'Pantech',
      'model_replacement' => '$1$2',
    ),
    303 =>
    array (
      'regex' => '; *((?:ADR8995|ADR910L|ADR930L|ADR930VW|PTL21|P8000)(?: 4G)?) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'Pantech',
      'model_replacement' => '$1',
    ),
    304 =>
    array (
      'regex' => '; *Pantech([^;/]+).* Build/',
      'device_replacement' => 'Pantech $1',
      'brand_replacement' => 'Pantech',
      'model_replacement' => '$1',
    ),
    305 =>
    array (
      'regex' => '; *(papyre)[ _\\-]([^;/]+) Build/',
      'regex_flag' => 'i',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Papyre',
      'model_replacement' => '$2',
    ),
    306 =>
    array (
      'regex' => '; *(?:Touchlet )?(X10\\.[^;/]+) Build/',
      'device_replacement' => 'Pearl $1',
      'brand_replacement' => 'Pearl',
      'model_replacement' => '$1',
    ),
    307 =>
    array (
      'regex' => '; PHICOMM (i800) Build/',
      'device_replacement' => 'Phicomm $1',
      'brand_replacement' => 'Phicomm',
      'model_replacement' => '$1',
    ),
    308 =>
    array (
      'regex' => '; PHICOMM ([^;/]+) Build/',
      'device_replacement' => 'Phicomm $1',
      'brand_replacement' => 'Phicomm',
      'model_replacement' => '$1',
    ),
    309 =>
    array (
      'regex' => '; *(FWS\\d{3}[^;/]+) Build/',
      'device_replacement' => 'Phicomm $1',
      'brand_replacement' => 'Phicomm',
      'model_replacement' => '$1',
    ),
    310 =>
    array (
      'regex' => '; *(D633|D822|D833|T539|T939|V726|W335|W336|W337|W3568|W536|W5510|W626|W632|W6350|W6360|W6500|W732|W736|W737|W7376|W820|W832|W8355|W8500|W8510|W930) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Philips',
      'model_replacement' => '$1',
    ),
    311 =>
    array (
      'regex' => '; *(?:Philips|PHILIPS)[ _]([^;/]+) Build',
      'device_replacement' => 'Philips $1',
      'brand_replacement' => 'Philips',
      'model_replacement' => '$1',
    ),
    312 =>
    array (
      'regex' => 'Android 4\\..*; *(M[12356789]|U[12368]|S[123])\\ ?(pro)? Build',
      'device_replacement' => 'Pipo $1$2',
      'brand_replacement' => 'Pipo',
      'model_replacement' => '$1$2',
    ),
    313 =>
    array (
      'regex' => '; *(MOMO[^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Ployer',
      'model_replacement' => '$1',
    ),
    314 =>
    array (
      'regex' => '; *(?:Polaroid[ _])?((?:MIDC\\d{3,}|PMID\\d{2,}|PTAB\\d{3,})[^;/]*)(\\/[^;/]*)? Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'Polaroid',
      'model_replacement' => '$1',
    ),
    315 =>
    array (
      'regex' => '; *(?:Polaroid )(Tablet) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'Polaroid',
      'model_replacement' => '$1',
    ),
    316 =>
    array (
      'regex' => '; *(POMP)[ _\\-](.+?) *(?:Build|[;/\\)])',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Pomp',
      'model_replacement' => '$2',
    ),
    317 =>
    array (
      'regex' => '; *(TB07STA|TB10STA|TB07FTA|TB10FTA) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'Positivo',
      'model_replacement' => '$1',
    ),
    318 =>
    array (
      'regex' => '; *(?:Positivo )?((?:YPY|Ypy)[^;/]+) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'Positivo',
      'model_replacement' => '$1',
    ),
    319 =>
    array (
      'regex' => '; *(MOB-[^;/]+) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'POV',
      'model_replacement' => '$1',
    ),
    320 =>
    array (
      'regex' => '; *POV[ _\\-]([^;/]+) Build/',
      'device_replacement' => 'POV $1',
      'brand_replacement' => 'POV',
      'model_replacement' => '$1',
    ),
    321 =>
    array (
      'regex' => '; *((?:TAB-PLAYTAB|TAB-PROTAB|PROTAB|PlayTabPro|Mobii[ _\\-]|TAB-P)[^;/]*) Build/',
      'device_replacement' => 'POV $1',
      'brand_replacement' => 'POV',
      'model_replacement' => '$1',
    ),
    322 =>
    array (
      'regex' => '; *(?:Prestigio )?((?:PAP|PMP)\\d[^;/]+) Build/',
      'device_replacement' => 'Prestigio $1',
      'brand_replacement' => 'Prestigio',
      'model_replacement' => '$1',
    ),
    323 =>
    array (
      'regex' => '; *(PLT[0-9]{4}.*) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'Proscan',
      'model_replacement' => '$1',
    ),
    324 =>
    array (
      'regex' => '; *(A2|A5|A8|A900)_?(Classic)? Build',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Qmobile',
      'model_replacement' => '$1 $2',
    ),
    325 =>
    array (
      'regex' => '; *(Q[Mm]obile)_([^_]+)_([^_]+) Build',
      'device_replacement' => 'Qmobile $2 $3',
      'brand_replacement' => 'Qmobile',
      'model_replacement' => '$2 $3',
    ),
    326 =>
    array (
      'regex' => '; *(Q\\-?[Mm]obile)[_ ](A[^;/]+) Build',
      'device_replacement' => 'Qmobile $2',
      'brand_replacement' => 'Qmobile',
      'model_replacement' => '$2',
    ),
    327 =>
    array (
      'regex' => '; *(Q\\-Smart)[ _]([^;/]+) Build/',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Qmobilevn',
      'model_replacement' => '$2',
    ),
    328 =>
    array (
      'regex' => '; *(Q\\-?[Mm]obile)[ _\\-](S[^;/]+) Build/',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Qmobilevn',
      'model_replacement' => '$2',
    ),
    329 =>
    array (
      'regex' => '; *(TA1013) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Quanta',
      'model_replacement' => '$1',
    ),
    330 =>
    array (
      'regex' => '; *(RK\\d+),? Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'Rockchip',
      'model_replacement' => '$1',
    ),
    331 =>
    array (
      'regex' => ' Build/(RK\\d+)',
      'device_replacement' => '$1',
      'brand_replacement' => 'Rockchip',
      'model_replacement' => '$1',
    ),
    332 =>
    array (
      'regex' => '; *(SAMSUNG |Samsung )?((?:Galaxy (?:Note II|S\\d)|GT-I9082|GT-I9205|GT-N7\\d{3}|SM-N9005)[^;/]*)\\/?[^;/]* Build/',
      'device_replacement' => 'Samsung $1$2',
      'brand_replacement' => 'Samsung',
      'model_replacement' => '$2',
    ),
    333 =>
    array (
      'regex' => '; *(Google )?(Nexus [Ss](?: 4G)?) Build/',
      'device_replacement' => 'Samsung $1$2',
      'brand_replacement' => 'Samsung',
      'model_replacement' => '$2',
    ),
    334 =>
    array (
      'regex' => '; *(SAMSUNG |Samsung )([^\\/]*)\\/[^ ]* Build/',
      'device_replacement' => 'Samsung $2',
      'brand_replacement' => 'Samsung',
      'model_replacement' => '$2',
    ),
    335 =>
    array (
      'regex' => '; *(Galaxy(?: Ace| Nexus| S ?II+|Nexus S| with MCR 1.2| Mini Plus 4G)?) Build/',
      'device_replacement' => 'Samsung $1',
      'brand_replacement' => 'Samsung',
      'model_replacement' => '$1',
    ),
    336 =>
    array (
      'regex' => '; *(SAMSUNG[ _\\-] *)+([^;/]+) Build',
      'device_replacement' => 'Samsung $2',
      'brand_replacement' => 'Samsung',
      'model_replacement' => '$2',
    ),
    337 =>
    array (
      'regex' => '; *(SAMSUNG-)?(GT\\-[BINPS]\\d{4}[^\\/]*)(\\/[^ ]*) Build',
      'device_replacement' => 'Samsung $1$2$3',
      'brand_replacement' => 'Samsung',
      'model_replacement' => '$2',
    ),
    338 =>
    array (
      'regex' => '(?:; *|^)((?:GT\\-[BIiNPS]\\d{4}|I9\\d{2}0[A-Za-z\\+]?\\b)[^;/\\)]*?)(?:Build|Linux|MIUI|[;/\\)])',
      'device_replacement' => 'Samsung $1',
      'brand_replacement' => 'Samsung',
      'model_replacement' => '$1',
    ),
    339 =>
    array (
      'regex' => '; (SAMSUNG-)([A-Za-z0-9\\-]+).* Build/',
      'device_replacement' => 'Samsung $1$2',
      'brand_replacement' => 'Samsung',
      'model_replacement' => '$2',
    ),
    340 =>
    array (
      'regex' => '; *((?:SCH|SGH|SHV|SHW|SPH|SC|SM)\\-[A-Za-z0-9 ]+)(/?[^ ]*)? Build',
      'device_replacement' => 'Samsung $1',
      'brand_replacement' => 'Samsung',
      'model_replacement' => '$1',
    ),
    341 =>
    array (
      'regex' => ' ((?:SCH)\\-[A-Za-z0-9 ]+)(/?[^ ]*)? Build',
      'device_replacement' => 'Samsung $1',
      'brand_replacement' => 'Samsung',
      'model_replacement' => '$1',
    ),
    342 =>
    array (
      'regex' => '; *(Behold ?(?:2|II)|YP\\-G[^;/]+|EK-GC100|SCL21|I9300) Build',
      'device_replacement' => 'Samsung $1',
      'brand_replacement' => 'Samsung',
      'model_replacement' => '$1',
    ),
    343 =>
    array (
      'regex' => '; *(SH\\-?\\d\\d[^;/]+|SBM\\d[^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Sharp',
      'model_replacement' => '$1',
    ),
    344 =>
    array (
      'regex' => '; *(SHARP[ -])([^;/]+) Build',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'Sharp',
      'model_replacement' => '$2',
    ),
    345 =>
    array (
      'regex' => '; *(SPX[_\\-]\\d[^;/]*) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'Simvalley',
      'model_replacement' => '$1',
    ),
    346 =>
    array (
      'regex' => '; *(SX7\\-PEARL\\.GmbH) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'Simvalley',
      'model_replacement' => '$1',
    ),
    347 =>
    array (
      'regex' => '; *(SP[T]?\\-\\d{2}[^;/]*) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'Simvalley',
      'model_replacement' => '$1',
    ),
    348 =>
    array (
      'regex' => '; *(SK\\-.*) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'SKtelesys',
      'model_replacement' => '$1',
    ),
    349 =>
    array (
      'regex' => '; *(?:SKYTEX|SX)-([^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Skytex',
      'model_replacement' => '$1',
    ),
    350 =>
    array (
      'regex' => '; *(IMAGINE [^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Skytex',
      'model_replacement' => '$1',
    ),
    351 =>
    array (
      'regex' => '; *(SmartQ) ?([^;/]+) Build/',
      'device_replacement' => '$1 $2',
      'brand_replacement' => '$1',
      'model_replacement' => '$2',
    ),
    352 =>
    array (
      'regex' => '; *(WF7C|WF10C|SBT[^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Smartbitt',
      'model_replacement' => '$1',
    ),
    353 =>
    array (
      'regex' => '; *(SBM(?:003SH|005SH|006SH|007SH|102SH)) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Sharp',
      'model_replacement' => '$1',
    ),
    354 =>
    array (
      'regex' => '; *(003P|101P|101P11C|102P) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Panasonic',
      'model_replacement' => '$1',
    ),
    355 =>
    array (
      'regex' => '; *(00\\dZ) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'ZTE',
      'model_replacement' => '$1',
    ),
    356 =>
    array (
      'regex' => '; HTC(X06HT) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'HTC',
      'model_replacement' => '$1',
    ),
    357 =>
    array (
      'regex' => '; *(001HT|X06HT) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'HTC',
      'model_replacement' => '$1',
    ),
    358 =>
    array (
      'regex' => '; *(201M) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Motorola',
      'model_replacement' => 'XT902',
    ),
    359 =>
    array (
      'regex' => '; *(ST\\d{4}.*)Build/ST',
      'device_replacement' => 'Trekstor $1',
      'brand_replacement' => 'Trekstor',
      'model_replacement' => '$1',
    ),
    360 =>
    array (
      'regex' => '; *(ST\\d{4}.*) Build/',
      'device_replacement' => 'Trekstor $1',
      'brand_replacement' => 'Trekstor',
      'model_replacement' => '$1',
    ),
    361 =>
    array (
      'regex' => '; *(Sony ?Ericsson ?)([^;/]+) Build',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'SonyEricsson',
      'model_replacement' => '$2',
    ),
    362 =>
    array (
      'regex' => '; *((?:SK|ST|E|X|LT|MK|MT|WT)\\d{2}[a-z0-9]*(?:-o)?|R800i|U20i) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'SonyEricsson',
      'model_replacement' => '$1',
    ),
    363 =>
    array (
      'regex' => '; *(Xperia (?:A8|Arc|Acro|Active|Live with Walkman|Mini|Neo|Play|Pro|Ray|X\\d+)[^;/]*) Build',
      'regex_flag' => 'i',
      'device_replacement' => '$1',
      'brand_replacement' => 'SonyEricsson',
      'model_replacement' => '$1',
    ),
    364 =>
    array (
      'regex' => '; Sony (Tablet[^;/]+) Build',
      'device_replacement' => 'Sony $1',
      'brand_replacement' => 'Sony',
      'model_replacement' => '$1',
    ),
    365 =>
    array (
      'regex' => '; Sony ([^;/]+) Build',
      'device_replacement' => 'Sony $1',
      'brand_replacement' => 'Sony',
      'model_replacement' => '$1',
    ),
    366 =>
    array (
      'regex' => '; *(Sony)([A-Za-z0-9\\-]+) Build',
      'device_replacement' => '$1 $2',
      'brand_replacement' => '$1',
      'model_replacement' => '$2',
    ),
    367 =>
    array (
      'regex' => '; *(Xperia [^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Sony',
      'model_replacement' => '$1',
    ),
    368 =>
    array (
      'regex' => '; *(C(?:1[0-9]|2[0-9]|53|55|6[0-9])[0-9]{2}|D[25]\\d{3}|D6[56]\\d{2}) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Sony',
      'model_replacement' => '$1',
    ),
    369 =>
    array (
      'regex' => '; *(SGP\\d{3}|SGPT\\d{2}) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Sony',
      'model_replacement' => '$1',
    ),
    370 =>
    array (
      'regex' => '; *(NW-Z1000Series) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Sony',
      'model_replacement' => '$1',
    ),
    371 =>
    array (
      'regex' => 'PLAYSTATION 3',
      'device_replacement' => 'PlayStation 3',
      'brand_replacement' => 'Sony',
      'model_replacement' => 'PlayStation 3',
    ),
    372 =>
    array (
      'regex' => '(PlayStation (?:Portable|Vita|\\d+))',
      'device_replacement' => '$1',
      'brand_replacement' => 'Sony',
      'model_replacement' => '$1',
    ),
    373 =>
    array (
      'regex' => '; *((?:CSL_Spice|Spice|SPICE|CSL)[ _\\-]?)?([Mm][Ii])([ _\\-])?(\\d{3}[^;/]*) Build/',
      'device_replacement' => '$1$2$3$4',
      'brand_replacement' => 'Spice',
      'model_replacement' => 'Mi$4',
    ),
    374 =>
    array (
      'regex' => '; *(Sprint )(.+?) *(?:Build|[;/])',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'Sprint',
      'model_replacement' => '$2',
    ),
    375 =>
    array (
      'regex' => '\\b(Sprint)[: ]([^;,/ ]+)',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'Sprint',
      'model_replacement' => '$2',
    ),
    376 =>
    array (
      'regex' => '; *(TAGI[ ]?)(MID) ?([^;/]+) Build/',
      'device_replacement' => '$1$2$3',
      'brand_replacement' => 'Tagi',
      'model_replacement' => '$2$3',
    ),
    377 =>
    array (
      'regex' => '; *(Oyster500|Opal 800) Build',
      'device_replacement' => 'Tecmobile $1',
      'brand_replacement' => 'Tecmobile',
      'model_replacement' => '$1',
    ),
    378 =>
    array (
      'regex' => '; *(TECNO[ _])([^;/]+) Build/',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'Tecno',
      'model_replacement' => '$2',
    ),
    379 =>
    array (
      'regex' => '; *Android for (Telechips|Techvision) ([^ ]+) ',
      'regex_flag' => 'i',
      'device_replacement' => '$1 $2',
      'brand_replacement' => '$1',
      'model_replacement' => '$2',
    ),
    380 =>
    array (
      'regex' => '; *(T-Hub2) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'Telstra',
      'model_replacement' => '$1',
    ),
    381 =>
    array (
      'regex' => '; *(PAD) ?(100[12]) Build/',
      'device_replacement' => 'Terra $1$2',
      'brand_replacement' => 'Terra',
      'model_replacement' => '$1$2',
    ),
    382 =>
    array (
      'regex' => '; *(T[BM]-\\d{3}[^;/]+) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'Texet',
      'model_replacement' => '$1',
    ),
    383 =>
    array (
      'regex' => '; *(tolino [^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Thalia',
      'model_replacement' => '$1',
    ),
    384 =>
    array (
      'regex' => '; *Build/.* (TOLINO_BROWSER)',
      'device_replacement' => '$1',
      'brand_replacement' => 'Thalia',
      'model_replacement' => 'Tolino Shine',
    ),
    385 =>
    array (
      'regex' => '; *(?:CJ[ -])?(ThL|THL)[ -]([^;/]+) Build/',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Thl',
      'model_replacement' => '$2',
    ),
    386 =>
    array (
      'regex' => '; *(T100|T200|T5|W100|W200|W8s) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'Thl',
      'model_replacement' => '$1',
    ),
    387 =>
    array (
      'regex' => '; *(T-Mobile[ _]G2[ _]Touch) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'HTC',
      'model_replacement' => 'Hero',
    ),
    388 =>
    array (
      'regex' => '; *(T-Mobile[ _]G2) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'HTC',
      'model_replacement' => 'Desire Z',
    ),
    389 =>
    array (
      'regex' => '; *(T-Mobile myTouch Q) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Huawei',
      'model_replacement' => 'U8730',
    ),
    390 =>
    array (
      'regex' => '; *(T-Mobile myTouch) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Huawei',
      'model_replacement' => 'U8680',
    ),
    391 =>
    array (
      'regex' => '; *(T-Mobile_Espresso) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'HTC',
      'model_replacement' => 'Espresso',
    ),
    392 =>
    array (
      'regex' => '; *(T-Mobile G1) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'HTC',
      'model_replacement' => 'Dream',
    ),
    393 =>
    array (
      'regex' => '\\b(T-Mobile ?)?(myTouch)[ _]?([34]G)[ _]?([^\\/]*) (?:Mozilla|Build)',
      'device_replacement' => '$1$2 $3 $4',
      'brand_replacement' => 'HTC',
      'model_replacement' => '$2 $3 $4',
    ),
    394 =>
    array (
      'regex' => '\\b(T-Mobile)_([^_]+)_(.*) Build',
      'device_replacement' => '$1 $2 $3',
      'brand_replacement' => 'Tmobile',
      'model_replacement' => '$2 $3',
    ),
    395 =>
    array (
      'regex' => '\\b(T-Mobile)[_ ]?(.*?)Build',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Tmobile',
      'model_replacement' => '$2',
    ),
    396 =>
    array (
      'regex' => ' (ATP[0-9]{4}) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Tomtec',
      'model_replacement' => '$1',
    ),
    397 =>
    array (
      'regex' => ' *(TOOKY)[ _\\-]([^;/]+) ?(?:Build|;)',
      'regex_flag' => 'i',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Tooky',
      'model_replacement' => '$2',
    ),
    398 =>
    array (
      'regex' => '\\b(TOSHIBA_AC_AND_AZ|TOSHIBA_FOLIO_AND_A|FOLIO_AND_A)',
      'device_replacement' => '$1',
      'brand_replacement' => 'Toshiba',
      'model_replacement' => 'Folio 100',
    ),
    399 =>
    array (
      'regex' => '; *([Ff]olio ?100) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'Toshiba',
      'model_replacement' => 'Folio 100',
    ),
    400 =>
    array (
      'regex' => '; *(AT[0-9]{2,3}(?:\\-A|LE\\-A|PE\\-A|SE|a)?|AT7-A|AT1S0|Hikari-iFrame/WDPF-[^;/]+|THRiVE|Thrive) Build/',
      'device_replacement' => 'Toshiba $1',
      'brand_replacement' => 'Toshiba',
      'model_replacement' => '$1',
    ),
    401 =>
    array (
      'regex' => '; *(TM-MID\\d+[^;/]+|TOUCHMATE|MID-750) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Touchmate',
      'model_replacement' => '$1',
    ),
    402 =>
    array (
      'regex' => '; *(TM-SM\\d+[^;/]+) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Touchmate',
      'model_replacement' => '$1',
    ),
    403 =>
    array (
      'regex' => '; *(A10 [Bb]asic2?) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'Treq',
      'model_replacement' => '$1',
    ),
    404 =>
    array (
      'regex' => '; *(TREQ[ _\\-])([^;/]+) Build',
      'regex_flag' => 'i',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'Treq',
      'model_replacement' => '$2',
    ),
    405 =>
    array (
      'regex' => '; *(X-?5|X-?3) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'Umeox',
      'model_replacement' => '$1',
    ),
    406 =>
    array (
      'regex' => '; *(A502\\+?|A936|A603|X1|X2) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'Umeox',
      'model_replacement' => '$1',
    ),
    407 =>
    array (
      'regex' => '(TOUCH(?:TAB|PAD).+?) Build/',
      'regex_flag' => 'i',
      'device_replacement' => 'Versus $1',
      'brand_replacement' => 'Versus',
      'model_replacement' => '$1',
    ),
    408 =>
    array (
      'regex' => '(VERTU) ([^;/]+) Build/',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Vertu',
      'model_replacement' => '$2',
    ),
    409 =>
    array (
      'regex' => '; *(Videocon)[ _\\-]([^;/]+) *(?:Build|;)',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'Videocon',
      'model_replacement' => '$2',
    ),
    410 =>
    array (
      'regex' => ' (VT\\d{2}[A-Za-z]*) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Videocon',
      'model_replacement' => '$1',
    ),
    411 =>
    array (
      'regex' => '; *((?:ViewPad|ViewPhone|VSD)[^;/]+) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'Viewsonic',
      'model_replacement' => '$1',
    ),
    412 =>
    array (
      'regex' => '; *(ViewSonic-)([^;/]+) Build/',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'Viewsonic',
      'model_replacement' => '$2',
    ),
    413 =>
    array (
      'regex' => '; *(GTablet.*) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'Viewsonic',
      'model_replacement' => '$1',
    ),
    414 =>
    array (
      'regex' => '; *([Vv]ivo)[ _]([^;/]+) Build',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'vivo',
      'model_replacement' => '$2',
    ),
    415 =>
    array (
      'regex' => '(Vodafone) (.*) Build/',
      'device_replacement' => '$1 $2',
      'brand_replacement' => '$1',
      'model_replacement' => '$2',
    ),
    416 =>
    array (
      'regex' => '; *(?:Walton[ _\\-])?(Primo[ _\\-][^;/]+) Build',
      'regex_flag' => 'i',
      'device_replacement' => 'Walton $1',
      'brand_replacement' => 'Walton',
      'model_replacement' => '$1',
    ),
    417 =>
    array (
      'regex' => '; *(?:WIKO[ \\-])?(CINK\\+?|BARRY|BLOOM|DARKFULL|DARKMOON|DARKNIGHT|DARKSIDE|FIZZ|HIGHWAY|IGGY|OZZY|RAINBOW|STAIRWAY|SUBLIM|WAX|CINK [^;/]+) Build/',
      'regex_flag' => 'i',
      'device_replacement' => 'Wiko $1',
      'brand_replacement' => 'Wiko',
      'model_replacement' => '$1',
    ),
    418 =>
    array (
      'regex' => '; *WellcoM-([^;/]+) Build',
      'device_replacement' => 'Wellcom $1',
      'brand_replacement' => 'Wellcom',
      'model_replacement' => '$1',
    ),
    419 =>
    array (
      'regex' => '(?:(WeTab)-Browser|; (wetab) Build)',
      'device_replacement' => '$1',
      'brand_replacement' => 'WeTab',
      'model_replacement' => 'WeTab',
    ),
    420 =>
    array (
      'regex' => '; *(AT-AS[^;/]+) Build',
      'device_replacement' => 'Wolfgang $1',
      'brand_replacement' => 'Wolfgang',
      'model_replacement' => '$1',
    ),
    421 =>
    array (
      'regex' => '; *(?:Woxter|Wxt) ([^;/]+) Build',
      'device_replacement' => 'Woxter $1',
      'brand_replacement' => 'Woxter',
      'model_replacement' => '$1',
    ),
    422 =>
    array (
      'regex' => '; *(?:Xenta |Luna )?(TAB[234][0-9]{2}|TAB0[78]-\\d{3}|TAB0?9-\\d{3}|TAB1[03]-\\d{3}|SMP\\d{2}-\\d{3}) Build/',
      'device_replacement' => 'Yarvik $1',
      'brand_replacement' => 'Yarvik',
      'model_replacement' => '$1',
    ),
    423 =>
    array (
      'regex' => '; *([A-Z]{2,4})(M\\d{3,}[A-Z]{2})([^;\\)\\/]*)(?: Build|[;\\)])',
      'device_replacement' => 'Yifang $1$2$3',
      'brand_replacement' => 'Yifang',
      'model_replacement' => '$2',
    ),
    424 =>
    array (
      'regex' => '; *((MI|HM|MI-ONE|Redmi)[ -](NOTE |Note )?[^;/]*) (Build|MIUI)/',
      'device_replacement' => 'XiaoMi $1',
      'brand_replacement' => 'XiaoMi',
      'model_replacement' => '$1',
    ),
    425 =>
    array (
      'regex' => '; *XOLO[ _]([^;/]*tab.*) Build',
      'regex_flag' => 'i',
      'device_replacement' => 'Xolo $1',
      'brand_replacement' => 'Xolo',
      'model_replacement' => '$1',
    ),
    426 =>
    array (
      'regex' => '; *XOLO[ _]([^;/]+) Build',
      'regex_flag' => 'i',
      'device_replacement' => 'Xolo $1',
      'brand_replacement' => 'Xolo',
      'model_replacement' => '$1',
    ),
    427 =>
    array (
      'regex' => '; *(q\\d0{2,3}[a-z]?) Build',
      'regex_flag' => 'i',
      'device_replacement' => 'Xolo $1',
      'brand_replacement' => 'Xolo',
      'model_replacement' => '$1',
    ),
    428 =>
    array (
      'regex' => '; *(PAD ?[79]\\d+[^;/]*|TelePAD\\d+[^;/]) Build',
      'device_replacement' => 'Xoro $1',
      'brand_replacement' => 'Xoro',
      'model_replacement' => '$1',
    ),
    429 =>
    array (
      'regex' => '; *(?:(?:ZOPO|Zopo)[ _]([^;/]+)|(ZP ?(?:\\d{2}[^;/]+|C2))|(C[2379])) Build',
      'device_replacement' => '$1$2$3',
      'brand_replacement' => 'Zopo',
      'model_replacement' => '$1$2$3',
    ),
    430 =>
    array (
      'regex' => '; *(ZiiLABS) (Zii[^;/]*) Build',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'ZiiLabs',
      'model_replacement' => '$2',
    ),
    431 =>
    array (
      'regex' => '; *(Zii)_([^;/]*) Build',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'ZiiLabs',
      'model_replacement' => '$2',
    ),
    432 =>
    array (
      'regex' => '; *(ARIZONA|(?:ATLAS|Atlas) W|D930|Grand (?:[SX][^;]*|Era|Memo[^;]*)|JOE|(?:Kis|KIS)\\b[^;]*|Libra|Light [^;]*|N8[056][01]|N850L|N8000|N9[15]\\d{2}|N9810|NX501|Optik|(?:Vip )Racer[^;]*|RacerII|RACERII|San Francisco[^;]*|V9[AC]|V55|V881|Z[679][0-9]{2}[A-z]?) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'ZTE',
      'model_replacement' => '$1',
    ),
    433 =>
    array (
      'regex' => '; *([A-Z]\\d+)_USA_[^;]* Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'ZTE',
      'model_replacement' => '$1',
    ),
    434 =>
    array (
      'regex' => '; *(SmartTab\\d+)[^;]* Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'ZTE',
      'model_replacement' => '$1',
    ),
    435 =>
    array (
      'regex' => '; *(?:Blade|BLADE|ZTE-BLADE)([^;/]*) Build',
      'device_replacement' => 'ZTE Blade$1',
      'brand_replacement' => 'ZTE',
      'model_replacement' => 'Blade$1',
    ),
    436 =>
    array (
      'regex' => '; *(?:Skate|SKATE|ZTE-SKATE)([^;/]*) Build',
      'device_replacement' => 'ZTE Skate$1',
      'brand_replacement' => 'ZTE',
      'model_replacement' => 'Skate$1',
    ),
    437 =>
    array (
      'regex' => '; *(Orange |Optimus )(Monte Carlo|San Francisco) Build',
      'device_replacement' => '$1$2',
      'brand_replacement' => 'ZTE',
      'model_replacement' => '$1$2',
    ),
    438 =>
    array (
      'regex' => '; *(?:ZXY-ZTE_|ZTE\\-U |ZTE[\\- _]|ZTE-C[_ ])([^;/]+) Build',
      'device_replacement' => 'ZTE $1',
      'brand_replacement' => 'ZTE',
      'model_replacement' => '$1',
    ),
    439 =>
    array (
      'regex' => '; (BASE) (lutea|Lutea 2|Tab[^;]*) Build',
      'device_replacement' => '$1 $2',
      'brand_replacement' => 'ZTE',
      'model_replacement' => '$1 $2',
    ),
    440 =>
    array (
      'regex' => '; (Avea inTouch 2|soft stone|tmn smart a7|Movistar[ _]Link) Build',
      'regex_flag' => 'i',
      'device_replacement' => '$1',
      'brand_replacement' => 'ZTE',
      'model_replacement' => '$1',
    ),
    441 =>
    array (
      'regex' => '; *(vp9plus)\\)',
      'device_replacement' => '$1',
      'brand_replacement' => 'ZTE',
      'model_replacement' => '$1',
    ),
    442 =>
    array (
      'regex' => '; ?(Cloud[ _]Z5|z1000|Z99 2G|z99|z930|z999|z990|z909|Z919|z900) Build/',
      'device_replacement' => '$1',
      'brand_replacement' => 'Zync',
      'model_replacement' => '$1',
    ),
    443 =>
    array (
      'regex' => '; ?(KFOT|Kindle Fire) Build\\b',
      'device_replacement' => 'Kindle Fire',
      'brand_replacement' => 'Amazon',
      'model_replacement' => 'Kindle Fire',
    ),
    444 =>
    array (
      'regex' => '; ?(KFOTE|Amazon Kindle Fire2) Build\\b',
      'device_replacement' => 'Kindle Fire 2',
      'brand_replacement' => 'Amazon',
      'model_replacement' => 'Kindle Fire 2',
    ),
    445 =>
    array (
      'regex' => '; ?(KFTT) Build\\b',
      'device_replacement' => 'Kindle Fire HD',
      'brand_replacement' => 'Amazon',
      'model_replacement' => 'Kindle Fire HD 7"',
    ),
    446 =>
    array (
      'regex' => '; ?(KFJWI) Build\\b',
      'device_replacement' => 'Kindle Fire HD 8.9" WiFi',
      'brand_replacement' => 'Amazon',
      'model_replacement' => 'Kindle Fire HD 8.9" WiFi',
    ),
    447 =>
    array (
      'regex' => '; ?(KFJWA) Build\\b',
      'device_replacement' => 'Kindle Fire HD 8.9" 4G',
      'brand_replacement' => 'Amazon',
      'model_replacement' => 'Kindle Fire HD 8.9" 4G',
    ),
    448 =>
    array (
      'regex' => '; ?(KFSOWI) Build\\b',
      'device_replacement' => 'Kindle Fire HD 7" WiFi',
      'brand_replacement' => 'Amazon',
      'model_replacement' => 'Kindle Fire HD 7" WiFi',
    ),
    449 =>
    array (
      'regex' => '; ?(KFTHWI) Build\\b',
      'device_replacement' => 'Kindle Fire HDX 7" WiFi',
      'brand_replacement' => 'Amazon',
      'model_replacement' => 'Kindle Fire HDX 7" WiFi',
    ),
    450 =>
    array (
      'regex' => '; ?(KFTHWA) Build\\b',
      'device_replacement' => 'Kindle Fire HDX 7" 4G',
      'brand_replacement' => 'Amazon',
      'model_replacement' => 'Kindle Fire HDX 7" 4G',
    ),
    451 =>
    array (
      'regex' => '; ?(KFAPWI) Build\\b',
      'device_replacement' => 'Kindle Fire HDX 8.9" WiFi',
      'brand_replacement' => 'Amazon',
      'model_replacement' => 'Kindle Fire HDX 8.9" WiFi',
    ),
    452 =>
    array (
      'regex' => '; ?(KFAPWA) Build\\b',
      'device_replacement' => 'Kindle Fire HDX 8.9" 4G',
      'brand_replacement' => 'Amazon',
      'model_replacement' => 'Kindle Fire HDX 8.9" 4G',
    ),
    453 =>
    array (
      'regex' => '; ?Amazon ([^;/]+) Build\\b',
      'device_replacement' => '$1',
      'brand_replacement' => 'Amazon',
      'model_replacement' => '$1',
    ),
    454 =>
    array (
      'regex' => '; ?(Kindle) Build\\b',
      'device_replacement' => 'Kindle',
      'brand_replacement' => 'Amazon',
      'model_replacement' => 'Kindle',
    ),
    455 =>
    array (
      'regex' => '; ?(Silk)/(\\d+)\\.(\\d+)(?:\\.([0-9\\-]+))? Build\\b',
      'device_replacement' => 'Kindle Fire',
      'brand_replacement' => 'Amazon',
      'model_replacement' => 'Kindle Fire$2',
    ),
    456 =>
    array (
      'regex' => ' (Kindle)/(\\d+\\.\\d+)',
      'device_replacement' => 'Kindle',
      'brand_replacement' => 'Amazon',
      'model_replacement' => '$1 $2',
    ),
    457 =>
    array (
      'regex' => ' (Silk|Kindle)/(\\d+)\\.',
      'device_replacement' => 'Kindle',
      'brand_replacement' => 'Amazon',
      'model_replacement' => 'Kindle',
    ),
    458 =>
    array (
      'regex' => '(sprd)\\-([^/]+)/',
      'device_replacement' => '$1 $2',
      'brand_replacement' => '$1',
      'model_replacement' => '$2',
    ),
    459 =>
    array (
      'regex' => '; *(H\\d{2}00\\+?) Build',
      'device_replacement' => '$1',
      'brand_replacement' => 'Hero',
      'model_replacement' => '$1',
    ),
    460 =>
    array (
      'regex' => '; *(iphone|iPhone5) Build/',
      'device_replacement' => 'Xianghe $1',
      'brand_replacement' => 'Xianghe',
      'model_replacement' => '$1',
    ),
    461 =>
    array (
      'regex' => '; *(e\\d{4}[a-z]?_?v\\d+|v89_[^;/]+)[^;/]+ Build/',
      'device_replacement' => 'Xianghe $1',
      'brand_replacement' => 'Xianghe',
      'model_replacement' => '$1',
    ),
    462 =>
    array (
      'regex' => '\\bUSCC[_\\-]?([^ ;/\\)]+)',
      'device_replacement' => '$1',
      'brand_replacement' => 'Cellular',
      'model_replacement' => '$1',
    ),
    463 =>
    array (
      'regex' => 'Windows Phone [^;]+; .*?IEMobile/[^;\\)]+[;\\)] ?(?:ARM; ?Touch; ?|Touch; ?)?(?:ALCATEL)[^;]*; *([^;,\\)]+)',
      'device_replacement' => 'Alcatel $1',
      'brand_replacement' => 'Alcatel',
      'model_replacement' => '$1',
    ),
    464 =>
    array (
      'regex' => 'Windows Phone [^;]+; .*?IEMobile/[^;\\)]+[;\\)] ?(?:ARM; ?Touch; ?|Touch; ?|WpsLondonTest; ?)?(?:ASUS|Asus)[^;]*; *([^;,\\)]+)',
      'device_replacement' => 'Asus $1',
      'brand_replacement' => 'Asus',
      'model_replacement' => '$1',
    ),
    465 =>
    array (
      'regex' => 'Windows Phone [^;]+; .*?IEMobile/[^;\\)]+[;\\)] ?(?:ARM; ?Touch; ?|Touch; ?)?(?:DELL|Dell)[^;]*; *([^;,\\)]+)',
      'device_replacement' => 'Dell $1',
      'brand_replacement' => 'Dell',
      'model_replacement' => '$1',
    ),
    466 =>
    array (
      'regex' => 'Windows Phone [^;]+; .*?IEMobile/[^;\\)]+[;\\)] ?(?:ARM; ?Touch; ?|Touch; ?|WpsLondonTest; ?)?(?:HTC|Htc|HTC_blocked[^;]*)[^;]*; *(?:HTC)?([^;,\\)]+)',
      'device_replacement' => 'HTC $1',
      'brand_replacement' => 'HTC',
      'model_replacement' => '$1',
    ),
    467 =>
    array (
      'regex' => 'Windows Phone [^;]+; .*?IEMobile/[^;\\)]+[;\\)] ?(?:ARM; ?Touch; ?|Touch; ?)?(?:HUAWEI)[^;]*; *(?:HUAWEI )?([^;,\\)]+)',
      'device_replacement' => 'Huawei $1',
      'brand_replacement' => 'Huawei',
      'model_replacement' => '$1',
    ),
    468 =>
    array (
      'regex' => 'Windows Phone [^;]+; .*?IEMobile/[^;\\)]+[;\\)] ?(?:ARM; ?Touch; ?|Touch; ?)?(?:LG|Lg)[^;]*; *(?:LG[ \\-])?([^;,\\)]+)',
      'device_replacement' => 'LG $1',
      'brand_replacement' => 'LG',
      'model_replacement' => '$1',
    ),
    469 =>
    array (
      'regex' => 'Windows Phone [^;]+; .*?IEMobile/[^;\\)]+[;\\)] ?(?:ARM; ?Touch; ?|Touch; ?)?(?:NOKIA|Nokia)[^;]*; *(?:NOKIA ?|Nokia ?|LUMIA ?|[Ll]umia ?)*(\\d{3,}[^;\\)]*)',
      'device_replacement' => 'Lumia $1',
      'brand_replacement' => 'Nokia',
      'model_replacement' => 'Lumia $1',
    ),
    470 =>
    array (
      'regex' => 'Windows Phone [^;]+; .*?IEMobile/[^;\\)]+[;\\)] ?(?:ARM; ?Touch; ?|Touch; ?)?(?:NOKIA|Nokia)[^;]*; *(RM-\\d{3,})',
      'device_replacement' => 'Nokia $1',
      'brand_replacement' => 'Nokia',
      'model_replacement' => '$1',
    ),
    471 =>
    array (
      'regex' => '(?:Windows Phone [^;]+; .*?IEMobile/[^;\\)]+[;\\)]|WPDesktop;) ?(?:ARM; ?Touch; ?|Touch; ?)?(?:NOKIA|Nokia)[^;]*; *(?:NOKIA ?|Nokia ?|LUMIA ?|[Ll]umia ?)*([^;\\)]+)',
      'device_replacement' => 'Nokia $1',
      'brand_replacement' => 'Nokia',
      'model_replacement' => '$1',
    ),
    472 =>
    array (
      'regex' => 'Windows Phone [^;]+; .*?IEMobile/[^;\\)]+[;\\)] ?(?:ARM; ?Touch; ?|Touch; ?)?(?:Microsoft(?: Corporation)?)[^;]*; *([^;,\\)]+)',
      'device_replacement' => 'Microsoft $1',
      'brand_replacement' => 'Microsoft',
      'model_replacement' => '$1',
    ),
    473 =>
    array (
      'regex' => 'Windows Phone [^;]+; .*?IEMobile/[^;\\)]+[;\\)] ?(?:ARM; ?Touch; ?|Touch; ?|WpsLondonTest; ?)?(?:SAMSUNG)[^;]*; *(?:SAMSUNG )?([^;,\\.\\)]+)',
      'device_replacement' => 'Samsung $1',
      'brand_replacement' => 'Samsung',
      'model_replacement' => '$1',
    ),
    474 =>
    array (
      'regex' => 'Windows Phone [^;]+; .*?IEMobile/[^;\\)]+[;\\)] ?(?:ARM; ?Touch; ?|Touch; ?|WpsLondonTest; ?)?(?:TOSHIBA|FujitsuToshibaMobileCommun)[^;]*; *([^;,\\)]+)',
      'device_replacement' => 'Toshiba $1',
      'brand_replacement' => 'Toshiba',
      'model_replacement' => '$1',
    ),
    475 =>
    array (
      'regex' => 'Windows Phone [^;]+; .*?IEMobile/[^;\\)]+[;\\)] ?(?:ARM; ?Touch; ?|Touch; ?|WpsLondonTest; ?)?([^;]+); *([^;,\\)]+)',
      'device_replacement' => '$1 $2',
      'brand_replacement' => '$1',
      'model_replacement' => '$2',
    ),
    476 =>
    array (
      'regex' => '(?:^|; )SAMSUNG\\-([A-Za-z0-9\\-]+).* Bada/',
      'device_replacement' => 'Samsung $1',
      'brand_replacement' => 'Samsung',
      'model_replacement' => '$1',
    ),
    477 =>
    array (
      'regex' => '\\(Mobile; ALCATEL ?(One|ONE) ?(Touch|TOUCH) ?([^;/]+)(?:/[^;]+)?; rv:[^\\)]+\\) Gecko/[^\\/]+ Firefox/',
      'device_replacement' => 'Alcatel $1 $2 $3',
      'brand_replacement' => 'Alcatel',
      'model_replacement' => 'One Touch $3',
    ),
    478 =>
    array (
      'regex' => '\\(Mobile; (?:ZTE([^;]+)|(OpenC)); rv:[^\\)]+\\) Gecko/[^\\/]+ Firefox/',
      'device_replacement' => 'ZTE $1$2',
      'brand_replacement' => 'ZTE',
      'model_replacement' => '$1$2',
    ),
    479 =>
    array (
      'regex' => 'Nokia(N[0-9]+)([A-z_\\-][A-z0-9_\\-]*)',
      'device_replacement' => 'Nokia $1',
      'brand_replacement' => 'Nokia',
      'model_replacement' => '$1$2',
    ),
    480 =>
    array (
      'regex' => '(?:NOKIA|Nokia)(?:\\-| *)(?:([A-Za-z0-9]+)\\-[0-9a-f]{32}|([A-Za-z0-9\\-]+)(?:UCBrowser)|([A-Za-z0-9\\-]+))',
      'device_replacement' => 'Nokia $1$2$3',
      'brand_replacement' => 'Nokia',
      'model_replacement' => '$1$2$3',
    ),
    481 =>
    array (
      'regex' => 'Lumia ([A-Za-z0-9\\-]+)',
      'device_replacement' => 'Lumia $1',
      'brand_replacement' => 'Nokia',
      'model_replacement' => 'Lumia $1',
    ),
    482 =>
    array (
      'regex' => '\\(Symbian; U; S60 V5; [A-z]{2}\\-[A-z]{2}; (SonyEricsson|Samsung|Nokia|LG)([^;/]+)\\)',
      'device_replacement' => '$1 $2',
      'brand_replacement' => '$1',
      'model_replacement' => '$2',
    ),
    483 =>
    array (
      'regex' => '\\(Symbian(?:/3)?; U; ([^;]+);',
      'device_replacement' => 'Nokia $1',
      'brand_replacement' => 'Nokia',
      'model_replacement' => '$1',
    ),
    484 =>
    array (
      'regex' => 'BB10; ([A-Za-z0-9\\- ]+)\\)',
      'device_replacement' => 'BlackBerry $1',
      'brand_replacement' => 'BlackBerry',
      'model_replacement' => '$1',
    ),
    485 =>
    array (
      'regex' => 'Play[Bb]ook.+RIM Tablet OS',
      'device_replacement' => 'BlackBerry Playbook',
      'brand_replacement' => 'BlackBerry',
      'model_replacement' => 'Playbook',
    ),
    486 =>
    array (
      'regex' => 'Black[Bb]erry ([0-9]+);',
      'device_replacement' => 'BlackBerry $1',
      'brand_replacement' => 'BlackBerry',
      'model_replacement' => '$1',
    ),
    487 =>
    array (
      'regex' => 'Black[Bb]erry([0-9]+)',
      'device_replacement' => 'BlackBerry $1',
      'brand_replacement' => 'BlackBerry',
      'model_replacement' => '$1',
    ),
    488 =>
    array (
      'regex' => 'Black[Bb]erry;',
      'device_replacement' => 'BlackBerry',
      'brand_replacement' => 'BlackBerry',
    ),
    489 =>
    array (
      'regex' => '(Pre|Pixi)/\\d+\\.\\d+',
      'device_replacement' => 'Palm $1',
      'brand_replacement' => 'Palm',
      'model_replacement' => '$1',
    ),
    490 =>
    array (
      'regex' => 'Palm([0-9]+)',
      'device_replacement' => 'Palm $1',
      'brand_replacement' => 'Palm',
      'model_replacement' => '$1',
    ),
    491 =>
    array (
      'regex' => 'Treo([A-Za-z0-9]+)',
      'device_replacement' => 'Palm Treo $1',
      'brand_replacement' => 'Palm',
      'model_replacement' => 'Treo $1',
    ),
    492 =>
    array (
      'regex' => 'webOS.*(P160U(?:NA)?)/(\\d+).(\\d+)',
      'device_replacement' => 'HP Veer',
      'brand_replacement' => 'HP',
      'model_replacement' => 'Veer',
    ),
    493 =>
    array (
      'regex' => '(Touch[Pp]ad)/\\d+\\.\\d+',
      'device_replacement' => 'HP TouchPad',
      'brand_replacement' => 'HP',
      'model_replacement' => 'TouchPad',
    ),
    494 =>
    array (
      'regex' => 'HPiPAQ([A-Za-z0-9]+)/\\d+.\\d+',
      'device_replacement' => 'HP iPAQ $1',
      'brand_replacement' => 'HP',
      'model_replacement' => 'iPAQ $1',
    ),
    495 =>
    array (
      'regex' => 'PDA; (PalmOS)/sony/model ([a-z]+)/Revision',
      'device_replacement' => '$1',
      'brand_replacement' => 'Sony',
      'model_replacement' => '$1 $2',
    ),
    496 =>
    array (
      'regex' => '(Apple\\s?TV)',
      'device_replacement' => 'AppleTV',
      'brand_replacement' => 'Apple',
      'model_replacement' => 'AppleTV',
    ),
    497 =>
    array (
      'regex' => '(QtCarBrowser)',
      'device_replacement' => 'Tesla Model S',
      'brand_replacement' => 'Tesla',
      'model_replacement' => 'Model S',
    ),
    498 =>
    array (
      'regex' => '(iPhone|iPad|iPod)(\\d+,\\d+)',
      'device_replacement' => '$1',
      'brand_replacement' => 'Apple',
      'model_replacement' => '$1$2',
    ),
    499 =>
    array (
      'regex' => '(iPad)(?:;| Simulator;)',
      'device_replacement' => '$1',
      'brand_replacement' => 'Apple',
      'model_replacement' => '$1',
    ),
    500 =>
    array (
      'regex' => '(iPod)(?:;| touch;| Simulator;)',
      'device_replacement' => '$1',
      'brand_replacement' => 'Apple',
      'model_replacement' => '$1',
    ),
    501 =>
    array (
      'regex' => '(iPhone)(?:;| Simulator;)',
      'device_replacement' => '$1',
      'brand_replacement' => 'Apple',
      'model_replacement' => '$1',
    ),
    502 =>
    array (
      'regex' => 'CFNetwork/.* Darwin/\\d.*\\(((?:Mac|iMac|PowerMac|PowerBook)[^\\d]*)(\\d+)(?:,|%2C)(\\d+)',
      'device_replacement' => '$1$2,$3',
      'brand_replacement' => 'Apple',
      'model_replacement' => '$1$2,$3',
    ),
    503 =>
    array (
      'regex' => 'CFNetwork/.* Darwin/\\d',
      'device_replacement' => 'iOS-Device',
      'brand_replacement' => 'Apple',
      'model_replacement' => 'iOS-Device',
    ),
    504 =>
    array (
      'regex' => 'acer_([A-Za-z0-9]+)_',
      'device_replacement' => 'Acer $1',
      'brand_replacement' => 'Acer',
      'model_replacement' => '$1',
    ),
    505 =>
    array (
      'regex' => '(?:ALCATEL|Alcatel)-([A-Za-z0-9\\-]+)',
      'device_replacement' => 'Alcatel $1',
      'brand_replacement' => 'Alcatel',
      'model_replacement' => '$1',
    ),
    506 =>
    array (
      'regex' => '(?:Amoi|AMOI)\\-([A-Za-z0-9]+)',
      'device_replacement' => 'Amoi $1',
      'brand_replacement' => 'Amoi',
      'model_replacement' => '$1',
    ),
    507 =>
    array (
      'regex' => '(?:; |\\/|^)((?:Transformer (?:Pad|Prime) |Transformer |PadFone[ _]?)[A-Za-z0-9]*)',
      'device_replacement' => 'Asus $1',
      'brand_replacement' => 'Asus',
      'model_replacement' => '$1',
    ),
    508 =>
    array (
      'regex' => '(?:asus.*?ASUS|Asus|ASUS|asus)[\\- ;]*((?:Transformer (?:Pad|Prime) |Transformer |Padfone |Nexus[ _])?[A-Za-z0-9]+)',
      'device_replacement' => 'Asus $1',
      'brand_replacement' => 'Asus',
      'model_replacement' => '$1',
    ),
    509 =>
    array (
      'regex' => '\\bBIRD[ \\-\\.]([A-Za-z0-9]+)',
      'device_replacement' => 'Bird $1',
      'brand_replacement' => 'Bird',
      'model_replacement' => '$1',
    ),
    510 =>
    array (
      'regex' => '\\bDell ([A-Za-z0-9]+)',
      'device_replacement' => 'Dell $1',
      'brand_replacement' => 'Dell',
      'model_replacement' => '$1',
    ),
    511 =>
    array (
      'regex' => 'DoCoMo/2\\.0 ([A-Za-z0-9]+)',
      'device_replacement' => 'DoCoMo $1',
      'brand_replacement' => 'DoCoMo',
      'model_replacement' => '$1',
    ),
    512 =>
    array (
      'regex' => '([A-Za-z0-9]+)_W;FOMA',
      'device_replacement' => 'DoCoMo $1',
      'brand_replacement' => 'DoCoMo',
      'model_replacement' => '$1',
    ),
    513 =>
    array (
      'regex' => '([A-Za-z0-9]+);FOMA',
      'device_replacement' => 'DoCoMo $1',
      'brand_replacement' => 'DoCoMo',
      'model_replacement' => '$1',
    ),
    514 =>
    array (
      'regex' => '\\b(?:HTC/|HTC/[a-z0-9]+/)?HTC[ _\\-;]? *(.*?)(?:-?Mozilla|fingerPrint|[;/\\(\\)]|$)',
      'device_replacement' => 'HTC $1',
      'brand_replacement' => 'HTC',
      'model_replacement' => '$1',
    ),
    515 =>
    array (
      'regex' => 'Huawei([A-Za-z0-9]+)',
      'device_replacement' => 'Huawei $1',
      'brand_replacement' => 'Huawei',
      'model_replacement' => '$1',
    ),
    516 =>
    array (
      'regex' => 'HUAWEI-([A-Za-z0-9]+)',
      'device_replacement' => 'Huawei $1',
      'brand_replacement' => 'Huawei',
      'model_replacement' => '$1',
    ),
    517 =>
    array (
      'regex' => 'vodafone([A-Za-z0-9]+)',
      'device_replacement' => 'Huawei Vodafone $1',
      'brand_replacement' => 'Huawei',
      'model_replacement' => 'Vodafone $1',
    ),
    518 =>
    array (
      'regex' => 'i\\-mate ([A-Za-z0-9]+)',
      'device_replacement' => 'i-mate $1',
      'brand_replacement' => 'i-mate',
      'model_replacement' => '$1',
    ),
    519 =>
    array (
      'regex' => 'Kyocera\\-([A-Za-z0-9]+)',
      'device_replacement' => 'Kyocera $1',
      'brand_replacement' => 'Kyocera',
      'model_replacement' => '$1',
    ),
    520 =>
    array (
      'regex' => 'KWC\\-([A-Za-z0-9]+)',
      'device_replacement' => 'Kyocera $1',
      'brand_replacement' => 'Kyocera',
      'model_replacement' => '$1',
    ),
    521 =>
    array (
      'regex' => 'Lenovo[_\\-]([A-Za-z0-9]+)',
      'device_replacement' => 'Lenovo $1',
      'brand_replacement' => 'Lenovo',
      'model_replacement' => '$1',
    ),
    522 =>
    array (
      'regex' => '(HbbTV)/[0-9]+\\.[0-9]+\\.[0-9]+ \\([^;]*; *(LG)E *; *([^;]*) *;[^;]*;[^;]*;\\)',
      'device_replacement' => '$1',
      'brand_replacement' => '$2',
      'model_replacement' => '$3',
    ),
    523 =>
    array (
      'regex' => '(HbbTV)/1\\.1\\.1.*CE-HTML/1\\.\\d;(Vendor/)*(THOM[^;]*?)[;\\s](?:.*SW-Version/.*)*(LF[^;]+);?',
      'device_replacement' => '$1',
      'brand_replacement' => 'Thomson',
      'model_replacement' => '$4',
    ),
    524 =>
    array (
      'regex' => '(HbbTV)(?:/1\\.1\\.1)?(?: ?\\(;;;;;\\))?; *CE-HTML(?:/1\\.\\d)?; *([^ ]+) ([^;]+);',
      'device_replacement' => '$1',
      'brand_replacement' => '$2',
      'model_replacement' => '$3',
    ),
    525 =>
    array (
      'regex' => '(HbbTV)/1\\.1\\.1 \\(;;;;;\\) Maple_2011',
      'device_replacement' => '$1',
      'brand_replacement' => 'Samsung',
    ),
    526 =>
    array (
      'regex' => '(HbbTV)/[0-9]+\\.[0-9]+\\.[0-9]+ \\([^;]*; *(?:CUS:([^;]*)|([^;]+)) *; *([^;]*) *;.*;',
      'device_replacement' => '$1',
      'brand_replacement' => '$2$3',
      'model_replacement' => '$4',
    ),
    527 =>
    array (
      'regex' => '(HbbTV)/[0-9]+\\.[0-9]+\\.[0-9]+',
      'device_replacement' => '$1',
    ),
    528 =>
    array (
      'regex' => 'LGE; (?:Media\\/)?([^;]*);[^;]*;[^;]*;?\\); "?LG NetCast(\\.TV|\\.Media|)-\\d+',
      'device_replacement' => 'NetCast$2',
      'brand_replacement' => 'LG',
      'model_replacement' => '$1',
    ),
    529 =>
    array (
      'regex' => 'InettvBrowser/[0-9]+\\.[0-9A-Z]+ \\([^;]*;(Sony)([^;]*);[^;]*;[^\\)]*\\)',
      'device_replacement' => 'Inettv',
      'brand_replacement' => '$1',
      'model_replacement' => '$2',
    ),
    530 =>
    array (
      'regex' => 'InettvBrowser/[0-9]+\\.[0-9A-Z]+ \\([^;]*;([^;]*);[^;]*;[^\\)]*\\)',
      'device_replacement' => 'Inettv',
      'brand_replacement' => 'Generic_Inettv',
      'model_replacement' => '$1',
    ),
    531 =>
    array (
      'regex' => '(?:InettvBrowser|TSBNetTV|NETTV|HBBTV)',
      'device_replacement' => 'Inettv',
      'brand_replacement' => 'Generic_Inettv',
    ),
    532 =>
    array (
      'regex' => 'Series60/\\d\\.\\d (LG)[\\-]?([A-Za-z0-9 \\-]+)',
      'device_replacement' => '$1 $2',
      'brand_replacement' => '$1',
      'model_replacement' => '$2',
    ),
    533 =>
    array (
      'regex' => '\\b(?:LGE[ \\-]LG\\-(?:AX)?|LGE |LGE?-LG|LGE?[ \\-]|LG[ /\\-]|lg[\\-])([A-Za-z0-9]+)\\b',
      'device_replacement' => 'LG $1',
      'brand_replacement' => 'LG',
      'model_replacement' => '$1',
    ),
    534 =>
    array (
      'regex' => '(?:^LG[\\-]?|^LGE[\\-/]?)([A-Za-z]+[0-9]+[A-Za-z]*)',
      'device_replacement' => 'LG $1',
      'brand_replacement' => 'LG',
      'model_replacement' => '$1',
    ),
    535 =>
    array (
      'regex' => '^LG([0-9]+[A-Za-z]*)',
      'device_replacement' => 'LG $1',
      'brand_replacement' => 'LG',
      'model_replacement' => '$1',
    ),
    536 =>
    array (
      'regex' => '(KIN\\.[^ ]+) (\\d+)\\.(\\d+)',
      'device_replacement' => 'Microsoft $1',
      'brand_replacement' => 'Microsoft',
      'model_replacement' => '$1',
    ),
    537 =>
    array (
      'regex' => '(?:MSIE|XBMC).*\\b(Xbox)\\b',
      'device_replacement' => '$1',
      'brand_replacement' => 'Microsoft',
      'model_replacement' => '$1',
    ),
    538 =>
    array (
      'regex' => '; ARM; Trident/6\\.0; Touch[\\);]',
      'device_replacement' => 'Microsoft Surface RT',
      'brand_replacement' => 'Microsoft',
      'model_replacement' => 'Surface RT',
    ),
    539 =>
    array (
      'regex' => 'Motorola\\-([A-Za-z0-9]+)',
      'device_replacement' => 'Motorola $1',
      'brand_replacement' => 'Motorola',
      'model_replacement' => '$1',
    ),
    540 =>
    array (
      'regex' => 'MOTO\\-([A-Za-z0-9]+)',
      'device_replacement' => 'Motorola $1',
      'brand_replacement' => 'Motorola',
      'model_replacement' => '$1',
    ),
    541 =>
    array (
      'regex' => 'MOT\\-([A-z0-9][A-z0-9\\-]*)',
      'device_replacement' => 'Motorola $1',
      'brand_replacement' => 'Motorola',
      'model_replacement' => '$1',
    ),
    542 =>
    array (
      'regex' => 'Nintendo WiiU',
      'device_replacement' => 'Nintendo Wii U',
      'brand_replacement' => 'Nintendo',
      'model_replacement' => 'Wii U',
    ),
    543 =>
    array (
      'regex' => 'Nintendo (DS|3DS|DSi|Wii);',
      'device_replacement' => 'Nintendo $1',
      'brand_replacement' => 'Nintendo',
      'model_replacement' => '$1',
    ),
    544 =>
    array (
      'regex' => '(?:Pantech|PANTECH)[ _-]?([A-Za-z0-9\\-]+)',
      'device_replacement' => 'Pantech $1',
      'brand_replacement' => 'Pantech',
      'model_replacement' => '$1',
    ),
    545 =>
    array (
      'regex' => 'Philips([A-Za-z0-9]+)',
      'device_replacement' => 'Philips $1',
      'brand_replacement' => 'Philips',
      'model_replacement' => '$1',
    ),
    546 =>
    array (
      'regex' => 'Philips ([A-Za-z0-9]+)',
      'device_replacement' => 'Philips $1',
      'brand_replacement' => 'Philips',
      'model_replacement' => '$1',
    ),
    547 =>
    array (
      'regex' => '(SMART-TV); .* Tizen ',
      'device_replacement' => 'Samsung $1',
      'brand_replacement' => 'Samsung',
      'model_replacement' => '$1',
    ),
    548 =>
    array (
      'regex' => 'SymbianOS/9\\.\\d.* Samsung[/\\-]([A-Za-z0-9 \\-]+)',
      'device_replacement' => 'Samsung $1',
      'brand_replacement' => 'Samsung',
      'model_replacement' => '$1',
    ),
    549 =>
    array (
      'regex' => '(Samsung)(SGH)(i[0-9]+)',
      'device_replacement' => '$1 $2$3',
      'brand_replacement' => '$1',
      'model_replacement' => '$2-$3',
    ),
    550 =>
    array (
      'regex' => 'SAMSUNG-ANDROID-MMS/([^;/]+)',
      'device_replacement' => '$1',
      'brand_replacement' => 'Samsung',
      'model_replacement' => '$1',
    ),
    551 =>
    array (
      'regex' => 'SAMSUNG(?:; |[ -/])([A-Za-z0-9\\-]+)',
      'regex_flag' => 'i',
      'device_replacement' => 'Samsung $1',
      'brand_replacement' => 'Samsung',
      'model_replacement' => '$1',
    ),
    552 =>
    array (
      'regex' => '(Dreamcast)',
      'device_replacement' => 'Sega $1',
      'brand_replacement' => 'Sega',
      'model_replacement' => '$1',
    ),
    553 =>
    array (
      'regex' => '^SIE-([A-Za-z0-9]+)',
      'device_replacement' => 'Siemens $1',
      'brand_replacement' => 'Siemens',
      'model_replacement' => '$1',
    ),
    554 =>
    array (
      'regex' => 'Softbank/[12]\\.0/([A-Za-z0-9]+)',
      'device_replacement' => 'Softbank $1',
      'brand_replacement' => 'Softbank',
      'model_replacement' => '$1',
    ),
    555 =>
    array (
      'regex' => 'SonyEricsson ?([A-Za-z0-9\\-]+)',
      'device_replacement' => 'Ericsson $1',
      'brand_replacement' => 'SonyEricsson',
      'model_replacement' => '$1',
    ),
    556 =>
    array (
      'regex' => 'Android [^;]+; ([^ ]+) (Sony)/',
      'device_replacement' => '$2 $1',
      'brand_replacement' => '$2',
      'model_replacement' => '$1',
    ),
    557 =>
    array (
      'regex' => '(Sony)(?:BDP\\/|\\/)?([^ /;\\)]+)[ /;\\)]',
      'device_replacement' => '$1 $2',
      'brand_replacement' => '$1',
      'model_replacement' => '$2',
    ),
    558 =>
    array (
      'regex' => 'Puffin/[\\d\\.]+IT',
      'device_replacement' => 'iPad',
      'brand_replacement' => 'Apple',
      'model_replacement' => 'iPad',
    ),
    559 =>
    array (
      'regex' => 'Puffin/[\\d\\.]+IP',
      'device_replacement' => 'iPhone',
      'brand_replacement' => 'Apple',
      'model_replacement' => 'iPhone',
    ),
    560 =>
    array (
      'regex' => 'Puffin/[\\d\\.]+AT',
      'device_replacement' => 'Generic Tablet',
      'brand_replacement' => 'Generic',
      'model_replacement' => 'Tablet',
    ),
    561 =>
    array (
      'regex' => 'Puffin/[\\d\\.]+AP',
      'device_replacement' => 'Generic Smartphone',
      'brand_replacement' => 'Generic',
      'model_replacement' => 'Smartphone',
    ),
    562 =>
    array (
      'regex' => 'Android[\\- ][\\d]+\\.[\\d]+; [A-Za-z]{2}\\-[A-Za-z]{0,2}; WOWMobile (.+) Build[/ ]',
      'brand_replacement' => 'Generic_Android',
      'model_replacement' => '$1',
    ),
    563 =>
    array (
      'regex' => 'Android[\\- ][\\d]+\\.[\\d]+\\-update1; [A-Za-z]{2}\\-[A-Za-z]{0,2} *; *(.+?) Build[/ ]',
      'brand_replacement' => 'Generic_Android',
      'model_replacement' => '$1',
    ),
    564 =>
    array (
      'regex' => 'Android[\\- ][\\d]+(?:\\.[\\d]+){1,2}; *[A-Za-z]{2}[_\\-][A-Za-z]{0,2}\\-? *; *(.+?) Build[/ ]',
      'brand_replacement' => 'Generic_Android',
      'model_replacement' => '$1',
    ),
    565 =>
    array (
      'regex' => 'Android[\\- ][\\d]+(?:\\.[\\d]+){1,2}; *[A-Za-z]{0,2}\\- *; *(.+?) Build[/ ]',
      'brand_replacement' => 'Generic_Android',
      'model_replacement' => '$1',
    ),
    566 =>
    array (
      'regex' => 'Android[\\- ][\\d]+(?:\\.[\\d]+){1,2}; *[a-z]{0,2}[_\\-]?[A-Za-z]{0,2};? Build[/ ]',
      'device_replacement' => 'Generic Smartphone',
      'brand_replacement' => 'Generic',
      'model_replacement' => 'Smartphone',
    ),
    567 =>
    array (
      'regex' => 'Android[\\- ][\\d]+(?:\\.[\\d]+){1,2}; *\\-?[A-Za-z]{2}; *(.+?) Build[/ ]',
      'brand_replacement' => 'Generic_Android',
      'model_replacement' => '$1',
    ),
    568 =>
    array (
      'regex' => 'Android[\\- ][\\d]+(?:\\.[\\d]+){1,2}(?:;.*)?; *(.+?) Build[/ ]',
      'brand_replacement' => 'Generic_Android',
      'model_replacement' => '$1',
    ),
    569 =>
    array (
      'regex' => '(GoogleTV)',
      'brand_replacement' => 'Generic_Inettv',
      'model_replacement' => '$1',
    ),
    570 =>
    array (
      'regex' => '(WebTV)/\\d+.\\d+',
      'brand_replacement' => 'Generic_Inettv',
      'model_replacement' => '$1',
    ),
    571 =>
    array (
      'regex' => '^(Roku)/DVP-\\d+\\.\\d+',
      'brand_replacement' => 'Generic_Inettv',
      'model_replacement' => '$1',
    ),
    572 =>
    array (
      'regex' => '(Android 3\\.\\d|Opera Tablet|Tablet; .+Firefox/|Android.*(?:Tab|Pad))',
      'regex_flag' => 'i',
      'device_replacement' => 'Generic Tablet',
      'brand_replacement' => 'Generic',
      'model_replacement' => 'Tablet',
    ),
    573 =>
    array (
      'regex' => '(Symbian|\\bS60(Version|V\\d)|\\bS60\\b|\\((Series 60|Windows Mobile|Palm OS|Bada); Opera Mini|Windows CE|Opera Mobi|BREW|Brew|Mobile; .+Firefox/|iPhone OS|Android|MobileSafari|Windows *Phone|\\(webOS/|PalmOS)',
      'device_replacement' => 'Generic Smartphone',
      'brand_replacement' => 'Generic',
      'model_replacement' => 'Smartphone',
    ),
    574 =>
    array (
      'regex' => '(hiptop|avantgo|plucker|xiino|blazer|elaine)',
      'regex_flag' => 'i',
      'device_replacement' => 'Generic Smartphone',
      'brand_replacement' => 'Generic',
      'model_replacement' => 'Smartphone',
    ),
    575 =>
    array (
      'regex' => '(bot|zao|borg|DBot|oegp|silk|Xenu|zeal|^NING|CCBot|crawl|htdig|lycos|slurp|teoma|voila|yahoo|Sogou|CiBra|Nutch|^Java/|^JNLP/|Daumoa|Genieo|ichiro|larbin|pompos|Scrapy|snappy|speedy|spider|msnbot|msrbot|vortex|^vortex|crawler|favicon|indexer|Riddler|scooter|scraper|scrubby|WhatWeb|WinHTTP|bingbot|BingPreview|openbot|gigabot|furlbot|polybot|seekbot|^voyager|archiver|Icarus6j|mogimogi|Netvibes|blitzbot|altavista|charlotte|findlinks|Retreiver|TLSProber|WordPress|SeznamBot|ProoXiBot|wsr\\-agent|Squrl Java|EtaoSpider|PaperLiBot|SputnikBot|A6\\-Indexer|netresearch|searchsight|baiduspider|YisouSpider|ICC\\-Crawler|http%20client|Python-urllib|dataparksearch|converacrawler|Screaming Frog|AppEngine-Google|YahooCacheSystem|fast\\-webcrawler|Sogou Pic Spider|semanticdiscovery|Innovazion Crawler|facebookexternalhit|Google.*/\\+/web/snippet|Google-HTTP-Java-Client|BlogBridge|IlTrovatore-Setaccio|InternetArchive|GomezAgent|WebThumbnail|heritrix|NewsGator|PagePeeker|Reaper|ZooShot|holmes|NL-Crawler|Pingdom)',
      'regex_flag' => 'i',
      'device_replacement' => 'Spider',
      'brand_replacement' => 'Spider',
      'model_replacement' => 'Desktop',
    ),
    576 =>
    array (
      'regex' => '^(1207|3gso|4thp|501i|502i|503i|504i|505i|506i|6310|6590|770s|802s|a wa|acer|acs\\-|airn|alav|asus|attw|au\\-m|aur |aus |abac|acoo|aiko|alco|alca|amoi|anex|anny|anyw|aptu|arch|argo|bmobile|bell|bird|bw\\-n|bw\\-u|beck|benq|bilb|blac|c55/|cdm\\-|chtm|capi|comp|cond|dall|dbte|dc\\-s|dica|ds\\-d|ds12|dait|devi|dmob|doco|dopo|dorado|el(?:38|39|48|49|50|55|58|68)|el[3456]\\d{2}dual|erk0|esl8|ex300|ez40|ez60|ez70|ezos|ezze|elai|emul|eric|ezwa|fake|fly\\-|fly_|g\\-mo|g1 u|g560|gf\\-5|grun|gene|go.w|good|grad|hcit|hd\\-m|hd\\-p|hd\\-t|hei\\-|hp i|hpip|hs\\-c|htc |htc\\-|htca|htcg)',
      'regex_flag' => 'i',
      'device_replacement' => 'Generic Feature Phone',
      'brand_replacement' => 'Generic',
      'model_replacement' => 'Feature Phone',
    ),
    577 =>
    array (
      'regex' => '^(htcp|htcs|htct|htc_|haie|hita|huaw|hutc|i\\-20|i\\-go|i\\-ma|i\\-mobile|i230|iac|iac\\-|iac/|ig01|im1k|inno|iris|jata|kddi|kgt|kgt/|kpt |kwc\\-|klon|lexi|lg g|lg\\-a|lg\\-b|lg\\-c|lg\\-d|lg\\-f|lg\\-g|lg\\-k|lg\\-l|lg\\-m|lg\\-o|lg\\-p|lg\\-s|lg\\-t|lg\\-u|lg\\-w|lg/k|lg/l|lg/u|lg50|lg54|lge\\-|lge/|leno|m1\\-w|m3ga|m50/|maui|mc01|mc21|mcca|medi|meri|mio8|mioa|mo01|mo02|mode|modo|mot |mot\\-|mt50|mtp1|mtv |mate|maxo|merc|mits|mobi|motv|mozz|n100|n101|n102|n202|n203|n300|n302|n500|n502|n505|n700|n701|n710|nec\\-|nem\\-|newg|neon)',
      'regex_flag' => 'i',
      'device_replacement' => 'Generic Feature Phone',
      'brand_replacement' => 'Generic',
      'model_replacement' => 'Feature Phone',
    ),
    578 =>
    array (
      'regex' => '^(netf|noki|nzph|o2 x|o2\\-x|opwv|owg1|opti|oran|ot\\-s|p800|pand|pg\\-1|pg\\-2|pg\\-3|pg\\-6|pg\\-8|pg\\-c|pg13|phil|pn\\-2|pt\\-g|palm|pana|pire|pock|pose|psio|qa\\-a|qc\\-2|qc\\-3|qc\\-5|qc\\-7|qc07|qc12|qc21|qc32|qc60|qci\\-|qwap|qtek|r380|r600|raks|rim9|rove|s55/|sage|sams|sc01|sch\\-|scp\\-|sdk/|se47|sec\\-|sec0|sec1|semc|sgh\\-|shar|sie\\-|sk\\-0|sl45|slid|smb3|smt5|sp01|sph\\-|spv |spv\\-|sy01|samm|sany|sava|scoo|send|siem|smar|smit|soft|sony|t\\-mo|t218|t250|t600|t610|t618|tcl\\-|tdg\\-|telm|tim\\-|ts70|tsm\\-|tsm3|tsm5|tx\\-9|tagt)',
      'regex_flag' => 'i',
      'device_replacement' => 'Generic Feature Phone',
      'brand_replacement' => 'Generic',
      'model_replacement' => 'Feature Phone',
    ),
    579 =>
    array (
      'regex' => '^(talk|teli|topl|tosh|up.b|upg1|utst|v400|v750|veri|vk\\-v|vk40|vk50|vk52|vk53|vm40|vx98|virg|vertu|vite|voda|vulc|w3c |w3c\\-|wapj|wapp|wapu|wapm|wig |wapi|wapr|wapv|wapy|wapa|waps|wapt|winc|winw|wonu|x700|xda2|xdag|yas\\-|your|zte\\-|zeto|aste|audi|avan|blaz|brew|brvw|bumb|ccwa|cell|cldc|cmd\\-|dang|eml2|fetc|hipt|http|ibro|idea|ikom|ipaq|jbro|jemu|jigs|keji|kyoc|kyok|libw|m\\-cr|midp|mmef|moto|mwbp|mywa|newt|nok6|o2im|pant|pdxg|play|pluc|port|prox|rozo|sama|seri|smal|symb|treo|upsi|vx52|vx53|vx60|vx61|vx70|vx80|vx81|vx83|vx85|wap\\-|webc|whit|wmlb|xda\\-|xda_)',
      'regex_flag' => 'i',
      'device_replacement' => 'Generic Feature Phone',
      'brand_replacement' => 'Generic',
      'model_replacement' => 'Feature Phone',
    ),
    580 =>
    array (
      'regex' => '^(Ice)$',
      'device_replacement' => 'Generic Feature Phone',
      'brand_replacement' => 'Generic',
      'model_replacement' => 'Feature Phone',
    ),
    581 =>
    array (
      'regex' => '(wap[\\-\\ ]browser|maui|netfront|obigo|teleca|up\\.browser|midp|Opera Mini)',
      'regex_flag' => 'i',
      'device_replacement' => 'Generic Feature Phone',
      'brand_replacement' => 'Generic',
      'model_replacement' => 'Feature Phone',
    ),
  ),
);