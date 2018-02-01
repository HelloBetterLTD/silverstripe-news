<?php
/**
 * Created by Nivanka Fonseka (nivanka@silverstripers.com).
 * User: nivankafonseka
 * Date: 2/1/18
 * Time: 11:54 AM
 * To change this template use File | Settings | File Templates.
 */

namespace SilverStripers\News\Pages;

use PageController;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\RSS\RSSFeed;
use SilverStripe\Core\Convert;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\SiteConfig\SiteConfig;

class NewsIndexController extends PageController
{

	private static $allowed_actions = array(
		'tag',
		'archive',
		'rss',
		'category'
	);


	public function tag()
	{
		return $this;
	}

	public function archive()
	{
		return $this;
	}

	public function category()
	{
		return $this;
	}

	/**
	 * @return bool
	 */
	public function IsTag()
	{
		return $this->request->param('Action') == 'tag';
	}

	/**
	 * @return bool
	 */
	public function IsArchive()
	{
		return $this->request->param('Action') == 'archive';
	}

	/**
	 * @return bool
	 */
	public function IsCategory()
	{
		return $this->request->param('Action') == 'category';
	}


	/**
	 * @return array|string
	 */
	public function GetFilterText()
	{
		return Convert::raw2xml($this->request->param('ID'));
	}


	/**
	 * @param int $iOffset
	 * @return PaginatedList
	 */
	public function Items($iOffset = 0)
	{
		$request = $this->GetRequestForItems($iOffset);

		$items = NewsPost::get()->filter('ParentID', $this->ID);

		if ($this->IsTag()) {
			$items = $items->filter('Tags:PartialMatch', $this->request->param('ID'));
		}

		if ($this->IsCategory()) {
			$items = $items->where('EXISTS ( 
				SELECT 1 FROM "NewsPost_Categories" WHERE 
					"NewsPost_Categories"."NewsPostID" = "NewsPost"."ID" 
					AND "NewsPost_Categories"."NewsCategoryID" = ' . ((int)$this->request->param('ID')) . ')');
		}

		if ($this->IsArchive()) {
			$strPattern = SiteConfig::current_site_config()->ArchivePattern ? : '%Y, %M';
			$items = $items->where('DATE_FORMAT("DateTime", \'' . $strPattern .  '\') = \'' . Convert::raw2sql($this->request->param('ID')) . '\'');
		}

		$items = $items->Sort('DateTime DESC');

		$this->extend('updateItemsList', $items);

		$paginatedList = new PaginatedList($items, $request);
		$paginatedList->setPageLength($this->ItemsPerPage ? : SiteConfig::current_site_config()->ItemsPerPage ? : 10);
		return $paginatedList;
	}


	/**
	 * @param int $iOffset
	 * @return NullHTTPRequest|SS_HTTPRequest
	 */
	public function GetRequestForItems($iOffset = 0)
	{
		if ($iOffset == 0) {
			return $this->request;
		}

		$iStart = 0;
		$request = Controller::curr()->getRequest();
		if ($request->getVar('start')) {
			$iStart = $request->getVar('start');
		}
		$iStart += $iOffset;

		return new HTTPRequest("get", "/", array(
			"start"        => $iStart,
		));
	}

	/**
	 * @return int
	 */
	public function NewsItemsPerPage()
	{
		return $this->ItemsPerPage ? : SiteConfig::current_site_config()->ItemsPerPage ? : 10;
	}

	/**
	 * RSS feed
	 */
	public function rss()
	{
		$list = NewsPost::get()->filter('ParentID', $this->ID);
		$list = $list->Sort('DateTime DESC');

		$this->extend('updateRSSItems', $list);

		$feed = new RSSFeed(
			$list,
			Director::absoluteURL($this->Link()),
			$this->Title
		);

		return $feed->outputToBrowser();
	}


	/**
	 * @return DataList
	 */
	public function NewsCategories()
	{
		return NewsCategory::get();
	}

}