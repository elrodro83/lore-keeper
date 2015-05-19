# lore-keeper
MediaWiki extension for managing and presenting historical/fictional stories

This extension will parse event metadata from wiki pages. That metadata will contain information of the event such as:
* when it happened
* where it happened
* who/what was involved
* what did happen

Event pages should contain some markup to be processed by the extension, for instance:

    {{#event:when=3434|where=[[Barad-dûr]]|who=[[Sauron]]|who=[[Last Alliance]]|who=[[Gil-galad]]|who=[[Elendil]]|who=[[Isildur]]|what=[[The One Ring]]}}

Then, from a page for a character or a place, the extension will query the pages with event tags that reference the object of the page. Once that data is in place, it can be used for generating timelines (biographies for characters, history for places) using the timeline component: [https://github.com/NUKnightLab/TimelineJS]
