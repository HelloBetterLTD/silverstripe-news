<?php
/**
 * Created by Nivanka Fonseka (nivanka@silverstripers.com).
 * User: nivankafonseka
 * Date: 2/1/18
 * Time: 11:56 AM
 * To change this template use File | Settings | File Templates.
 */

namespace SilverStripers\News\Pages;

use PageController;

class LinkPostController extends PageController
{

	public function init(){
		parent::init();
		return $this->redirect($this->ShareLink);
	}

}