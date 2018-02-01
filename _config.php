<?php
/**
 * Created by Nivanka Fonseka (nivanka@silverstripers.com).
 * User: nivankafonseka
 * Date: 4/6/15
 * Time: 2:03 PM
 * To change this template use File | Settings | File Templates.
 */
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\ORM\Hierarchy\Hierarchy;
use SilverStripers\News\Extensions\NewsHierarchy;


SiteTree::remove_extension(Hierarchy::class);
SiteTree::add_extension(NewsHierarchy::class);

