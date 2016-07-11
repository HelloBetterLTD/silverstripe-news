<?php
/**
 * Created by Nivanka Fonseka (nivanka@silverstripers.com).
 * User: nivankafonseka
 * Date: 1/15/16
 * Time: 8:44 AM
 * To change this template use File | Settings | File Templates.
 */

class LinkPost extends NewsPost
{

	private static $db = array(
		'ShareLink'			=> 'Varchar(500)',
		'LinkTarget'		=> 'Enum("_self,_blank")'
	);

	private static $icon = 'silverstripe-news/images/LinkPost.png';

	public function getCMSFields()
	{
		$fields = parent::getCMSFields();

		$fields->removeByName(array(
			'Content',
			'URLSegment'
		));

		$fields->addFieldsToTab('Root.Main', array(
			TextField::create('ShareLink', 'Link'),
			DropdownField::create('LinkTarget', 'Target')
				->setSource(array(
					'_self'		=> 'Self',
					'_blank'	=> 'Open in a new window'
				))
		), 'Summary');


		return $fields;
	}

	public function Link($action = null) {
		return $this->ShareLink;
	}


}

class LinkPost_Controller extends NewsPost_Controller
{

	public function init(){
		parent::init();
		return $this->redirect($this->ShareLink);
	}

}