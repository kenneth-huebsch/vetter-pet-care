<?php namespace Zenbu\librairies;

use Craft;
use Zenbu\frameworks\craft\Cache;
use Zenbu\frameworks\craft\Lang;
use Zenbu\frameworks\craft\Request;
use Zenbu\frameworks\craft\Session;
use Zenbu\frameworks\craft\Convert;
use Zenbu\frameworks\craft\Db;
use Zenbu\frameworks\craft\UserBase as UserBase;

class Users extends UserBase
{
    public static function getAuthors()
    {
        return UserBase::getAuthors();
    }

    // --------------------------------------------------------------------
}