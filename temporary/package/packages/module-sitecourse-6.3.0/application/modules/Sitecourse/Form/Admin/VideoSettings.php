<?php
class Sitecourse_Form_Admin_VideoSettings extends Engine_Form {
    public function init() {
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $this
        ->setTitle('Video Settings')
        ->setDescription('Following settings will help you to enable options like YouTube, My Computer on your website for this plugin.');

        $this->addElement('Text', 'sitecourse_ffmpeg_path', array(
            'label' => 'Path to FFMPEG',
            'description' => 'Please enter the full path to your FFMPEG installation. (Environment variables are not present)',
            'value' => $coreSettings->getSetting('sitecourse.ffmpeg.path', ''),
        ));

        $description = 'While posting videos on your site, users can choose YouTube as a source. This requires a valid YouTube API key.<br>To learn how to create that key with correct permissions, read our <a href=" https://developers.google.com/youtube/v3/getting-started" target="_blank">KB Article</a>';

        $currentYouTubeApiKey = '******';
        if (!_ENGINE_ADMIN_NEUTER) {
            $currentYouTubeApiKey = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.youtube.apikey', $coreSettings->getSetting('video.youtube.apikey'));
        }
        $this->addElement('Text', 'sitecourse_youtube_apikey', array(
            'label' => 'YouTube API Key',
            'description' => $description,
            'filters' => array(
                'StringTrim',
            ),
            'value' => $currentYouTubeApiKey,
        ));
        $this->sitecourse_youtube_apikey->getDecorator('Description')->setOption('escape', false);
        
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}

?>
