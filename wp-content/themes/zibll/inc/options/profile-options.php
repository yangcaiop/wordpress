<?php
/*
 * @Author        : Qinver
 * @Url           : dkewl.com
 * @Date          : 2020-09-29 13:18:36
 * @LastEditTime: 2022-04-28 21:25:40
 * @About         : 刀客源码
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

//个人资料设置
CSF::createProfileOptions('user_meta_main', array(
    'data_type' => 'unserialize',
));
CSF::createSection('user_meta_main', array(
    'fields' => array(
        array(
            'type'    => 'content',
            'content' => '<h3>更多资料</h3><span style="color: #fb3f2c;">此处内容建议在前台用户中心修改</span>',
        ),
        array(
            'id'    => 'custom_avatar',
            'type'  => 'upload',
            'library'      => 'image',
            'title' => '自定义头像',
        ),
        array(
            'id'    => 'cover_image',
            'type'  => 'upload',
            'library'      => 'image',
            'title' => '自定义封面',
        ),
        array(
            'id'          => 'gender',
            'type'        => 'select',
            'title'       => '性别',
            'options'     => array(
                '保密'  => '保密',
                '男'  => '男',
                '女'  => '女',
            ),
            'default'     => '保密'
        ),
        array(
            'id'      => 'qq',
            'type'    => 'text',
            'title'   => 'QQ',
            'placeholder'   => '请输入QQ号',
        ),
        array(
            'id'      => 'weixin',
            'type'    => 'text',
            'title'   => '微信',
            'placeholder'   => '请输入微信号',
        ),
        array(
            'id'      => 'weibo',
            'type'    => 'text',
            'title'   => '微博',
            'placeholder'   => '请输入微博地址',
        ),
        array(
            'id'      => 'github',
            'type'    => 'text',
            'title'   => 'GitHub',
            'placeholder'   => '请输入GitHub地址',
        ),
        array(
            'id'      => 'address',
            'type'    => 'text',
            'title'   => '住址',
            'placeholder'   => '请输入住址',
        ),

    )
));
