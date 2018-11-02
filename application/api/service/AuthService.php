<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/11/2
 * Time: 3:59 PM
 */

namespace app\api\service;


use app\api\model\AuthGroup;
use app\api\model\AuthRule;

class AuthService
{
    /*-- ----------------------------
    -- think_auth_rule，规则表，
    -- id:主键，name：规则唯一标识, title：规则中文名称 status 状态：为1正常，为0禁用，condition：规则表达式，为空表示存在就验证，不为空表示按照条件验证
    -- ----------------------------
    DROP TABLE IF EXISTS `repair_auth_rule`;
    CREATE TABLE `repair_auth_rule` (
    `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
    `name` char(80) NOT NULL DEFAULT '',
    `title` char(20) NOT NULL DEFAULT '',
    `type` tinyint(1) NOT NULL DEFAULT '1',
    `status` tinyint(1) NOT NULL DEFAULT '1',
    `condition` char(100) NOT NULL DEFAULT '', # 规则附件条件,满足附加条件的规则,才认为是有效的规则
    PRIMARY KEY (`id`),
    UNIQUE KEY `name` (`name`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
    -- ------------------------------ think_auth_group 用户组表，
    -- id：主键， title:用户组中文名称， rules：用户组拥有的规则id， 多个规则","隔开，status 状态：为1正常，为0禁用
    -- ----------------------------
    DROP TABLE IF EXISTS `repair_auth_group`;
    CREATE TABLE `repair_auth_group` (
    `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
    `title` char(100) NOT NULL DEFAULT '',
    `status` tinyint(1) NOT NULL DEFAULT '1',
    `rules` char(80) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
    -- ----------------------------
    -- think_auth_group_access 用户组明细表
    -- uid:用户id，group_id：用户组id
    -- ----------------------------
    DROP TABLE IF EXISTS `repair_auth_group_access`;
    CREATE TABLE `repair_auth_group_access` (
    `uid` mediumint(8) unsigned NOT NULL,
    `group_id` mediumint(8) unsigned NOT NULL,
    UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
    KEY `uid` (`uid`),
    KEY `group_id` (`group_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;*/


    public function authRules()
    {
        $rules = AuthRule::where('status', 1)
            ->where('parent_id', 0)
            ->field('id,name,condition')
            ->select()->toArray();
        if (count($rules))
            foreach ($rules as $k => $v) {

                $rules [$k]['child'] = $this->getChildren($v['id']);

            }

        return $rules;


    }

    private function getChildren($id)
    {
        $rules = AuthRule::where('status', 1)->where('parent_id', $id)
            ->field('id,name,condition,parent_id')
            ->select()->toArray();

        if (count($rules)) {
            foreach ($rules as $k => $v) {

                $rules[$k]['child'] = AuthRule::where('status', 1)
                    ->where('parent_id', $v['id'])
                    ->field('id,name,condition,parent_id')
                    ->select()->toArray();

            }

        }

        return $rules;

    }


}