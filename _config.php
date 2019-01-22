<?php
/**
 * Created by Nivanka Fonseka (nivanka@silverstripers.com).
 * User: nivankafonseka
 * Date: 4/6/15
 * Time: 2:03 PM
 * To change this template use File | Settings | File Templates.
 */

if (!class_exists('SS_Object')) class_alias('Object', 'SS_Object');

SiteTree::remove_extension('Hierarchy');
SS_Object::add_extension('SiteTree', 'NewsHierarchy');
