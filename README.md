# userint2ssiesi

This is a typo3 extension which collects all uncached elements in a Page and replaces  them with either a Serverside Include Statement (SSI) (<!--#include virtual-->) for Nginx or Apache, or an Akamai/Cloudflare Style ESI Statement (<!--esi <esi:include src/>-->), which is understood by varnish as well.

The Configuration is pre-render stored in the cache, and then referenced by a separate pagetype that will be pulled by either the webserver or varnish ( or cloudflare ). The idea is to have a middleware cacheable page, with references to uncached elements inside the page, or elements with different caching lifecycles.

This plugin is heavily influenced by an extension Nicole Cordes ( https://github.com/IchHabRecht/ ) wrote in the scope of a customer project in context with varnish, where she gratiously gave some insights at the Typo3 Camp Stuttgart 2019 when we got to talk about similar strategies for speeding up delivery of highly dynamic content.

## Warning
this is very much work in progress and a proof of concept for now. It might break your site or data. Please be patient for a Release Version 1.0.

The scope of this extension WILL NOT be varnish management. There are better tools alreay out there.
This extension will may be have some cache management for the nginx caching proxy, but that as well might be another extension

Feedback and insights are very much welcome.

The TYPO3 project - inspiring people to share!
