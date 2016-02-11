<?php 
namespace Home\Model;
use Think\Model;
/**
 +------------------------------------------------------------------------------
 * 权限数据访问
 +------------------------------------------------------------------------------
 * @author liu21st <liu21st@gmail.com>
 * @version  $Id: GroupDao.class.php 142 2007-06-15 03:28:16Z liu21st $
 +------------------------------------------------------------------------------
 */
class GroupModel extends Model
{//类定义开始

function setGroupApp($groupId,$appId) 
{
    $table = C("DB_PREFIX").'access';

    $result = $this->execute('insert into '.$table.' values('.$groupId.','.$actionId.')');
    if($result===false) {
        return false;
    }else {
        return true;
    }
}
function setGroupApps($groupId,$appIdList) 
{
        if(empty($appIdList)) {
        return true;
    }
    $id = implode(',',$appIdList);
    $where = 'a.id ='.$groupId.' AND b.id in('.$id.')';
    $rs = $this->execute('INSERT INTO '.C("DB_PREFIX").'access (groupId,nodeId,parentNodeId,level) SELECT a.id, b.id,b.pid,b.level FROM '.C("DB_PREFIX").'group a, '.C("DB_PREFIX").'node b WHERE '.$where);
    if($result===false) {
        return false;
    }else {
        return true;
    }
}


function delGroupApp($groupId) 
{
    $table = C("DB_PREFIX").'access';

    $result = $this->execute('delete from '.$table.' where level=1 and groupId='.$groupId);
    if($result===false) {
        return false;
    }else {
        return true;
    }
}

function delGroupAction($groupId,$moduleId) 
{
    $table = C("DB_PREFIX").'access';

    $result = $this->execute('delete from '.$table.' where level=3 and parentNodeId='.$moduleId.' and groupId='.$groupId);
    if($result===false) {
        return false;
    }else {
        return true;
    }
}

function getGroupActionList($groupId,$moduleId) 
{
    $table = C("DB_PREFIX").'access';
    $rs = $this->query('select b.id,b.title,b.name from '.$table.' as a ,'.C("DB_PREFIX").'node as b where a.nodeId=b.id and  b.pid='.$moduleId.' and  a.groupId='.$groupId.' ',false);
    return $rs;
}


function delGroupModule($groupId,$appId) 
{
    $table = C("DB_PREFIX").'access';

    $result = $this->execute('delete from '.$table.' where level=2 and parentNodeId='.$appId.' and groupId='.$groupId);
    if($result===false) {
        return false;
    }else {
        return true;
    }
}

function getGroupAppList($groupId) 
{
    $table = C("DB_PREFIX").'access';
    $rs = $this->query('select b.id,b.title,b.name from '.$table.' as a ,'.C("DB_PREFIX").'node as b where a.nodeId=b.id and  b.pid=0 and a.groupId='.$groupId.' ',false);
    return $rs;
	//return $this->rsToVo($rs);
}

function getGroupModuleList($groupId,$appId) 
{
    $table = C("DB_PREFIX").'access';
    $rs = $this->query('select b.id,b.title,b.name from '.$table.' as a ,'.C("DB_PREFIX").'node as b where a.nodeId=b.id and  b.pid='.$appId.' and a.groupId='.$groupId.' ',false);
    return $rs;
	//return $this->rsToVo($rs);
}

function setGroupUser($groupId,$userId) 
{
    $table = C("DB_PREFIX").'groupuser';

    $result = $this->execute('insert into '.$table.' values('.$groupId.','.$userId.')');
    if($result===false) {
        return false;
    }else {
        return true;
    }
}

function delGroupUser($groupId) 
{
    $table = C("DB_PREFIX").'groupuser';

    $result = $this->execute('delete from '.$table.' where groupId='.$groupId);
    if($result===false) {
        return false;
    }else {
        return true;
    }
}

function getGroupUserList($groupId) 
{
    $table = C("DB_PREFIX").'groupuser';
    $rs = $this->query('select b.id,b.account from '.$table.' as a ,'.C("DB_PREFIX").'user as b where a.userId=b.id and  a.groupId='.$groupId.' ',false);
    return $rs;
	//return $this->rsToVo($rs);
}

function setGroupUsers($groupId,$userIdList) 
{
    if(empty($userIdList)) {
        return true;
    }
    $id = implode(',',$userIdList);
    $where = 'a.id ='.$groupId.' AND b.id in('.$id.')';
    $rs = $this->execute('INSERT INTO '.C("DB_PREFIX").'groupuser (groupId,userId) SELECT a.id, b.id FROM '.C("DB_PREFIX").'group a, '.C("DB_PREFIX").'user b WHERE '.$where);
    if($result===false) {
        return false;
    }else {
        return true;
    }
}

function setGroupActions($groupId,$actionIdList) 
{
        if(empty($actionIdList)) {
        return true;
    }
    $id = implode(',',$actionIdList);
    $where = 'a.id ='.$groupId.' AND b.id in('.$id.')';
    $rs = $this->execute('INSERT INTO '.C("DB_PREFIX").'access (groupId,nodeId,parentNodeId,level) SELECT a.id, b.id,b.pid,b.level FROM '.C("DB_PREFIX").'group a, '.C("DB_PREFIX").'node b WHERE '.$where);
    if($result===false) {
        return false;
    }else {
        return true;
    }
}

function setGroupModules($groupId,$moduleIdList) 
{
    if(empty($moduleIdList)) {
        return true;
    }
    $id = implode(',',$moduleIdList);
    $where = 'a.id ='.$groupId.' AND b.id in('.$id.')';
    $rs = $this->execute('INSERT INTO '.C("DB_PREFIX").'access (groupId,nodeId,parentNodeId,level) SELECT a.id, b.id,b.pid,b.level FROM '.C("DB_PREFIX").'group a, '.C("DB_PREFIX").'node b WHERE '.$where);
    if($result===false) {
        return false;
    }else {
        return true;
    }
}
}//类定义结束
?>