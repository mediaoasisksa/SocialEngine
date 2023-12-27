<?php

return array(
    array(
        'title' => 'Course Profile: Announcements',
        'description' => 'Displays the Announcements posted for each course. This widget should be placed on the Course profile page.',
        'category' => 'Course Builder / Learning Management Plugin',
        'type' => 'widget',
        'name' => 'sitecourse.course-announcement',
        'adminForm' => array(
            'elements' => array(),
        ),
        'defaultParams' => array(
            'title' => 'Announcements',
        ),
    ),
    array(
        'title' => 'Browse Courses',
        'description' => 'Display a list of all the approved courses on your site. This widget should be placed on the course browse page.',
        'category' => 'Course Builder / Learning Management Plugin',
        'type' => 'widget',
        'name' => 'sitecourse.browse-courses-sitecourse',
        'requirements' => array(
            'no-subject',
        ),
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                array('Text','itemCount',array(
                    'label' => 'Count ',                        
                    'description' => '(number of courses to show)',
                    'value' => 3,
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
            ),

                array('Text','truncationLimit',array(
                    'label' => 'Truncation Limit',                        
                    'description' => 'Add setting for truncation limit for title.',
                    'value' => 25,
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
            ),
                array('MultiCheckbox','courseInfo',array(
                    'label' => 'Choose',                        
                    'description' => 'Choose the Fields that should visible.',
                    'multiOptions' => array('postedBy'=>'Posted By','enrolledCount'=>'Enrolled Count','category'=>'Category','difficultyLevel'=>'Difficulty Level'
                    ),
                ),
            ),
            ),
        ),
    ),
    array(
        'title' => 'My Courses',
        'description' => 'Displays a list of all the Courses created by a user on the site. This widget should be placed on My Courses page.',
        'category' => 'Course Builder / Learning Management Plugin',
        'type' => 'widget',
        'name' => 'sitecourse.my-courses-sitecourse',
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
        'defaultParams' => array(
            'title' => 'My Courses',
        ),
    ),
    array(
        'title' => 'My Purchased Courses',
        'description' => 'Displays a list of all the Courses purchased by a user on the site. This widget should be placed on My Courses page.',
        'category' => 'Course Builder / Learning Management Plugin',
        'type' => 'widget',
        'name' => 'sitecourse.my-purchased-courses-sitecourse',
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
        'defaultParams' => array(
            'title' => 'My Purchased Courses',
        ),
    ),
    array(
        'title' => 'Course Browse Menu',
        'description' => 'This widget displays the browse menu for ‘Course Builder / Learning Management Plugin’ having links to browse courses, my courses, create new courses.',
        'category' => 'Course Builder / Learning Management Plugin',
        'type' => 'widget',
        'name' => 'sitecourse.browse-menu',
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
    ),

    array(
        'title' => 'Course Profile: Curriculum',
        'description' => 'Displays all the curriculum added for the course. This widget should be placed on the Course Profile page.',
        'category' => 'Course Builder / Learning Management Plugin',
        'type' => 'widget',
        'name' => 'sitecourse.course-curriculum',
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
        'defaultParams' => array(
            'title' => 'Curriculum',
        ),
    ),

    array(
        'title' => 'Course Requirements',
        'description' => 'Displays all the prerequisites of a course. This widget should be placed on the Course Profile page.',
        'category' => 'Course Builder / Learning Management Plugin',
        'type' => 'widget',
        'name' => 'sitecourse.course-requirements',
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
        'defaultParams' => array(
            'title' => 'Requirements',
        ),
    ),

    array(
        'title' => 'Course Profile: Instructor',
        'description' => 'Displays all the details of an Instructor of a particular course. This widget should be placed on the Course Profile page.',
        'category' => 'Course Builder / Learning Management Plugin',
        'type' => 'widget',
        'name' => 'sitecourse.about-instructor',
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
        'defaultParams' => array(
            'title' => 'Instructor',
        ),
    ),
    array(
        'title' => 'Course Profile: Breadcrumb',
        'description' => 'Displays breadcrumb of the course based on the categories, sub-categories. This widget should be placed on the Course profile page.',
        'category' => 'Course Builder / Learning Management Plugin',
        'type' => 'widget',
        'name' => 'sitecourse.course-profile-breadcrumb',
        'adminForm' => array(
            'elements' => array(),
        ),
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => 'Course Learning: Curriculum',
        'description' => 'Displays Course Curriculum on the learning page',
        'category' => 'Course Builder / Learning Management Plugin',
        'type' => 'widget',
        'name' => 'sitecourse.course-learning-curriculum',
        'adminForm' => array(
            'elements' => array(),
        ),
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => 'Course Learning: Content',
        'description' => 'Displays Course Video/Text on the learning page',
        'category' => 'Course Builder / Learning Management Plugin',
        'type' => 'widget',
        'name' => 'sitecourse.course-learning-content',
        'adminForm' => array(
            'elements' => array(),
        ),
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => 'Course Profile: Benefits',
        'description' => 'Displays all the benefits of the courses added by the course owner. This widget should be placed on the Course profile page.',
        'category' => 'Course Builder / Learning Management Plugin',
        'type' => 'widget',
        'name' => 'sitecourse.course-benefits',
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
        'defaultParams' => array(
            'title' => 'Course Benefits',
        ),
    ),
    array(
        'title' => 'Categories Widget',
        'description' => 'Displays the Categories, Sub-categories of courses in an expandable form. Clicking on them will redirect the viewer to the list of courses created in that category.',
        'category' => 'Course Builder / Learning Management Plugin',
        'type' => 'widget',
        'name' => 'sitecourse.categories',
        'defaultParams' => array(
            'title' => 'Categories',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array('Select','showAll',array(                        
                    'description' => 'Do you want all the categories to be shown to the users even if they have 0 courses in them?',
                    'value' => 1,
                    'multiOptions' => array('0'=>'No','1'=>'Yes'),
                ),
            ),
            )
        )
    ),
    array(
        'title' => 'Newest Courses',
        'description' => 'Displays the list of newly created courses based on the threshold value.',
        'category' => 'Course Builder / Learning Management Plugin',
        'type' => 'widget',
        'name' => 'sitecourse.newest-courses',
        'defaultParams' => array(
            'title' => 'Newest Courses',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array('Text','itemCount',array(
                    'label' => 'Count ',                        
                    'description' => '(number of courses to show upto 10)',
                    'value' => 3,
                    'validators' => array(
                        array('Int', true),
                        array('LessThan', true, array(10)),
                    ),
                ),
            ),
                array('Select','sortingCriteria',array(
                    'label' => 'Popularity / Sorting Criteria',   
                    'value' => 0,                     
                    'description' => 'Select which category will be shown in this widget',
                    'multiOptions' => array('0'=>'Creation Date','1'=>'Random'),
                ),
            ),
                array('MultiCheckbox','courseInfo',array(
                    'label' => 'Choose',                        
                    'description' => 'Choose the Fields that should visible on the course.',
                    'multiOptions' => array('postedBy'=>'Posted By','category'=>'Category','difficultyLevel'=>'Difficulty Level'),
                ),
            ),


            ),
        ),
    ),
    array(
        'title' => 'Best Seller Courses',
        'description' => 'Displays the courses based on the best seller threshold value. This Widget should be placed on the Course Browse page',
        'category' => 'Course Builder / Learning Management Plugin',
        'type' => 'widget',
        'name' => 'sitecourse.bestseller-courses',
        'defaultParams' => array(
            'title' => 'Best Seller Courses',
            'titleCount' => true,
        ),

        'adminForm' => array(
            'elements' => array(
                array('Text','itemCount',array(
                    'label' => 'Count ',                        
                    'description' => '(number of courses to show upto 10)',
                    'value' => 3,
                    'validators' => array(
                        array('Int', true),
                        array('LessThan', true, array(11)),
                    ),
                ),
            ),
                array('Select','sortingCriteria',array(
                    'label' => 'Popularity / Sorting Criteria', 
                    'value' => 1,                       
                    'description' => 'Select which category will be shown in this widget',
                    'multiOptions' => array('0'=>'Creation Date','1'=>'Most Enrolled','2'=>'Random'),
                ),
            ),
            array('MultiCheckbox','courseInfo',array(
                    'label' => 'Choose',                        
                    'description' => 'Choose the Fields that should visible.',
                    'multiOptions' => array('postedBy'=>'Posted By','category'=>'Category','difficultyLevel'=>'Difficulty Level'),
                ),
            ),
            ),
        ),
    ),
    array(
        'title' => 'Top Rated Courses',
        'description' => 'Displays a list of Top Rated Courses.',
        'category' => 'Course Builder / Learning Management Plugin',
        'type' => 'widget',
        'name' => 'sitecourse.toprated-courses',
        'defaultParams' => array(
            'title' => 'Top-Rated Courses',
            'titleCount' => true,
        ),

        'adminForm' => array(
            'elements' => array(
                array('Text','itemCount',array(
                    'label' => 'Count ',                        
                    'description' => '(number of courses to show upto 10)',
                    'value' => 3,
                    'validators' => array(
                        array('Int', true),
                        array('LessThan', true, array(11)),
                    ),
                ),
            ),
                array('Select','sortingCriteria',array(
                    'label' => 'Popularity / Sorting Criteria', 
                    'value' => 1,                       
                    'description' => 'Select which category will be shown in this widget',
                    'multiOptions' => array('0'=>'Creation Date','1'=>'Most Rated','2'=>'Random'),
                ),
            ),
                array('MultiCheckbox','courseInfo',array(
                    'label' => 'Choose',                        
                    'description' => 'Choose the Fields that should visible on the course.',
                    'multiOptions' => array('postedBy'=>'Posted By','category'=>'Category','difficultyLevel'=>'Difficulty Level'),
                ),
            ),


            ),
        ),
    ),
    array(
        'title' => 'Course Carousel',
        'description' => 'Displays the courses in an attractive carousel based on the type of courses selected from the edit settings of this widget. This Widget should be placed on the Course Browse page.',
        'category' => 'Course Builder / Learning Management Plugin',
        'type' => 'widget',
        'name' => 'sitecourse.course-carousel',
        'defaultParams' => array(
            'titleCount' => true,
        ),

        'adminForm' => array(
            'elements' => array(
                array('Text','itemCount',array(
                    'label' => 'Count ',                        
                    'description' => '(number of courses to show upto 10)',
                    'value' => 2,
                    'validators' => array(
                        array('Int', true),
                        array('LessThan', true, array(11)),
                    ),
                ),
            ),
                array('Select','courseType',array(                        
                    'description' => 'Select which types of courses should be displayed here.',
                    'value' => 2,
                    'multiOptions' => array('0'=>'Best Seller Only','1'=>'Newest Only','2'=>'Top Rated Only'),
                ),
            ),
                array('Select','sortingCriteria',array(
                    'label' => 'Popularity / Sorting Criteria',  
                    'value' => 1,                      
                    'description' => 'Select which category will be shown in this widget',
                    'multiOptions' => array('0'=>'Creation Date','1'=>'Random'),
                ),
            ),
                array('MultiCheckbox','courseInfo',array(
                    'label' => 'Choose',                        
                    'description' => 'Choose the Fields that should visible on the course.',                    
                    'multiOptions' => array('postedBy'=>'Posted By','category'=>'Category','difficultyLevel'=>'Difficulty Level'),
                ),
            ),
                array('Select','tabletItemCount',array( 
                    'label' => 'Items in Tablet view',  
                    'value' => 1,                    
                    'description' => 'Please select number of items in a slide(Tablet view).',
                    'multiOptions' => array('1'=>'1','2'=>'2','3'=>'3','4'=>'4'),
                ),
            ),
                array('Select','desktopItemCount',array( 
                    'label' => 'Items in Desktop view',                       
                    'description' => 'Please select number of items in a slide(Desktop view).',
                    'value' => 2,
                    'multiOptions' => array('1'=>'1','2'=>'2','3'=>'3','4'=>'4'),
                ),
            ),
                array('Text','carouselSpeed',array(
                    'label' => 'Carousel Speed(In milliSec. and minimum 3000ms)',                        
                    'description' => '(What maximum Carousel Speed should be applied to the widget?)',
                    'value' => 3000,
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(2999)),
                    ),
                ),
            ),


            ),
        ),
    ),
    array(
        'title' => 'Course Full-Width Slider',
        'description' => 'Displays the courses in a Full-width slider view.',
        'category' => 'Course Builder / Learning Management Plugin',
        'type' => 'widget',
        'name' => 'sitecourse.fullwidth-slider-courses',
        'defaultParams' => array(
            'titleCount' => true,
        ),

        'adminForm' => array(
            'elements' => array(
                array('Text','itemCount',array(
                    'label' => 'Count ',                        
                    'description' => '(number of courses to show upto 10)',
                    'value' => 3,
                    'validators' => array(
                        array('Int', true),
                        array('LessThan', true, array(11)),
                    ),
                ),
            ),
                array('Text','sliderHieght',array(
                    'label' => 'Height ',                        
                    'description' => 'Height of Slider in pixels',
                    'value' => 150,
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
            ),
                array('Text','truncationLimit',array(
                    'label' => 'Truncation Limit',                        
                    'description' => 'Add trucation limit for title.',
                    'value' => 20,
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
            ),
                array('Select','coursesCriteria',array(
                    'label' => 'Show in slider',      
                    'value' => 2,                   
                    'description' => 'Select which courses should visible in the slider',
                    'multiOptions' => array('0'=>'Newest Only','1'=>'Top-Rated Only','2'=>'BestSeller Only'),
                ),
            ),
                array('MultiCheckbox','courseInfo',array(
                    'label' => 'Choose',                        
                    'description' => 'Choose the Fields that should visible on the course.',
                    'multiOptions' => array('postedBy'=>'Posted By','enrolledCount'=>'Enrolled Count','category'=>'Category','difficultyLevel'=>'Difficulty Level'),
                ),
            ),


            ),
        ),
    ),
    array(
        'title' => 'Search Courses Form',
        'description' => 'Displays the form for searching Courses on the basis of various filters.',
        'category' => 'Course Builder / Learning Management Plugin',
        'type' => 'widget',
        'name' => 'sitecourse.search-sitecourse',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => 'Show Search Form',
                        'multiOptions' => array(
                            'horizontal' => 'Horizontal',
                            'vertical' => 'Vertical',
                        ),
                        'value' => 'horizontal'
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Course Profile: Overview',
        'description' => 'Displays a rich overview of a particular course. This widget should be placed on the Course Profile page.',
        'category' => 'Course Builder / Learning Management Plugin',
        'type' => 'widget',
        'name' => 'sitecourse.course-overview',
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
        'defaultParams' => array(
            'title' => 'Overview',
        ),
    ),
    array(
        'title' => 'Course Profile: Options',
        'description' => 'Displays the various action links that can be performed on the course profile page. This widget should be placed on the Course Profile page.',
        'category' => 'Course Builder / Learning Management Plugin',
        'type' => 'widget',
        'name' => 'sitecourse.course-options',
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
        'defaultParams' => array(
            'title' => 'Options',
        ),
    ),
    array(
        'title' => 'Course Profile: Promotional Video',
        'description' => 'Displays the promotional video added by the course owner for each course. This widget should be placed on the Course Profile page.',
        'category' => 'Course Builder / Learning Management Plugin',
        'type' => 'widget',
        'name' => 'sitecourse.course-promotional-video',
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
        'defaultParams' => array(
        ),
    ),
    array(
        'title' => 'Course Owner Info',
        'description' => 'Displays the Contact details of the owner.',
        'category' => 'Course Builder / Learning Management Plugin',
        'type' => 'widget',
        'name' => 'sitecourse.course-owner-info',
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(),
        ),
        'defaultParams' => array(
            'title' => 'Owner Info',
        ),
    ),
    array(
        'title' => 'Popular Courses Tags',
        'description' => 'Displays popular tags. You can choose to display tags based on their frequency / alphabets from the Edit Settings of this widget. This widget should be placed on the "Course Browse Page" / "My Courses pages."',
        'category' => 'Course Builder / Learning Management Plugin',
        'type' => 'widget',
        'name' => 'sitecourse.tags-sitecourse',
        'defaultParams' => array(
            'title' => 'Popular Courses Tags',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array('Text','itemCount',array(
                    'label' => 'Count ',                        
                    'description' => '(number of tags to show. Enter 0 for displaying all tags)',
                    'value' => 12,
                    'validators' => array(
                        array('Int', true),
                    ),
                ),
            ),
                array(
                    'Radio',
                    'alphabetical',
                    array(
                        'description' => 'Do you want to show popular courses tags in alphabetical order?',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => '1'
                    )
                ),
            ),
        ),
    ),

    array(
        'title' => 'Course Review',
        'description' => 'Displays a list of all the reviews of the courses. This widget should be placed on the Course Profile page.',
        'category' => 'Course Builder / Learning Management Plugin',
        'type' => 'widget',
        'name' => 'sitecourse.course-review',
        'adminForm' => array(
            'elements' => array(),
        ),
        'defaultParams' => array(
            'title' => 'Course Reviews',
        ),
    ),
    

    array(
        'title' => 'Create New Courses',
        'description' => 'Displays a button ‘Create New Course’ to create a Course on your website.',
        'category' => 'Course Builder / Learning Management Plugin',
        'type' => 'widget',
        'name' => 'sitecourse.newcourse-sitecourse',    
        'adminForm' => array(
            'elements' => array(),
        ),
        'defaultParams' => array(
            'title' => '',
        ),
    ),
);
?>
