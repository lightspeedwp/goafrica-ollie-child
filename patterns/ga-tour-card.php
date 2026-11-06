<?php
/**
 * Title: Tour Card
 * Slug: ga-tour-card
 * Inserter: yes
 *
 * @package goafrica-ollie-child
 */

?>
<!-- wp:group {"metadata":{"name":"Tour Card","patternName":"go-africa-ollie-child/ga-tour-card"},"className":"overflow-hidden is-style-ga-tour-card","style":{"spacing":{"blockGap":"0"},"border":{"radius":"0.5rem"}},"backgroundColor":"base","layout":{"type":"default"},"ariaLabel":"Tour Card","linkDestination":"post"} -->
<div aria-label="Tour Card" class="wp-block-group overflow-hidden is-style-ga-tour-card has-base-background-color has-background" style="border-radius:0.5rem"><!-- wp:cover {"useFeaturedImage":true,"dimRatio":0,"customOverlayColor":"#FFF","isUserOverlayColor":false,"contentPosition":"top left","isDark":false,"style":{"dimensions":{"aspectRatio":"3/2"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-cover is-light has-custom-content-position is-position-top-left"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim" style="background-color:#FFF"></span><div class="wp-block-cover__inner-container"><!-- wp:post-terms {"term":"travel-style","separator":"   ","className":"is-style-term-button ga-tour-card-badges","style":{"border":{"radius":{"topLeft":"0","topRight":"0","bottomLeft":"0","bottomRight":"0"}}}} /--></div></div>
<!-- /wp:cover -->

<!-- wp:group {"metadata":{"name":"Content"},"style":{"spacing":{"padding":{"right":"var:preset|spacing|small","left":"var:preset|spacing|small","top":"var:preset|spacing|small","bottom":"var:preset|spacing|small"},"blockGap":"var:preset|spacing|small"}},"layout":{"type":"default"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--small);padding-right:var(--wp--preset--spacing--small);padding-bottom:var(--wp--preset--spacing--small);padding-left:var(--wp--preset--spacing--small)"><!-- wp:group {"metadata":{"name":"Tour Info"},"style":{"spacing":{"blockGap":"10px","padding":{"bottom":"var:preset|spacing|small"}},"border":{"bottom":{"color":"var:preset|color|border-light","width":"1px"}}},"layout":{"type":"constrained","justifyContent":"left"}} -->
<div class="wp-block-group" style="border-bottom-color:var(--wp--preset--color--border-light);border-bottom-width:1px;padding-bottom:var(--wp--preset--spacing--small)"><!-- wp:group {"metadata":{"name":"Tour Title"},"className":"center-vertically","style":{"dimensions":{"minHeight":"0rem"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"left","verticalAlignment":"top"}} -->
<div class="wp-block-group center-vertically" style="min-height:0rem"><!-- wp:post-title {"textAlign":"left","level":3,"isLink":true,"style":{"elements":{"link":{":hover":{"color":{"text":"var:preset|color|primary-700"}},"color":{"text":"var:preset|color|main"}}}},"textColor":"main","fontSize":"medium"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"Tour Information"},"className":"lsx-tour-info","style":{"spacing":{"padding":{"left":"0","right":"0","top":"0","bottom":"0"},"blockGap":"5px"},"border":{"top":{"width":"0px","style":"none"},"bottom":{"width":"0px","style":"none"}}},"fontSize":"medium","layout":{"type":"default"},"ariaLabel":"Tour details"} -->
<div aria-label="Tour details" class="wp-block-group lsx-tour-info has-medium-font-size" style="border-top-style:none;border-top-width:0px;border-bottom-style:none;border-bottom-width:0px;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:group {"metadata":{"name":"Duration Row"},"className":"lsx-duration-wrapper","style":{"spacing":{"blockGap":"5px"},"elements":{"link":{"color":{"text":"var:preset|color|primary"},":hover":{"color":{"text":"var:preset|color|primary-alt"}}}}},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"center"}} -->
<div class="wp-block-group lsx-duration-wrapper has-link-color"><!-- wp:outermost/icon-block {"iconName":"","iconColor":"primary","iconColorValue":"#1F4B3B","width":"20px"} -->
<div class="wp-block-outermost-icon-block"><div class="icon-container has-icon-color has-primary-color" style="color:#1F4B3B;width:20px;transform:rotate(0deg) scaleX(1) scaleY(1)"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#000000" viewBox="0 0 256 256"><path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216Zm64-88a8,8,0,0,1-8,8H128a8,8,0,0,1-8-8V72a8,8,0,0,1,16,0v48h48A8,8,0,0,1,192,128Z"></path></svg></div></div>
<!-- /wp:outermost/icon-block -->

<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"lsx/post-meta","args":{"key":"duration"}},"__default":{"source":"core/pattern-overrides"}},"name":"Duration"},"fontSize":"small"} -->
<p class="has-small-font-size"></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size">dagen</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"Travel Styles Row","blockVisibility":false},"className":"lsx-travel-style-wrapper","style":{"spacing":{"blockGap":"5px"},"elements":{"link":{"color":{"text":"var:preset|color|primary"},":hover":{"color":{"text":"var:preset|color|primary-alt"}}}}},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"top"}} -->
<div class="wp-block-group lsx-travel-style-wrapper has-link-color"><!-- wp:outermost/icon-block {"iconName":"","iconColor":"primary","iconColorValue":"#1F4B3B","width":"20px","style":{"spacing":{"padding":{"top":"2px","bottom":"2px"}}}} -->
<div class="wp-block-outermost-icon-block"><div class="icon-container has-icon-color has-primary-color" style="color:#1F4B3B;width:20px;padding-top:2px;padding-bottom:2px;transform:rotate(0deg) scaleX(1) scaleY(1)"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#000000" viewBox="0 0 256 256"><path d="M243.31,136,144,36.69A15.86,15.86,0,0,0,132.69,32H40a8,8,0,0,0-8,8v92.69A15.86,15.86,0,0,0,36.69,144L136,243.31a16,16,0,0,0,22.63,0l84.68-84.68a16,16,0,0,0,0-22.63Zm-96,96L48,132.69V48h84.69L232,147.31ZM96,84A12,12,0,1,1,84,72,12,12,0,0,1,96,84Z"></path></svg></div></div>
<!-- /wp:outermost/icon-block -->

