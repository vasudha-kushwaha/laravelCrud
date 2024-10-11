<?php
namespace App\Http\Traits;
trait dbBasic
{
    use Functions;
    //Now stokenDetails 16 characters are put before id
    public function extractToken($sTokenAndId)
    {
        //0 is Invalid row number. returning NULL causes query failure "select * where id= "
        if(empty($sTokenAndId)) return 'g1a2r3_bage';
        $sToken = substr($sTokenAndId, 0, 16);
        if($sToken === false) return 'g1a2r3_bage';
        else return $this->makeSafe($sToken);
    }

    //Now stokenDetails 32 characters are put before id
    public function extractToken32($sTokenAndId)
    {
        //0 is Invalid row number. returning NULL causes query failure "select * where id= "
        if(empty($sTokenAndId)) return 'g1a2r3_bage';
        $sToken = substr($sTokenAndId, 0, 32);
        if($sToken === false) return 'g1a2r3_bage';
        else return $this->makeSafe($sToken);
    }

    //Now stokenDetails 16 characters are put before id
    public function extractId($sTokenAndId)
    {
        //0 is Invalid row number. returning null causes query failure "select * where id="
        if(empty($sTokenAndId)) return 0;
        $sId = substr($sTokenAndId, 16);
        if($sId === false) return 0;
        else return intval($sId);//makeSafe($sId);
    }

    //Now stokenDetails 32 characters are put before id
    public function extractId32($sTokenAndId)
    {
        //0 is Invalid row number. returning null causes query failure "select * where id="
        if(empty($sTokenAndId)) return 0;
        $sId = substr($sTokenAndId, 32);
        if($sId === false) return 0;
        else return intval($sId);//makeSafe($sId);
    }

}
