<div class="navigation_middle">
    <ul>
        <li>
            <?php  echo $this->htmlLink(Engine_Api::_()->sitecourse()->getHref($course['course_id'], $course['owner_id'],null, $course['title']),$this->translate('Course View Page'),array());?>
        </li>
        <li>
            <?php  echo $this->htmlLink(array('route' => 'sitecourse_learning', 'module' => 'sitecourse', 'controller' => 'learning', 'action' => 'index', 'course_id' =>$id,), $this->translate('Course Learning Page')); ?> 
        </li>
    </ul>
</div>
