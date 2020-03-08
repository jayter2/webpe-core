<?php
// ----------------------------------------------------------------------
// WebPE
// Copyright (c) 2016-2020 http://www.webpe.cn All rights reserved.
// Author: jayter <jayter2@qq.com>
// ----------------------------------------------------------------------
// WebPE系统后台权限服务类

const adminRoleId = 1;  //超级管理组ID

/**
 * 后台登录认证/权限验证,功能特性：
 * 1，对:模块/控制器/方法 进行验证。
 *      $auth = new AdminService();  $auth->checkAccess('m/c/a');
 * 2，用户可有多个组(auth_group_access存用户所属组)，用户组规则(webpe_auth_group)
 * 3，支持菜单规则表达式。
 *      在auth_menu表expres字段可定义规则，如{score}>5 and {score}<100
 */
class AdminService
{

    private $uid = 0;
    private $superRoleId = 1; //超级角色ID


    /**
     * 设置超级管理的角色ID
     * @param int $roleid
     */
    public function setSuperRoleId($roleid){
    	$this->superRoleId = $roleid;
    	return $this;
    }

    /**
     * 检查和获取后台登录信息
     * @return array|boolean
     */
    public function isLogin(){

        
    }

    /**
     * 后台登录
     * @param string $name mobile|email|name
     * @param string $password
     * @return array|boolean
     */
    public function login($name,$password){


    }

    /**
     * 是否为超级管理员角色
     * @param number $uid
     * @return mixed|boolean
     */
    public function isSuperRole($uid=0){


    }

    /**
      * 检查当前uid的权限
      * @param string rule  需要验证的规则列表m/c/a,支持逗号分隔的权限规则或索引数组
      * @param int uid            认证用户的id
      * @return boolean           通过验证返回true;失败返回false
     */
    public function checkAccess($rule='') {

    }



}
