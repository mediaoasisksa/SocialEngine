
<?php
  $photoClass = 'blogs_gutter_photo';

  if( $this->blog && $this->blog->photo_id ):
    // blog photo
    echo $this->htmlLink($this->blog->getHref(),
    $this->itemPhoto($this->blog),
    array('class' => $photoClass));

    $ownerPhoto = $this->itemBackgroundPhoto($this->owner, 'thumb.icon');
    $photoClass = 'blogs_owner_icon';
  endif;

  if( !isset($ownerPhoto) ):
    $ownerPhoto = $this->itemBackgroundPhoto($this->owner);
  endif;

  // owner photo
  echo $this->htmlLink($this->owner->getHref(), $ownerPhoto, array('class' => $photoClass));

  echo $this->htmlLink($this->owner->getHref(), $this->owner->getTitle(), array('class' => 'blogs_gutter_name'));
