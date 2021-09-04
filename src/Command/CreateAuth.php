<?php

namespace Hahadu\ThinkAuth\Command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Db;

/*****
 * 创建Auth权限表
 */
class CreateAuth  extends Command
{
    private $db_prefix = '';
    public function __construct()
    {
        parent::__construct();
        $this->setDbPrefix();
    }

    protected function configure()
    {
        // 指令配置
        $this->setName('userModel')
            ->setDescription('创建用户权限表');
    }

    protected function execute(Input $input, Output $output)
    {
        // 指令输出
        $output->writeln('创建Auth数据表中。。。。');
        $this->createAuthRule($input,$output);
        $this->createAuthGroup($input,$output);
        $this->createAuthGroupAccess($input,$output);
        $output->writeln('创建Auth数据表成功');
    }

    /*****
     * 创建数据表
     * @return mixed
     */
    private function createAuthRule(Input $input, Output $output){
        $auth_rule = $this->db_prefix.'auth_rule';
        $auth_rule_sql = <<<sql
-- ----------------------------
-- $auth_rule 规则表，
-- id:主键，name：规则唯一标识, title：规则中文名称 status 状态：为1正常，为0禁用，condition：规则表达式，为空表示存在就验证，不为空表示按照条件验证
-- ----------------------------
CREATE TABLE IF NOT EXISTS `$auth_rule` (
`id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
`name` char(80) NOT NULL DEFAULT '',
`title` char(20) NOT NULL DEFAULT '',
`type` tinyint(1) NOT NULL DEFAULT '1',
`status` tinyint(1) NOT NULL DEFAULT '1',
`condition` char(100) NOT NULL DEFAULT '',  # 规则附件条件,满足附加条件的规则,才认为是有效的规则
PRIMARY KEY (`id`),
UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
sql;

        $create = Db::query($auth_rule_sql);
        $output->writeln($auth_rule . '表创建成功' . json_encode($create, 256));
    }
    private function createAuthGroup(Input $input, Output $output){
        $auth_group = $this->db_prefix.'auth_group';
        $auth_group_sql = <<<sql
-- ----------------------------
-- $auth_group 用户组表，
-- id：主键， title:用户组中文名称， rules：用户组拥有的规则id， 多个规则","隔开，status 状态：为1正常，为0禁用
-- ----------------------------
CREATE TABLE IF NOT EXISTS  `$auth_group` (
`id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
`title` char(100) NOT NULL DEFAULT '',
`status` tinyint(1) NOT NULL DEFAULT '1',
`rules` char(80) NOT NULL DEFAULT '',
PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
sql;

        $create = Db::query($auth_group_sql);
        $output->writeln($auth_group . '表创建成功' . json_encode($create, 256));
    }
    private function createAuthGroupAccess(Input $input, Output $output){
        $auth_group_access = $this->db_prefix.'auth_group_access';

        $auth_group_access_sql = <<<sql
-- ----------------------------
-- $auth_group_access 用户组明细表
-- uid:用户id，group_id：用户组id
-- ----------------------------
CREATE TABLE IF NOT EXISTS `$auth_group_access` (
`uid` mediumint(8) unsigned NOT NULL,
`group_id` mediumint(8) unsigned NOT NULL,
UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
KEY `uid` (`uid`),
KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
sql;
        $create = Db::query($auth_group_access_sql);
        $output->writeln($auth_group_access . '表创建成功' . json_encode($create, 256));

    }

    /*****
     * 设置表前缀
     * @param string $prefix
     */
    private function setDbPrefix(){
        $this->db_prefix = env('DATABASE_PREFIX','');
    }

}