/**
 * LayChat
 * laychat.workerman.net
 */

var WEB_SOCKET_SWF_LOCATION = "./static/swf/WebSocketMain.swf";
var WEB_SOCKET_DEBUG = true;
var WEB_SOCKET_SUPPRESS_CROSS_DOMAIN_SWF_ERROR = true;

var laychat = {
    appName           : 'LayChat',
    initUrl           : './init.php',         // 初始化好友列表和群组接口，这个接口返回个json，格式见
    sendMessageUrl    : './send_message.php', // 发消息接口
    membersUrl        : './members.php',
    uploadImageUrl    : './upload_img.php',
    uploadFileUrl     : './upload_file.php',
    chatLogUrl        : './chat_log.php',
    bindUrl           : './bind.php',
    updateSignUrl     : './update_sign.php',
    msgBoxUrl         : './msg_box.php',
    agreeUrl          : './agree.php',
    getNoticeUrl      : './get_notice.php',
    isMobile          : false,
    address           : 'ws://'+document.domain+':8282',
    setMin            : false,
    findUrl           : false,
    inited            : 0,
    socket            : null,
    jq                : null,
    open              : function() {
        if (this.inited) return;
        if(this.isIE6or7()) return;
        if(!this.jq) this.jq = $;
        this.connectWorkerman();
        this.inited = 1;
    },
    isIE6or7 : function(){
        var b = document.createElement('b');
        b.innerHTML = '<!--[if IE 5]><i></i><![endif]--><!--[if IE 6]><i></i><![endif]--><!--[if IE 7]><i></i><![endif]-->';
        return b.getElementsByTagName('i').length === 1;
    },
    connectWorkerman : function() {
        laychat.socket        = new WebSocket(laychat.address);
        laychat.socket.onopen = function(){
            laychat.socket.send(JSON.stringify({type: 'init'}));
        };

        laychat.socket.onmessage = function(e){
            var msg = JSON.parse(e.data);
            // 如果layim还没有初始化就收到消息则忽略（init消息除外）
            if(!msg.message_type || (msg.message_type != 'init' && !layui.layim)) return;
            switch(msg.message_type) {
                // 初始化im
                case 'init':
                    // ajax请求，绑定client_id和uid
                    laychat.jq.post(laychat.bindUrl, {client_id: msg.client_id}, function(data){
                        alert(laychat.bindUrl);
                        if(data.code == 0) {
                            // im获得初始化数据，当前用户，好友列表等数据
                            laychat.jq.post(laychat.initUrl, {}, function(initData){
                                if (initData.code != 0) {
                                    alert('laychat服务端返回失败：' + initData.msg);
                                } else {
                                    laychat.initIM(data, initData.data);
                                }
                            }, 'json');
                        } else {
                            alert('laychat服务端返回失败：' + data.msg);
                        }
                    }, 'json');
                    return;
                // 添加一个用户到好友列表
                case 'addList':
                    if(laychat.jq('#layim-friend'+msg.data.id).length == 0 && layui.layim.cache() && layui.layim.cache().id != msg.data.id){
                        return layui.layim.addList && layui.layim.addList(msg.data);
                    }
                    if (msg.data.type == 'friend') {
                        layui.layim.setFriendStatus && layui.layim.setFriendStatus(msg.data.id, 'online');
                    }
                    return;
                // 收到一个消息
                case 'chatMessage':
                    if(msg.data.type == 'group') {
                        if(msg.data.from_id != layui.layim.cache().mine.id){
                            layui.layim.getMessage(msg.data);
                        }
                    }else if(layui.layim.cache().mine.id != msg.data.id){
                        layui.layim.getMessage(msg.data);
                    }
                    return;
                case 'msgbox':
                    layui.layim.msgbox && layui.layim.msgbox(msg.count);
                    return;
                // 退出
                case 'logout':
                // 隐身
                case 'hide':
                    return layui.layim.setFriendStatus && layui.layim.setFriendStatus(msg.id, 'offline');
                // 上线
                case 'online':
                    return layui.layim.setFriendStatus && layui.layim.setFriendStatus(msg.id, 'online');
            }
        }
        laychat.socket.onclose = laychat.connectWorkerman;
    },
    sendHeartbeat : function() {
        if(this.socket && this.socket.readyState == 1) {
            this.socket.send(JSON.stringify({type :'ping'}));
        }
    },
    initIM : function(msg_data, init_data){
        var unread_msg_tips = function(msg_data){
            // 离线消息
            for(var key in msg_data.unread_message){
                layui.layim.getMessage(JSON.parse(msg_data.unread_message[key]));
            }
            if (msg_data.unread_notice_count) {
                // 设置消息盒子未读计数
                layui.layim.msgbox && layui.layim.msgbox(msg_data.unread_notice_count);
            }
            return;
        };
        // layim已经初始化了，则只提示未读消息
        if(this.inited == 2) {
            return unread_msg_tips(msg_data);
        }
        this.inited = 2;
        // 心跳数据，用来保持长链接，避免socket链接长时间不通讯被路由节点防火墙关闭
        setInterval('laychat.sendHeartbeat()', 12000);

        var module = laychat.isMobile ? 'mobile' : 'layim';
        layui.use(module, function(layim){
            if (laychat.isMobile) {
                var mobile = layui.mobile
                    , layim = mobile.layim
                    , layer = mobile.layer;
                layui.layim = layim;
                layui.layer = layer;
            }

            layui.layim.config({
                //初始化接口
                init: init_data

                //查看群员接口
                ,members: {
                    url: laychat.membersUrl
                }

                // 上传图片
                ,uploadImage: {
                    url: laychat.uploadImageUrl
                }

                // 上传文件
                ,uploadFile: {
                    url: laychat.uploadFileUrl
                }

                ,msgbox: laychat.msgBoxUrl+(laychat.msgBoxUrl.indexOf('?') == -1 ? '?' : '&')+'getNoticeUrl='+encodeURI(laychat.getNoticeUrl)+'&agreeUrl='+encodeURI(laychat.agreeUrl)

                //聊天记录地址
                ,chatLog: laychat.chatLogUrl

                ,find: laychat.findUrl

                ,copyright: false //是否授权

                ,title: laychat.appName

                ,min: laychat.setMin
            });


            //监听发送消息
            layim.on('sendMessage', function(data){
                laychat.jq.post(laychat.sendMessageUrl, { data: data} , function(data){
                    if(data.code != 0) {
                        layui.layer.msg(data.msg, {time: 7000});
                    }
                }, 'json');
            });

            //监听在线状态的切换事件
            layim.on('online', function(data){
                laychat.socket.send(JSON.stringify({type: data}));
            });

            //更改个性签名
            layim.on('sign', function(value){
                laychat.jq.post(laychat.updateSignUrl, {sign: value} , function(data){
                    if(data.code != 0) {
                        layui.layer.msg(data.msg, {time: 7000});
                    }
                }, 'json');
            });

            //layim建立就绪
            layim.on('ready', function(res){
                // 离线消息
                return unread_msg_tips(msg_data);
            });
        });
    }
};