<!-- wp:post-terms {"term":"travel-style","fontSize":"small"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"Destination to Tour"},"className":"lsx-duration-wrapper","style":{"spacing":{"blockGap":"5px"},"elements":{"link":{"color":{"text":"var:preset|color|primary"},":hover":{"color":{"text":"var:preset|color|primary-alt"}}}}},"fontSize":"small","layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"top"}} -->
<div class="wp-block-group lsx-duration-wrapper has-link-color has-small-font-size"><!-- wp:outermost/icon-block {"iconName":"","iconColor":"primary","iconColorValue":"#1F4B3B","width":"20px","className":"lsx-info-label","style":{"spacing":{"blockGap":"5px","padding":{"top":"2px","bottom":"2px"}}}} -->
<div class="wp-block-outermost-icon-block lsx-info-label"><div class="icon-container has-icon-color has-primary-color" style="color:#1F4B3B;width:20px;padding-top:2px;padding-bottom:2px;transform:rotate(0deg) scaleX(1) scaleY(1)"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#000000" viewBox="0 0 256 256"><path d="M128,64a40,40,0,1,0,40,40A40,40,0,0,0,128,64Zm0,64a24,24,0,1,1,24-24A24,24,0,0,1,128,128Zm0-112a88.1,88.1,0,0,0-88,88c0,31.4,14.51,64.68,42,96.25a254.19,254.19,0,0,0,41.45,38.3,8,8,0,0,0,9.18,0A254.19,254.19,0,0,0,174,200.25c27.45-31.57,42-64.85,42-96.25A88.1,88.1,0,0,0,128,16Zm0,206c-16.53-13-72-60.75-72-118a72,72,0,0,1,144,0C200,161.23,144.53,209,128,222Z"></path></svg></div></div>
<!-- /wp:outermost/icon-block -->

<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"lsx/post-connection","args":{"key":"destination_to_tour","parents":"true"}}}},"className":"lsx-info-value"} -->
<p class="lsx-info-value"></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"verticalAlignment":"top"} -->
<div class="wp-block-column is-vertically-aligned-top"><!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"fontSize":"x-small"} -->
<p class="has-x-small-font-size">Vanaf</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"core/post-meta","args":{"key":"price"}}}},"style":{"elements":{"link":{"color":{"text":"var:preset|color|primary-alt-accent"}}}},"textColor":"primary-alt-accent","fontSize":"medium","prefix":"€"} -->
<p class="has-primary-alt-accent-color has-text-color has-link-color has-medium-font-size"> price</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"bottom"} -->
<div class="wp-block-column is-vertically-aligned-bottom"><!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"width":100,"metadata":{"name":"Permalink"},"className":"is-style-ga-cta-button","style":{"border":{"radius":{"topLeft":"var:preset|border-radius|sm","topRight":"var:preset|border-radius|sm","bottomLeft":"var:preset|border-radius|sm","bottomRight":"var:preset|border-radius|sm"}},"spacing":{"padding":{"left":"var:preset|spacing|small","right":"var:preset|spacing|small","top":"14px","bottom":"14px"}}},"fontSize":"small"} -->
<div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-ga-cta-button"><a class="wp-block-button__link has-small-font-size has-custom-font-size wp-element-button" href="#permalink" style="border-top-left-radius:var(--wp--preset--border-radius--sm);border-top-right-radius:var(--wp--preset--border-radius--sm);border-bottom-left-radius:var(--wp--preset--border-radius--sm);border-bottom-right-radius:var(--wp--preset--border-radius--sm);padding-top:14px;padding-right:var(--wp--preset--spacing--small);padding-bottom:14px;padding-left:var(--wp--preset--spacing--small)">Bekijk reis</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
