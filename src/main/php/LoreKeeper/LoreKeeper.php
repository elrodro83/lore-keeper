<?php

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
);

/* Setup */

// Initialize an easy to use shortcut:
$dir = dirname( __FILE__ );
$dirbasename = basename( $dir );

$wgAutoloadClasses['LoreKeeper'] = $dir . '/LoreKeeper.body.php';
$wgAutoloadClasses['Event'] = $dir . '/metadata/Event.php';
$wgAutoloadClasses['Timeline'] = $dir . '/metadata/Timeline.php';

$wgMessagesDirs['Example'] = __DIR__ . '/i18n';

$wgHooks['ParserFirstCallInit'][] = 'LoreKeeper::onParserFirstCallInit';

// Allow translation of the parser function name
$wgExtensionMessagesFiles['LoreKeeper'] = __DIR__ . '/LoreKeeper.i18n.php';