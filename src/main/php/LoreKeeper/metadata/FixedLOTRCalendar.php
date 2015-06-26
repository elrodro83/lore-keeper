<?php 

/**
 * This is just to cut the dependency on mediawiki in the unit tests.
 * The proper way to create a calendar is through the $wgLoreKeeperCalendarPage variable,
 * 	as explained in the documentation.
 *
 * @author rodro
 */
class FixedLOTRCalendar {
	
	public static function fetchCalendar($eraQualifier) {
	
		return new Calendar("ethuil,spring,54;laer,summer,72;iavas,autumn,54;firith,fading,54;rhîw,winter,72;echuir,stirring,54",
				$eraQualifier, 0);
	}
}
