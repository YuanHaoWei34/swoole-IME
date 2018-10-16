<?php 
/**
 * IME通信服务端
 */
// 1.创建服务器
$IME_Server = new swoole_websocket_server("0.0.0.0", 9502);
// 2.网络连接情况
// 2.1 连接成功
$IME_Server->on('open',function($IME_Server, $request){
	echo "新用户进入！\n" .$request->fd;
	$GLOBALS['fd'][$request->fd]['id'] = $request->fd;
	$GLOBALS['fd'][$request->fd]['name'] = '匿名用户';
});
// 2.2 连接消息
$IME_Server->on('message',function($IME_Server, $request){
	$msg = $GLOBALS['fd'][$request->fd]['name'] .":".$request->data."\n";

	if(strstr($request->data,"#name#")){
		//设置用户名
		$GLOBALS['fd'][$request->fd]['name'] = str_replace("#name#",'',$request->data);
	}else{
		//发送用户消息
		foreach($GLOBALS['fd'] as $i) {
			$IME_Server->push($i['id'], $msg);
		}
	}
});

// 3.关闭网络连接
$IME_Server->on('close', function($IME_Server, $request){
	echo "用户：{$request}退出聊天";
	unset($GLOBALS['fd'][$request]); //清除网络连接
});

// 4.开启网络服务
$IME_Server->start();
