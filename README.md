# lore-keeper
MediaWiki extension for managing and presenting historical/fictional stories

This extension will parse event metadata from wiki pages. That metadata will contain information of the event such as:
* when it happened
* where it happened
* who/what was involved
* what did happen

Event pages should contain some markup to be processed by the extension, for instance:

    {{#event:when=1-ethuil-3434 Elf 2nd Age|where=[[Barad-dûr]]|who=[[Sauron]]|who=[[Last Alliance]]|who=[[Gil-galad]]|who=[[Elendil]]|who=[[Isildur]]|what=[[The One Ring]]}}

Then, from a page for a character or a place that includes the markup (Sauron, for instance):
    
    {{timeline:}}
    
, the extension will query the pages with event tags that reference the object of the page. Once that data is fetched, it will be used for generating timelines (biographies for characters, history for places) using the timeline component: [https://github.com/NUKnightLab/TimelineJS].

The wiki must have a *Calendars* page where the rules for parsing the *when* attribute are defined:

	{{#calendar:ethuil,spring,54;laer,summer,72;iavas,autumn,54;firith,fading,54;rhîw,winter,72;echuir,stirring,54|Elf 1st Age|-215350}}
	
* The first attribute is an enumeration of the months, with hoe long each one lasts.
* The second is the definition of the calendar. In this case it indicates that this is the Elf calendar for the 2nd age.
* The third one is the offset in days between the beginning of the era and the "epoch". For this example, I defined the epoch as being the start of the second age, so i put as the offset the negative duration in days of the First Age. This parameter allows for the creation of timelines composed of events that may have its dates based on different calendars.

## Disclaimer

### About example data

Examples and test data are taken from [The One Wiki to Rule Them All](http://lotr.wikia.com/wiki/Main_Page).
LoreKeeper mediawiki extension is Not in any way, shape, or form affiliated with [The One Wiki to Rule Them All](http://lotr.wikia.com/wiki/Main_Page), [Saul Zaentz](http://www.zaentz.com/), [Middle-earth Enterprises](http://www.middleearth.com/home.html), or the Tolkien Estate.
Copyrights and trademarks for the books, films, and other promotional materials are held by their respective owners and their use is allowed under the fair use clause of the Copyright Law.