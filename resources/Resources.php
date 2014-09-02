<?php

use Wikibase\Client\WikibaseClient;
use Wikibase\Repo\WikibaseRepo;

/**
 * File for Wikibase resourceloader modules.
 * When included this returns an array with all the modules introduced by Wikibase.
 *
 * @since 0.2
 *
 * @licence GNU GPL v2+
 * @author Daniel Werner
 * @author H. Snater < mediawiki@snater.com >
 *
 * @codeCoverageIgnoreStart
 */
return call_user_func( function() {
	$remoteExtPathParts = explode( DIRECTORY_SEPARATOR . 'extensions' . DIRECTORY_SEPARATOR , __DIR__, 2 );
	$moduleTemplate = array(
		'localBasePath' => __DIR__,
		'remoteExtPath' => $remoteExtPathParts[1],
	);

	$modules = array(
		// common styles independent from JavaScript being enabled or disabled
		'wikibase.common' => $moduleTemplate + array(
			'styles' => array(
				// Order must be hierarchical, do not order alphabetically
				'wikibase.css',
				'jquery.wikibase/themes/default/jquery.wikibase.labelview.css',
				'jquery.wikibase/themes/default/jquery.wikibase.descriptionview.css',
				'jquery.wikibase/themes/default/jquery.wikibase.aliasesview.css',
				'jquery.wikibase/themes/default/jquery.wikibase.sitelinklistview.css',
				'jquery.wikibase/themes/default/jquery.wikibase.sitelinkview.css',
			)
		),

		'wikibase.sites' => $moduleTemplate + array(
			'scripts' => array(
				'wikibase.sites.js',
			),
			'dependencies' => array(
				'mw.config.values.wbSiteDetails',
				'wikibase',
				'wikibase.Site',
			)
		),

		'wikibase.Site' => $moduleTemplate + array(
			'scripts' => array(
				'wikibase.Site.js',
			),
			'dependencies' => array(
				'mediawiki.util',
				'util.inherit',
				'wikibase',
			),
		),

		'mw.config.values.wbSiteDetails' => $moduleTemplate + array(
			'class' => 'Wikibase\SitesModule'
		),

		'mw.config.values.wbRepo' => $moduleTemplate + array(
			'class' => 'Wikibase\RepoAccessModule',
		),

		'wikibase' => $moduleTemplate + array(
			'scripts' => array(
				'wikibase.js',
				'wikibase.RevisionStore.js'
			),
			'dependencies' => array(
				'wikibase.common',
			),
			'messages' => array(
				'special-createitem',
				'wb-special-newitem-new-item-notification'
			)
		),

		'wikibase.dataTypes' => $moduleTemplate + array(
			'scripts' => array(
				'wikibase.dataTypes/wikibase.dataTypes.js',
			),
			'dependencies' => array(
				'dataTypes.DataType',
				'dataTypes.DataTypeStore',
				'mw.config.values.wbDataTypes',
				'wikibase',
			),
		),

		'mw.config.values.wbDataTypes' => $moduleTemplate + array(
			'class' => 'DataTypes\DataTypesModule',
			'datatypefactory' => function() {
				// TODO: relative uglynes here! Get rid of this method!
				if ( defined( 'WB_VERSION' ) ) { // repo mode
					$wikibase = WikibaseRepo::getDefaultInstance();
				} elseif ( defined( 'WBC_VERSION' ) ) { // client mode
					$wikibase = WikibaseClient::getDefaultInstance();
				} else {
					throw new \RuntimeException( "Neither repo nor client found!" );
				}
				return $wikibase->getDataTypeFactory();
			},
			'datatypesconfigvarname' => 'wbDataTypes',
		),

		'wikibase.store' => $moduleTemplate + array(
			'scripts' => array(
				'wikibase.store/store.js'
			),
			'dependencies' => array(
				'wikibase'
			)
		),

		'wikibase.store.FetchedContent' => $moduleTemplate + array(
			'scripts' => array(
				'wikibase.store/store.FetchedContent.js',
			),
			'dependencies' => array(
				'wikibase.store',
				'mediawiki.Title',
			)
		),

		'wikibase.store.FetchedContentUnserializer' => $moduleTemplate + array(
			'scripts' => array(
				'wikibase.store/store.FetchedContentUnserializer.js',
			),
			'dependencies' => array(
				'util.inherit',
				'wikibase.serialization', // For registering in the SerializerFactory
				'wikibase.store',
				'wikibase.store.FetchedContent',
			)
		),

		'wikibase.store.EntityStore' => $moduleTemplate + array(
			'scripts' => array(
				'wikibase.store/store.EntityStore.js',
			),
			'dependencies' => array(
				'mediawiki.Title',
				'wikibase.store',
				'wikibase.store.FetchedContent'
			)
		),

		'wikibase.compileEntityStoreFromMwConfig' => $moduleTemplate + array(
			'scripts' => array(
				'wikibase.compileEntityStoreFromMwConfig.js',
			),
			'dependencies' => array(
				'json',
				'wikibase',
				'wikibase.serialization',
				'wikibase.serialization.entities',
				'wikibase.store.FetchedContent',
				'wikibase.store.FetchedContentUnserializer',
				'wikibase.datamodel'
			)
		),

		'wikibase.AbstractedRepoApi' => $moduleTemplate + array(
			'scripts' => array(
				'wikibase.RepoApi/wikibase.AbstractedRepoApi.js',
			),
			'dependencies' => array(
				'util.inherit',
				'wikibase.datamodel',
				'wikibase.serialization.entities',
			)
		),

		'wikibase.RepoApi' => $moduleTemplate + array(
			'scripts' => array(
				'wikibase.RepoApi/wikibase.RepoApi.js',
			),
			'dependencies' => array(
				'json',
				'user.tokens',
				'mediawiki.api',
				'mw.config.values.wbRepo',
				'wikibase',
			)
		),

		'wikibase.RepoApiError' => $moduleTemplate + array(
			'scripts' => array(
				'wikibase.RepoApi/wikibase.RepoApiError.js',
			),
			'messages' => array(
				'wikibase-error-unexpected',
				'wikibase-error-save-generic',
				'wikibase-error-remove-generic',
				'wikibase-error-save-timeout',
				'wikibase-error-remove-timeout',
				'wikibase-error-ui-client-error',
				'wikibase-error-ui-no-external-page',
				'wikibase-error-ui-cant-edit',
				'wikibase-error-ui-no-permissions',
				'wikibase-error-ui-link-exists',
				'wikibase-error-ui-session-failure',
				'wikibase-error-ui-edit-conflict',
				'wikibase-error-ui-edit-conflict',
			),
			'dependencies' => array(
				'util.inherit',
				'wikibase',
			)
		),

		'wikibase.utilities' => $moduleTemplate + array(
			'scripts' => array(
				'wikibase.utilities/wikibase.utilities.js',
				'wikibase.utilities/wikibase.utilities.ui.js',
				'wikibase.utilities/wikibase.utilities.ui.StatableObject.js',
			),
			'styles' => array(
				'wikibase.utilities/wikibase.utilities.ui.css',
			),
			'dependencies' => array(
				'wikibase',
				'jquery.tipsy',
				'util.inherit',
				'mediawiki.language',
			),
			'messages' => array(
				'wikibase-ui-pendingquantitycounter-nonpending',
				'wikibase-ui-pendingquantitycounter-pending',
				'wikibase-ui-pendingquantitycounter-pending-pendingsubpart',
				'wikibase-label-empty',
				'wikibase-deletedentity-item',
				'wikibase-deletedentity-property',
				'wikibase-deletedentity-query',
				'word-separator',
				'parentheses',
			)
		),

		'wikibase.utilities.GuidGenerator' => $moduleTemplate + array(
			'scripts' => array(
				'wikibase.utilities/wikibase.utilities.GuidGenerator.js',
			),
			'dependencies' => array(
				'util.inherit',
				'wikibase.utilities',
			)
		),

		'wikibase.utilities.ClaimGuidGenerator' => $moduleTemplate + array(
			'scripts' => array(
				'wikibase.utilities/wikibase.utilities.ClaimGuidGenerator.js',
			),
			'dependencies' => array(
				'wikibase.utilities.GuidGenerator',
			)
		),

		'wikibase.utilities.jQuery' => $moduleTemplate + array(
			'scripts' => array(
				'wikibase.utilities/wikibase.utilities.js',
				'wikibase.utilities/wikibase.utilities.jQuery.js',
			),
			'dependencies' => array(
				'wikibase.utilities'
			)
		),

		'jquery.ui.tagadata' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.ui/jquery.ui.tagadata.js',
			),
			'styles' => array(
				'jquery.ui/jquery.ui.tagadata.css',
			),
			'dependencies' => array(
				'jquery.event.special.eachchange',
				'jquery.effects.blind',
				'jquery.inputautoexpand',
				'jquery.ui.widget'
			)
		),

		'jquery.wikibase.toolbarcontroller' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/toolbar/toolbarcontroller.js',
				'jquery.wikibase/toolbar/toolbarcontroller.definitions.js',
			),
			'dependencies' => array(
				'jquery.wikibase.addtoolbar',
				'jquery.wikibase.edittoolbar',
				'jquery.wikibase.movetoolbar',
				'jquery.wikibase.removetoolbar',
			)
		),

		'jquery.wikibase.toolbarbase' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/toolbar/toolbarbase.js',
			),
			'dependencies' => array(
				'jquery.ui.widget',
				'jquery.wikibase.toolbar',
				'jquery.wikibase.toolbareditgroup',
				'wikibase.templates',
			),
		),

		'jquery.wikibase.addtoolbar' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/toolbar/addtoolbar.js',
			),
			'dependencies' => array(
				'jquery.wikibase.toolbarbase',
				'wikibase.templates',
			),
			'messages' => array(
				'wikibase-add'
			)
		),

		'jquery.wikibase.edittoolbar' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/toolbar/edittoolbar.js',
			),
			'dependencies' => array(
				'jquery.wikibase.toolbarbase',
				'jquery.wikibase.toolbareditgroup',
				'wikibase.templates',
			),
			'messages' => array(
				'wikibase-remove-inprogress',
				'wikibase-save-inprogress',
			)
		),

		'jquery.wikibase.movetoolbar' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/toolbar/movetoolbar.js',
			),
			'styles' => array(
				'jquery.wikibase/toolbar/themes/default/movetoolbar.css',
			),
			'dependencies' => array(
				'jquery.ui.widget',
				'jquery.wikibase.toolbarbase',
				'jquery.wikibase.toolbarbutton',
				'wikibase.templates',
			),
			'messages' => array(
				'wikibase-move-up',
				'wikibase-move-down',
			),
		),

		'jquery.wikibase.removetoolbar' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/toolbar/removetoolbar.js',
			),
			'dependencies' => array(
				'jquery.wikibase.toolbarbase',
				'wikibase.templates',
			),
			'messages' => array(
				'wikibase-remove',
			),
		),

		'wikibase.templates' => $moduleTemplate + array(
			'class' => 'Wikibase\TemplateModule',
			'scripts' => 'templates.js'
		),

		'wikibase.ValueViewBuilder' => $moduleTemplate + array(
			'scripts' => array(
				'wikibase.ValueViewBuilder.js',
			),
			'dependencies' => array(
				'wikibase',
				'jquery.valueview'
			)
		),

		'jquery.ui.TemplatedWidget' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.ui/jquery.ui.TemplatedWidget.js'
			),
			'dependencies' => array(
				'wikibase.templates',
				'jquery.ui.widget',
				'util.inherit',
			)
		),

		'jquery.wikibase.aliasesview' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/jquery.wikibase.aliasesview.js'
			),
			'styles' => array(
				'jquery.wikibase/themes/default/jquery.wikibase.aliasesview.css',
			),
			'dependencies' => array(
				'jquery.inputautoexpand',
				'jquery.ui.tagadata',
				'jquery.ui.TemplatedWidget',
				'jquery.wikibase.edittoolbar',
				'jquery.wikibase.toolbarcontroller',
				'wikibase.RepoApiError',
				'wikibase.templates',
			),
			'messages' => array(
				'wikibase-aliases-label',
				'wikibase-aliases-input-help-message',
				'wikibase-alias-edit-placeholder',
			),
		),

		'jquery.wikibase.descriptionview' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/jquery.wikibase.descriptionview.js'
			),
			'styles' => array(
				'jquery.wikibase/themes/default/jquery.wikibase.descriptionview.css',
			),
			'dependencies' => array(
				'jquery.inputautoexpand',
				'jquery.ui.TemplatedWidget',
				'jquery.wikibase.edittoolbar',
				'jquery.wikibase.toolbarcontroller',
				'wikibase',
				'wikibase.RepoApiError',
			),
			'messages' => array(
				'wikibase-description-edit-placeholder',
				'wikibase-description-edit-placeholder-language-aware',
				'wikibase-description-input-help-message',
			),
		),

		'jquery.wikibase.labelview' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/jquery.wikibase.labelview.js'
			),
			'styles' => array(
				'jquery.wikibase/themes/default/jquery.wikibase.labelview.css',
			),
			'dependencies' => array(
				'jquery.ui.TemplatedWidget',
				'jquery.wikibase.edittoolbar',
				'jquery.wikibase.toolbarcontroller',
				'wikibase',
				'wikibase.RepoApiError',
			),
			'messages' => array(
				'parentheses',
				'wikibase-label-edit-placeholder',
				'wikibase-label-edit-placeholder-language-aware',
				'wikibase-label-input-help-message',
			),
		),

		'jquery.wikibase.sitelinkgroupview' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/jquery.wikibase.sitelinkgroupview.js'
			),
			'dependencies' => array(
				'jquery.ui.TemplatedWidget',
				'jquery.wikibase.sitelinklistview',
				'mediawiki.jqueryMsg', // for {{plural}} and {{gender}} support in messages
				'wikibase.sites',
			),
		),

		'jquery.wikibase.sitelinklistview' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/jquery.wikibase.sitelinklistview.js'
			),
			'styles' => array(
				'jquery.wikibase/themes/default/jquery.wikibase.sitelinklistview.css',
			),
			'dependencies' => array(
				'jquery.event.special.eachchange',
				'jquery.tablesorter',
				'jquery.ui.TemplatedWidget',
				'jquery.wikibase.addtoolbar',
				'jquery.wikibase.edittoolbar',
				'jquery.wikibase.listview',
				'jquery.wikibase.sitelinkview',
				'jquery.wikibase.toolbarcontroller',
				'wikibase',
				'wikibase.datamodel',
				'wikibase.RepoApiError',
				'wikibase.templates',
				'wikibase.utilities', // wikibase.utilities.ui
			),
			'messages' => array(
				'parentheses',
				'wikibase-propertyedittool-counter-entrieslabel',
				'wikibase-propertyedittool-counter-pending-tooltip',
				'wikibase-sitelink-site-edit-placeholder',
				'wikibase-sitelink-page-edit-placeholder',
				'wikibase-sitelinks-sitename-columnheading',
				'wikibase-sitelinks-sitename-columnheading-special',
				'wikibase-sitelinks-siteid-columnheading',
				'wikibase-sitelinks-link-columnheading',
				'wikibase-sitelinksedittool-full',
			)
		),

		'jquery.wikibase.sitelinkview' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/jquery.wikibase.sitelinkview.js'
			),
			'styles' => array(
				'jquery.wikibase/themes/default/jquery.wikibase.sitelinkview.css',
			),
			'dependencies' => array(
				'jquery.ui.TemplatedWidget',
				'jquery.wikibase.pagesuggester',
				'jquery.wikibase.siteselector',
				'jquery.wikibase.toolbarcontroller',
				'mediawiki.util',
				'wikibase.datamodel',
				'wikibase.sites',
				'wikibase.templates',
			),
			'messages' => array(
				'wikibase-add-badges',
				'wikibase-sitelinks-input-help-message',
			),
		),

		'jquery.wikibase.siteselector' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/jquery.wikibase.siteselector.js'
			),
			'dependencies' => array(
				'jquery.event.special.eachchange',
				'jquery.ui.ooMenu',
				'jquery.ui.suggester',
			)
		),

		'jquery.wikibase.pagesuggester' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/jquery.wikibase.pagesuggester.js',
			),
			'dependencies' => array(
				'jquery.ui.suggester',
				'wikibase.sites',
			),
		),

		'jquery.wikibase.listview' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/jquery.wikibase.listview.js',
				'jquery.wikibase/jquery.wikibase.listview.ListItemAdapter.js'
			),
			'dependencies' => array(
				'jquery.NativeEventHandler',
				'jquery.ui.TemplatedWidget',
				'jquery.ui.widget',
			)
		),

		'jquery.wikibase.snaklistview' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/jquery.wikibase.snaklistview.js',
			),
			'dependencies' => array(
				'jquery.NativeEventHandler',
				'jquery.ui.TemplatedWidget',
				'jquery.ui.widget',
				'jquery.wikibase.listview',
				'jquery.wikibase.snakview',
				'wikibase.datamodel',
			),
			'messages' => array(
				'wikibase-claimview-snak-tooltip',
				'wikibase-claimview-snak-new-tooltip',
			)
		),

		'jquery.wikibase.snakview' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/jquery.wikibase.snakview/snakview.js',
				'jquery.wikibase/jquery.wikibase.snakview/snakview.SnakTypeSelector.js',
				'jquery.wikibase/jquery.wikibase.snakview/snakview.ViewState.js',
				'jquery.wikibase/jquery.wikibase.snakview/snakview.variations.js',
				'jquery.wikibase/jquery.wikibase.snakview/snakview.variations.Variation.js',
				'jquery.wikibase/jquery.wikibase.snakview/snakview.variations.Value.js',
				'jquery.wikibase/jquery.wikibase.snakview/snakview.variations.SomeValue.js',
				'jquery.wikibase/jquery.wikibase.snakview/snakview.variations.NoValue.js',
			),
			'styles' => array(
				'jquery.wikibase/jquery.wikibase.snakview/themes/default/snakview.SnakTypeSelector.css',
			),
			'dependencies' => array(
				'jquery.event.special.eachchange',
				'jquery.NativeEventHandler',
				'jquery.ui.position',
				'jquery.ui.TemplatedWidget',
				'jquery.wikibase.entityselector',
				'mediawiki.legacy.shared',
				'mw.config.values.wbRepo',
				'util.inherit',
				'wikibase.datamodel',
				'wikibase.dataTypes',
				'wikibase.utilities'
			),
			'messages' => array(
				'wikibase-snakview-property-input-placeholder',
				'wikibase-snakview-unsupportedsnaktype',
				'wikibase-snakview-choosesnaktype',
				'wikibase-snakview-variation-datavaluetypemismatch',
				'wikibase-snakview-variation-datavaluetypemismatch-details',
				'wikibase-snakview-variation-nonewvaluefordeletedproperty',
				'datatypes-type-wikibase-item',
				'wikibase-snakview-variations-somevalue-label',
				'wikibase-snakview-variations-novalue-label',
				'wikibase-snakview-snaktypeselector-value',
				'wikibase-snakview-snaktypeselector-somevalue',
				'wikibase-snakview-snaktypeselector-novalue'
			)
		),

		'jquery.wikibase.claimview' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/jquery.wikibase.claimview.js'
			),
			'dependencies' => array(
				'jquery.wikibase.snakview',
				'jquery.wikibase.snaklistview',
				'wikibase.datamodel',
				'jquery.wikibase.toolbarcontroller',
			),
			'messages' => array(
				'wikibase-addqualifier',
				'wikibase-claimview-snak-tooltip',
				'wikibase-claimview-snak-new-tooltip'
			)
		),

		'jquery.wikibase.referenceview' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/jquery.wikibase.referenceview.js',
			),
			'dependencies' => array(
				'jquery.wikibase.listview',
				'jquery.wikibase.snaklistview',
				'jquery.wikibase.toolbarcontroller',
				'wikibase.datamodel',
				'wikibase.utilities.jQuery',
			)
		),

		'jquery.wikibase.statementview' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/jquery.wikibase.statementview.js',
				'jquery.wikibase/jquery.wikibase.statementview.RankSelector.js',
			),
			'styles' => array(
				'jquery.wikibase/themes/default/jquery.wikibase.statementview.RankSelector.css',
			),
			'dependencies' => array(
				'jquery.ui.TemplatedWidget',
				'jquery.ui.position',
				'jquery.ui.toggler',
				'util.inherit',
				'jquery.wikibase.claimview',
				'jquery.wikibase.listview',
				'jquery.wikibase.referenceview',
				'jquery.wikibase.toolbarcontroller',
				'wikibase.datamodel',
				'wikibase.utilities',
			),
			'messages' => array(
				'wikibase-statementview-rank-preferred',
				'wikibase-statementview-rank-normal',
				'wikibase-statementview-rank-deprecated',
				'wikibase-statementview-referencesheading-pendingcountersubject',
				'wikibase-statementview-referencesheading-pendingcountertooltip',
				'wikibase-addreference'
			)
		),

		'jquery.wikibase.claimlistview' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/jquery.wikibase.claimlistview.js'
			),
			'dependencies' => array(
				'jquery.ui.TemplatedWidget',
				'jquery.ui.widget',
				'jquery.wikibase.claimview',
				'jquery.wikibase.listview',
				'jquery.wikibase.statementview',
				'jquery.wikibase.toolbarcontroller',
				'wikibase',
				'wikibase.datamodel',
				'wikibase.RepoApiError',
				'wikibase.templates',
				'wikibase.utilities',
				'wikibase.utilities.ClaimGuidGenerator',
			),
			'messages' => array(
				'wikibase-entity-property',
			),
		),

		'jquery.wikibase.claimgrouplistview' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/jquery.wikibase.claimgrouplistview.js'
			),
			'dependencies' => array(
				'jquery.ui.TemplatedWidget',
				'jquery.ui.widget',
				'jquery.wikibase.claimlistview',
				'jquery.wikibase.listview',
				'jquery.wikibase.toolbarcontroller',
				'wikibase',
				'wikibase.datamodel',
			),
		),

		'jquery.wikibase.entityview' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/jquery.wikibase.entityview.js'
			),
			'dependencies' => array(
				'jquery.ui.TemplatedWidget',
				'jquery.wikibase.aliasesview',
				'jquery.wikibase.claimgrouplistview',
				'jquery.wikibase.claimlistview',
				'jquery.wikibase.descriptionview',
				'jquery.wikibase.labelview',
				'jquery.wikibase.toolbarcontroller',
				'jquery.wikibase.statementview',
				'wikibase',
				'wikibase.templates',
			),
		),

		'jquery.wikibase.entityselector' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/jquery.wikibase.entityselector.js'
			),
			'styles' => array(
				'jquery.wikibase/themes/default/jquery.wikibase.entityselector.css'
			),
			'dependencies' => array(
				'jquery.event.special.eachchange',
				'jquery.ui.suggester',
				'jquery.ui.ooMenu',
				'jquery.ui.widget',
			),
			'messages' => array(
				'wikibase-aliases-label',
				'wikibase-entityselector-more'
			)
		),

		'jquery.wikibase.claimgrouplabelscroll' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/jquery.wikibase.claimgrouplabelscroll.js'
			),
			'dependencies' => array(
				'jquery.ui.widget',
			),
		),

		'jquery.wikibase.toolbarlabel' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/toolbar/toolbarlabel.js',
			),
			'styles' => array(
				'jquery.wikibase/toolbar/themes/default/toolbarlabel.css',
			),
			'dependencies' => array(
				'jquery.ui.widget',
				'wikibase.utilities',
			),
		),

		'jquery.wikibase.toolbarbutton' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/toolbar/toolbarbutton.js',
			),
			'styles' => array(
				'jquery.wikibase/toolbar/themes/default/toolbarbutton.css',
			),
			'dependencies' => array(
				'jquery.wikibase.toolbarlabel',
			),
		),

		'jquery.wikibase.toolbar' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/toolbar/toolbar.js',
			),
			'styles' => array(
				'jquery.wikibase/toolbar/themes/default/toolbar.css',
			),
			'dependencies' => array(
				'jquery.wikibase.toolbarbutton',
			),
		),

		'jquery.wikibase.toolbareditgroup' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/toolbar/toolbareditgroup.js',
			),
			'dependencies' => array(
				'jquery.ui.widget',
				'jquery.wikibase.toolbar',
				'jquery.wikibase.wbtooltip',
				'wikibase.templates',
			),
			'messages' => array(
				'wikibase-cancel',
				'wikibase-edit',
				'wikibase-save',
				'wikibase-remove',
			),
		),

		'jquery.wikibase.wbtooltip' => $moduleTemplate + array(
			'scripts' => array(
				'jquery.wikibase/jquery.wikibase.wbtooltip.js',
			),
			'styles' => array(
				'jquery.wikibase/themes/default/jquery.wikibase.wbtooltip.css'
			),
			'dependencies' => array(
				'jquery.tipsy',
				'jquery.ui.toggler',
				'jquery.ui.widget',
			),
			'messages' => array(
				'wikibase-tooltip-error-details',
			),
		),

	);

	$modules = array_merge(
		$modules,
		include( __DIR__ . '/api/resources.php' ),
		include( __DIR__ . '/experts/resources.php' ),
		include( __DIR__ . '/formatters/resources.php' ),
		include( __DIR__ . '/parsers/resources.php' )
	);

	if ( defined( 'ULS_VERSION' ) ) {
		$modules['wikibase']['dependencies'][] = 'ext.uls.mediawiki';
		$modules['wikibase.Site']['dependencies'][] = 'ext.uls.mediawiki';
	}

	return $modules;
} );
