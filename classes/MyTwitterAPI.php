<?php
require_once('TwitterAPIExchange.php');

/*
$url = 'https://api.twitter.com/1.1/search/tweets.json';
$url = 'https://api.twitter.com/1.1/followers/ids.json';
$url = 'https://api.twitter.com/1.1/followers/list.json';
*/

class MyTwitterAPI extends TwitterAPIExchange
{
    protected $base_url = 'https://api.twitter.com/1.1';
    protected $cursor = -1;
    protected $cursor_count = 5;


    public function getFollowersProfileText($screen_name, $sep="\n")
    {
        $res = $this->getFollowersProfileTextFields($screen_name);
        foreach ($res as $k=>$v) {
            $res[$k] = implode($sep, $v);
        }
        return $res;
    }

    public function getFollowersProfileTextFields($screen_name)
    {
        return $this->getFollowersFields($screen_name, explode(',', 'screen_name,name,location,description'));
    }

    public function getFollowersScreenNames($screen_name)
    {
        return $this->getFollowersFields($screen_name, array('screen_name'));
    }

    public function getFollowersFields($screen_name, $ar_fields)
    {
        $res = array();
        do {
//vdump($this->cursor, '$this->cursor'); 
           $obj = $this->getFollowersListByScreenName($screen_name);
            if (property_exists($obj, 'users')) {
                foreach ($obj->users as $i=>$obj_user) {
                    foreach ($ar_fields as $field) {
                        $res[$obj_user->screen_name][$field] = $obj_user->$field;
                    }
                }
                $this->cursor = $obj->next_cursor_str;
            }
        } while ($this->cursor != 0);
vdump($res, '$res');

//vdump_e($ar);
        return $res;
    }

    public function getFollowersIdsByUserId($param)
    {
        return $this->getFollowersIdsBy('user_id', $param);
    }

    public function getFollowersIdsByScreenName($param)
    {
//vdump($param, 'param');
        return $this->getFollowersIdsBy('screen_name', $param);
    }

    public function getFollowersListByUserId($param)
    {
        return $this->getFollowersListBy('user_id', $param);
    }

    public function getFollowersListByScreenName($param)
    {
        return $this->getFollowersListBy('screen_name', $param);
    }

    public function getFollowersListBy($type, $param)
    {
        return $this->getFollowersBy($type, $param, 'list');
    }

    public function getFollowersIdsBy($type, $param)
    {
        return $this->getFollowersBy($type, $param, 'ids');
    }

    public function getFollowersBy($type, $param, $url_subdir)
    {
        return $this->getResponseGET('?'.$type.'='.$param, 'followers/'.$url_subdir);
    }

    public function getUserInfoByScreenName($screen_name)
    {
        $ar = getUsersByScreenNames($screen_name);
        return $ar[0];
    }

    public function getUsersByScreenNames($screen_name)
    {
        if (!is_array($screen_name)) {
            $screen_name = array($screen_name);
        }
        return $this->getResponseGET('?screen_name='.implode(',', $screen_name), 'users/lookup');
    }

    public function getURL($url_dir)
    {
        return rtrim($this->base_url, '/').'/'.ltrim($url_dir, '/').'.json';
    }

    public function getResponse($fields, $url_dir, $type='GET')
    {
        $res = false;
        $url = $this->getURL($url_dir);
        $fields.= '&count='.min($this->cursor_count,200).'&cursor='.$this->cursor;
//vdump($fields, 'fields');
//vdump($url, 'url');
        if ('GET' == strtoupper($type)) {
            $this->setGetfield($fields)
                 ->buildOauth($url, $type);
        } else {
            $this->buildOauth($url, $type)
                 ->setPostfields($fields);
        }

        $res = $this->performRequest();

        return $res;
    }

    public function getResponseGET($fields, $url_dir)
    {
        return $this->getResponse($fields, $url_dir);
    }

    public function getResponsePOST($fields, $url_dir)
    {
        return $this->getResponse($fields, $url_dir, 'POST');
    }

    public function performRequest($return = true, $curlOptions = array())
    {
        $res = parent::performRequest($return, $curlOptions);
//vdump($res, __FUNCTION__);
        $res = json_decode($res);
//vdump_e($res, __FUNCTION__);
        return $res;
    }
}

