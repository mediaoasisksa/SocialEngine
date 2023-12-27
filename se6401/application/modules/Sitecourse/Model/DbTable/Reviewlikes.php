<?php   
class Sitecourse_Model_DbTable_Reviewlikes extends Engine_Db_Table
{
    protected $_rowClass = 'Sitecourse_Model_Reviewlike';

    /**
     * @param {int} course id,user id
     * @return {int} 1 = liked, 2 = disliked, 0 = Not liked or disliked 
     */
    public function checkReviewLike($review_id,$user_id) {
        $checkLiked = $this->select()
        ->from($this->info('name'),array('value'))
        ->where('review_id = ?', $review_id)
        ->where('user_id = ?', $user_id)
        ->query()
        ->fetchColumn();
        if ($checkLiked == 1 || $checkLiked == 2)
            return $checkLiked;
        else
            return 0;
    }

    /**
     * @param {int} course id,value { 1 = liked , 2 = disliked }
     * @return array{int} total liked count and disliked count 
     */
    public function setReviewLike($review_id,$value) {

        $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();        

        $reviewlikeTableName = $this->info('name');
        $select = $this->select()
        ->from($reviewlikeTableName)
        ->where($reviewlikeTableName . '.review_id = ?', $review_id)
        ->where($reviewlikeTableName . '.user_id = ?', $user_id);
        $row = $this->fetchRow($select);

        $reviewTable = Engine_Api::_()->getDbTable('reviews','sitecourse');
        $review = Engine_Api::_()->getItem('sitecourse_review',$review_id);
        $likeCnt  = $review['like_count'];
        $dislikeCnt = $review['dislike_count'];
        $reviewName = $reviewTable->info('name');
        if(empty($row)) {
            // create rating
            $this->insert(array(
                'review_id' => $review_id,
                'user_id' => $user_id,
                'value' => $value
            ));
            if($value == 1) $likeCnt += 1;
            else $dislikeCnt += 1;
            $reviewTable->update(array('like_count'=> $likeCnt,'dislike_count'=> $dislikeCnt), array('review_id = ?'=>$review_id));
        } else {
            $this->update(array(  
                'value' => $value,
            ), array('review_id=?' => $review_id,'user_id=?' => $user_id,));
            if($value == 1) {
                $likeCnt += 1;
                $dislikeCnt -= 1;
            }
           else {
                $dislikeCnt += 1;
                $likeCnt -= 1;
            } 
            if($dislikeCnt < 0) $dislikeCnt = 0;
            if($likeCnt < 0) $likeCnt = 0;
            $reviewTable->update(array('like_count'=> $likeCnt,'dislike_count'=> $dislikeCnt), array('review_id = ?'=>$review_id));
        }

        return array('like_count'=>$likeCnt,'dislike_count'=>$dislikeCnt);
    }


}
?>
