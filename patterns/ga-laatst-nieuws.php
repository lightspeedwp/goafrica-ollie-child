<?php
/**
 * Title: Laatst Nieuws
 * Slug: ga-laatst-nieuws
 * Description: Version-controlled copy of the live synced pattern "Laatst Nieuws" (wp_block 88829 on goafrica.nl). Templates still reference the synced pattern; this file keeps the markup in the theme.
 * Categories: posts
 * Inserter: no
 *
 * @package goafrica-ollie-child
 */

?>
<!-- wp:group {"metadata":{"name":"Laatste nieuws \u0026 tips"},"className":"is-style-ga-page-section-default","style":{"spacing":{"blockGap":"var:preset|spacing|large"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group is-style-ga-page-section-default"><!-- wp:group {"metadata":{"name":"Header"},"style":{"spacing":{"padding":{"top":"0","bottom":"0"},"blockGap":"var:preset|spacing|small"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:0;padding-bottom:0"><!-- wp:paragraph {"className":"has-text-align-center is-style-ga-uppercase-bold has-primary-alt-accent-color has-text-color has-link-color","style":{"typography":{"textAlign":"center"},"elements":{"link":{"color":{"text":"var:preset|color|primary-alt-accent"}}}},"textColor":"primary-alt-accent"} -->
<p class="has-text-align-center is-style-ga-uppercase-bold has-primary-alt-accent-color has-text-color has-link-color">Nieuws</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"has-text-align-center","style":{"typography":{"textAlign":"center"},"elements":{"link":{"color":{"text":"var:preset|color|primary"}}}},"textColor":"primary"} -->
<h2 class="wp-block-heading has-text-align-center has-primary-color has-text-color has-link-color">Laatste nieuws &amp; tips</h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:query {"queryId":1,"query":{"perPage":6,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"taxQuery":null,"parents":[],"format":[]},"align":"wide"} -->
<div class="wp-block-query alignwide lsx-to-slider"><!-- wp:post-template {"layout":{"type":"grid","columnCount":null,"minimumColumnWidth":"22rem"}} -->
<!-- wp:group {"metadata":{"name":"Blog Card","patternName":"ga-blog-card"},"className":"overflow-hidden is-style-ga-tour-card","style":{"spacing":{"blockGap":"0"},"border":{"radius":"0.5rem"}},"layout":{"type":"default"},"ariaLabel":"Tour Card","linkDestination":"post"} -->
<div aria-label="Tour Card" class="wp-block-group overflow-hidden is-style-ga-tour-card" style="border-radius:0.5rem"><!-- wp:cover {"useFeaturedImage":true,"dimRatio":0,"customOverlayColor":"#f3ebef","isUserOverlayColor":false,"contentPosition":"top left","isDark":false,"style":{"dimensions":{"aspectRatio":"3/2"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-cover is-light has-custom-content-position is-position-top-left"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim" style="background-color:#f3ebef"></span><div class="wp-block-cover__inner-container"><!-- wp:paragraph {"placeholder":"Write title…","style":{"typography":{"textAlign":"center"}},"fontSize":"large"} -->
<p class="has-text-align-center has-large-font-size"></p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:cover -->

<!-- wp:group {"metadata":{"name":"Content"},"style":{"spacing":{"padding":{"right":"var:preset|spacing|small","left":"var:preset|spacing|small","top":"var:preset|spacing|small","bottom":"var:preset|spacing|small"},"blockGap":"var:preset|spacing|small"}},"layout":{"type":"default"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--small);padding-right:var(--wp--preset--spacing--small);padding-bottom:var(--wp--preset--spacing--small);padding-left:var(--wp--preset--spacing--small)"><!-- wp:post-terms {"term":"category","className":"is-style-default","style":{"typography":{"textTransform":"uppercase"},"elements":{"link":{"color":{"text":"var:preset|color|primary"},":hover":{"color":{"text":"var:preset|color|primary-alt-accent"}}}}},"fontSize":"x-small"} /-->

<!-- wp:group {"metadata":{"name":"Title"},"className":"center-vertically","style":{"dimensions":{"minHeight":"0rem"},"spacing":{"padding":{"top":"0","bottom":"0"}}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"left","verticalAlignment":"top"}} -->
<div class="wp-block-group center-vertically" style="min-height:0rem;padding-top:0;padding-bottom:0"><!-- wp:post-title {"textAlign":"left","level":3,"isLink":true,"style":{"elements":{"link":{":hover":{"color":{"text":"var:preset|color|primary-700"}}}},"typography":{"lineHeight":"1.25"}},"fontSize":"medium"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"Excerpt"},"className":"lsx-tour-info","style":{"spacing":{"padding":{"left":"0","right":"0","top":"0","bottom":"0"},"blockGap":"0"},"border":{"top":{"width":"0px","style":"none"},"bottom":{"width":"0px","style":"none"}}},"fontSize":"medium","layout":{"type":"default"},"ariaLabel":"Tour details"} -->
<div aria-label="Tour details" class="wp-block-group lsx-tour-info has-medium-font-size" style="border-top-style:none;border-top-width:0px;border-bottom-style:none;border-bottom-width:0px;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:post-excerpt {"excerptLength":25,"className":"is-style-excerpt-truncate-4","fontSize":"small"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
<!-- /wp:post-template --></div>
<!-- /wp:query -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-ga-primary-outline-button"} -->
<div class="wp-block-button is-style-ga-primary-outline-button"><a class="wp-block-button__link wp-element-button" href="/blog/">Lees alle artikelen</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->
