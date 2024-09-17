<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( 1 );
}

$wgExtensionCredits['other'][] = array(
		'path' => __FILE__,

		'name' => 'LoreKeeper',
		'author' => array(
				'Rodro (https://github.com/elrodro83)'
		),

		'version'  => '0.1.0',

		'url' => 'https://github.com/elrodro83/lore-keeper',

		# Key name of the message containing the description.
		'descriptionmsg' => 'lorekeeper-desc',
		'license-name'   => 'GPL-3.0+'
);

/* Setup */
$wgLoreKeeperCalendarPage = "Calendars";

// Initialize an easy to use shortcut:
$dir = dirname( __FILE__ );
$dirbasename = basename( $dir );

$wgAutoloadClasses['LoreKeeper'] = $dir . '/LoreKeeper.body.php';
$wgAutoloadClasses['PageFetchUtils'] = $dir . '/util/PageFetchUtils.php';
$wgAutoloadClasses['ParserUtils'] = $dir . '/util/ParserUtils.php';
$wgAutoloadClasses['Calendar'] = $dir . '/metadata/Calendar.php';
$wgAutoloadClasses['InPageCalendar'] = $dir . '/metadata/InPageCalendar.php';
$wgAutoloadClasses['LKDate'] = $dir . '/metadata/LKDate.php';
$wgAutoloadClasses['Event'] = $dir . '/metadata/Event.php';
$wgAutoloadClasses['Era'] = $dir . '/metadata/Era.php';
$wgAutoloadClasses['Timeline'] = $dir . '/metadata/Timeline.php';

$wgMessagesDirs['Example'] = __DIR__ . '/i18n';

$wgHooks['ParserFirstCallInit'][] = 'LoreKeeper::onParserFirstCallInit';

// Allow translation of the parser function name
$wgExtensionMessagesFiles['LoreKeeper'] = __DIR__ . '/LoreKeeper.i18n.php';