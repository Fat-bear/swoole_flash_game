<?php
namespace control\server;
use common,
    control\base,
    ZPHP\Core\Config as ZConfig,
    ZPHP\Common\Debug;

use model\cdModel;
use model\testModel;
use service\testService;

class test extends base
{

    /**
     * @var testModel;
     */
    public $testModel;

    /**
     * @var cdModel;
     */
    public $cdModel;

    /**
     * @var testService;
     */
    public $testService;

    public function main()
    {

        //---------------------------------------------------
        echo "Debug test:\n";
        Debug::log("debug log\n");
        Debug::info("debug info\n");
        Debug::debug("debug debug\n");
        Debug::error("debug error\n");
        Debug::error('debug error', 'aa', 'bb', 'cc');

        //pdo.php ping() 就是在执行sql的时候，失败的话，检查一下状态码。进行重连
        //---------------------------------------------------
        echo "\n\nDB option:\n";
        $user = $this->testModel->getAll();
        echo "get userMode one record.\n";
        print_r($user[0]);

        //---------------------------------------------------
        //fd opt
        echo "\n\nfd and connection option:\n";
        $uid = 10000;
        $uinfo = $this->connection->get($uid);
        if ($uinfo) {
            echo "already exist user.\n";
            print_r($uinfo);
        } else {
            echo "new add user.\n";
            $this->connection->add($uid, $this->fd);
            $this->connection->addFd($this->fd, $uid);
            print_r($this->connection->get($uid));
        }

        common\connection::sendOne($this->fd, 1, 'test send me');
        echo "send me sucess.\n";

        common\connection::sendToChannel(1, 'test send all');
        echo "send all sucess.\n";

        //加入到room1
        $this->connection->addChannel($uid, 'ROOM1');
        common\connection::sendToChannel(1, 'test send ROOM1', 'ROOM1');
        echo "send all room1.\n";

        //---------------------------------------------------
        echo "\n\ncache option:\n";
        $userinfo = array(
            'id' => $uid,
            'name' => 'ansen',
            'sex' => 2
        );
        $this->cache->set('c_uid_' . $uid, json_encode($userinfo));
        echo "cache set userinfo sucess.\n";

        echo "get cache userinfo.\n";
        print_r(json_decode($this->cache->get('c_uid_' . $uid), true));

        //---------------------------------------------------
        echo "\n\nrank option:\n";
        $this->rankCache->addRank($rankType = 'global', $key = 'global_u1', $score = 1000, $length = 5);
        $this->rankCache->addRank($rankType = 'global', $key = 'global_u2', $score = 2000, $length = 5);
        $this->rankCache->addRank($rankType = 'global', $key = 'global_u3', $score = 500, $length = 5);
        $this->rankCache->addRank($rankType = 'global', $key = 'global_u4', $score = 100, $length = 5);
        $this->rankCache->addRank($rankType = 'global', $key = 'global_u5', $score = 6000, $length = 5);
        echo "add rank u1 u2 u3 u4 u5 over.\n";

        echo "get rank1:\n";
        $list1 = $this->rankCache->getRank($rankType = 'global', $start = 0, $limit = 100, $score = true, $desc = 0);
        print_r($list1);

        echo "get rank by score:\n";
        $list2 = $this->rankCache->getRankByScore($rankType = 'global', $start = 200, $end = 2000, $scores = true, $offset = 0, $count = 0);
        print_r($list2);

        echo "get rank by key(global_u3): 第"; //排名第几
        echo $this->rankCache->getRankByKey($rankType = 'global', $key = 'global_u3', $desc = 0) . "位\n\n\n";


        //---------------------------------------------------
        echo "\n\ncd option:\n";
        $this->cdModel->addCd(10000, 'ansen');
        $this->cdModel->updCd(10000, 'ansen', array('cdCount' => 1, 'cdTimeStamp' => 234234));
        $cd =  $this->cdModel->getCd(10000, 'ansen');
        print_r($cd);

        echo "get user keep login days:";
        echo $this->cdModel->getKeepLoginDays(10000);
        echo " days\n";

        //---------------------------------------------------
//        echo "\n\nreids store struct option:\n";
//        $username = 'tomi';
//        $password = 'tomi123';
//
//        $userid = $this->cache->increment("global:nextUserId");
//        $this->cache->set("username:$username:id", $userid);
//        $this->cache->set("uid:$userid:username", $username);
//        $this->cache->set("uid:$userid:password", $password);
//        //设置此用户密钥
//        $authcookie = md5(time());
//        $this->cache->set("uid:$userid:auth", md5(time()));
//        $this->cache->set("auth:$authcookie", $userid);

        return 'finish action.';

    }

}
