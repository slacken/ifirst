<?php
$config = array(
	//注册
	'signup' => array(
					array(
						'field' => 'email',
						'label' => '',
						'rules' => 'required|valid_email'
					),
					array(
						'field' => 'password',
						'label' => '',
						'rules' => 'required|trim'
					),
					array(
						'field'=>'name',
						'label'=>'',
						'rules'=>'required'
					),
					array(
						'field'=>'gender',
						'label'=>'',
						'rules'=>'required|integer'
					)
				),
	//登录
	'signin'=>array(
					array(
						'field' => 'email',
						'label' => '',
						'rules' => 'required|valid_email'
					),
					array(
						'field' => 'password',
						'label' => '',
						'rules' => 'required|trim'
					)
				),
	//发布帮帮忙
	'add_help'=>array(
					array(
						'field' => 'title',
						'label' => '',
						'rules' => 'required'
					),
					array(
						'field' => 'content',
						'label' => '',
						'rules' => 'required|trim'
					),
					array(
						'field' => 'reward',
						'label' => '',
						'rules' => 'required'
					)
				),
	//发布文章
	'add_post'=>array(
					array(
						'field' => 'title',
						'label' => '',
						'rules' => 'required'
					),
					array(
						'field' => 'content',
						'label' => '',
						'rules' => 'required'
					)
				),
	//发布二手物品
	'add_goods'=>array(
					array(
						'field' => 'title',
						'label' => '',
						'rules' => 'required'
					),
					array(
						'field' => 'content',
						'label' => '',
						'rules' => 'required'
					),
					array(
						'field' => 'price',
						'label' => '',
						'rules' => 'required|is_natural'
					)/*,
					array(
						'field' => 'image',
						'label' => '',
						'rules' => 'required'
					)*/
				),
	//发布评论
	'add_comment'=>array(
					array(
						'field' => 'content',
						'label' => '',
						'rules' => 'required'
					)
				)
);