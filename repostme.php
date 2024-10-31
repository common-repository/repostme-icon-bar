<?php
/*
Plugin Name: Repost.Me Icon Bar
Plugin URI: http://repostme.darkain.com/
Description: Powered By: <a href="http://Repost.Me/">Repost.Me</a> --- A widget that appends image buttons at the end of each of your WordPress blog enteries for viewers to easily Repost links for your content on various social network web sites such as: <a href="http://twitter.com/">Twitter</a> - <a href="http://www.facebook.com/">Facebook</a> - <a href="http://www.myspace.com/">MySpace</a> - <a href="http://www.digg.com/">Digg</a> - <a href="http://www.livejournal.com/">LiveJournal</a> - <a href="http://delicious.com/">Del.icio.us</a> - <a href="http://www.google.com/buzz">Google Buzz</a> - and many others!
Version: 0.3
Author: Darkain Multimedia
Author URI: http://www.darkain.com/
License: BSD
*/


//Main class/namespace for RepostMe Icon Bar
class RepostMe {

  //Main constructor - Used to defined all hooks
  function __construct() {
    add_filter('the_content', array(&$this, 'append_repostme'), 9999999);

    add_action('admin_menu', array(&$this, 'register_settings_page'));
    add_action('admin_init', array(&$this, 'add_settings'));

    $plugin = plugin_basename(__FILE__);
    add_filter("plugin_action_links_$plugin", array(&$this, 'add_action_links'));
  }


  //Append the RepostMe Icon Bar at the end of the post
  function append_repostme($content) {
    global $post;

    $options   = $this->get_options();
    $permalink = get_permalink($post->ID);

    $str  = "\r\n";
    $str .= '<div>';

    //Load JavaScript from the main Repost.Me API server
    $str .= '<script type="text/javascript" src="http://api.repost.me/' . $options['build'] . '"></script>' . "\r\n";
    $str .= '<script type="text/javascript"><!--' . "\r\n";
    $str .= "repostme_size=$options[size];\r\n";
    $str .= "repostme_border='$options[border]';\r\n";
    $str .= "repostme_background='$options[background]';\r\n";
    $str .= "repostme_color='$options[color]';\r\n";
    $str .= "repostme_fade='$options[fade]';\r\n";
    $str .= "repostme_text='" . str_replace("'", "\'", $options['text']) . "';\r\n";
    $str .= "repostme_buttons=['" . implode("','", $options['buttons']) . "'];\r\n";
    $str .= 'repostme_bar(';
      $str .= '"' . htmlspecialchars($permalink) . '",';
      $str .= '"' . htmlspecialchars($post->post_title) . '"';
    $str .= ');' . "\r\n";
    $str .= '//--></script>' . "\r\n";

    $str .= '</div>';

    return $content . $str;
  }


  function settings_page() {
    $options = $this->get_options();

?>
<h2><?php _e(self::$title . ' Settings', self::$name); ?></h2>
<form id="smer_form" method="post" action="options.php"><div>
<?php settings_fields(self::$name); ?>

  <table class="form-table">

    <tr>
      <th>Build Version</th>
      <td>
        <select name="<?php echo self::$option; ?>[build]">
          <option <?php if ($options['build']=='stable.js')  echo 'selected="selected"'; ?> value="stable.js">stable.js</option>
          <option <?php if ($options['build']=='edge.js')    echo 'selected="selected"'; ?> value="edge.js">edge.js</option>
          <option <?php if ($options['build']=='testing.js') echo 'selected="selected"'; ?> value="testing.js">testing.js</option>
        </select> Default: <i>stable.js</i>
        <br />
        <ul>
          <li><b>stable.js</b> -
            The most stable version of the three builds.  Older, but the most
            tested of the three and currently usable in a production environment.
          <br /><br /></li>
     
          <li><b>edge.js</b> -
            Slightly newer than stable, but may have less testing and may
            contain minor bugs. 
          <br /><br /></li>
     
          <li><b>testing.js</b> -
            The latest and greatest of all features.  This build is
            the absolute newest features available for the API, however it has very little
            testing.  Good for experimental developers, bad for production environments.
          </li>
        </ul>
      </td>
    </tr>

    <tr>
      <th>Button Size</th>
      <td>
        <select name="<?php echo self::$option; ?>[size]">
          <option <?php if ($options['size']=='16') echo 'selected="selected"'; ?> value="16">Small (16px)</option>
          <option <?php if ($options['size']=='32') echo 'selected="selected"'; ?> value="32">Large (32px)</option>
        </select> Default: <i>Small</i>
      </td>
    </tr>

    <tr>
      <th>Text Color</th>
      <td>
        #<input type="text" value="<?php echo htmlspecialchars($options['color']); ?>" size="7" name="<?php echo self::$option; ?>[color]" />
        Default: <i>5A5E5C</i>
      </td>
    </tr>

    <tr>
      <th>Border Color</th>
      <td>
        #<input type="text" value="<?php echo htmlspecialchars($options['border']); ?>" size="7" name="<?php echo self::$option; ?>[border]" />
        Default: <i>5A5E5C</i>
      </td>
    </tr>

    <tr>
      <th>Background Color</th>
      <td>
        #<input type="text" value="<?php echo htmlspecialchars($options['background']); ?>" size="7" name="<?php echo self::$option; ?>[background]" />
        Default: <i>DCE6E2</i>
      </td>
    </tr>

    <tr>
      <th>Background Fade Color</th>
      <td>
        <select name="<?php echo self::$option; ?>[fade]">
          <option <?php if ($options['fade']=='white')   echo 'selected="selected"'; ?> value="white">White</option>
          <option <?php if ($options['fade']=='grey')    echo 'selected="selected"'; ?> value="grey">Grey</option>
          <option <?php if ($options['fade']=='black')   echo 'selected="selected"'; ?> value="black">Black</option>
          <option <?php if ($options['fade']=='red')     echo 'selected="selected"'; ?> value="red">Red</option>
          <option <?php if ($options['fade']=='green')   echo 'selected="selected"'; ?> value="green">Green</option>
          <option <?php if ($options['fade']=='blue')    echo 'selected="selected"'; ?> value="blue">Blue</option>
          <option <?php if ($options['fade']=='cyan')    echo 'selected="selected"'; ?> value="cyan">Cyan</option>
          <option <?php if ($options['fade']=='magenta') echo 'selected="selected"'; ?> value="magenta">Magenta</option>
          <option <?php if ($options['fade']=='yellow')  echo 'selected="selected"'; ?> value="yellow">Yellow</option>
          <option <?php if ($options['fade']=='none')    echo 'selected="selected"'; ?> value="none">None</option>
        </select> Default: <i>White</i>
      </td>
    </tr>

    <tr>
      <th>Text</th>
      <td>
        <input type="text" value="<?php echo htmlspecialchars($options['text']); ?>" size="40" name="<?php echo self::$option; ?>[text]" /><br />
        Default: "Click to tell your &lt;b&gt;{site}&lt;/b&gt; friends about '&lt;i&gt;{title}&lt;/i&gt;'"
      </td>
    </tr>

    <tr>
      <th>Buttons</th>
      <td>
      <?php
        for ($i=0; $i<20;$i++) {
          $value = (isset($options['buttons'][$i]) ? $options['buttons'][$i] : '');
          echo '<select name="' . self::$option . '[buttons][' . $i . ']">';
            echo '<option ' . ($value==''?'selected="selected"':'') . 'value="">[SPACER]</option>';
            echo '<option ' . ($value=='Bebo'?'selected="selected"':'') . 'value="Bebo">Bebo</option>';
            echo '<option ' . ($value=='Delicious'?'selected="selected"':'') . 'value="Delicious">Delicious</option>';
            echo '<option ' . ($value=='Digg'?'selected="selected"':'') . 'value="Digg">Digg</option>';
            echo '<option ' . ($value=='Facebook'?'selected="selected"':'') . 'value="Facebook">Facebook</option>';
            echo '<option ' . ($value=='FeedBurner'?'selected="selected"':'') . 'value="FeedBurner">FeedBurner</option>';
            echo '<option ' . ($value=='FriendFeed'?'selected="selected"':'') . 'value="FriendFeed">FriendFeed</option>';
            echo '<option ' . ($value=='Google'?'selected="selected"':'') . 'value="Google">Google Buzz</option>';
            echo '<option ' . ($value=='LinkedIn'?'selected="selected"':'') . 'value="LinkedIn">LinkedIn</option>';
            echo '<option ' . ($value=='LiveJournal'?'selected="selected"':'') . 'value="LiveJournal">LiveJournal</option>';
            echo '<option ' . ($value=='MySpace'?'selected="selected"':'') . 'value="MySpace">MySpace</option>';
            echo '<option ' . ($value=='Reddit'?'selected="selected"':'') . 'value="Reddit">Reddit</option>';
            echo '<option ' . ($value=='StumbleUpon'?'selected="selected"':'') . 'value="StumbleUpon">StumbleUpon</option>';
            echo '<option ' . ($value=='Technorati'?'selected="selected"':'') . 'value="Technorati">Technorati</option>';
            echo '<option ' . ($value=='Twitter'?'selected="selected"':'') . 'value="Twitter">Twitter</option>';
            echo '<option ' . ($value=='Yahoo'?'selected="selected"':'') . 'value="Yahoo">Yahoo</option>';
          echo '</select> ';
        }
      ?>
      </td>
    </tr>

    <tr>
      <th></th>
      <td>
        <input type="submit" name="<?php echo self::$name; ?>-submit" class="button-primary" value="<?php _e('Save Changes', self::$name) ?>" />
      </td>
    </tr>

</table>

</div></form>
<?php
    //Display credits in Footer
    add_action( 'in_admin_footer', array(&$this, 'add_footer_links'));
  }


  function add_action_links( $links ) {
    $settings_link = '<a href="options-general.php?page=' . self::$name . '">' . __('Settings', self::$name) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
  }


  function add_settings() {
    register_setting( self::$name, self::$option);
  }


  function register_settings_page() {
    add_options_page(
      __(self::$title, self::$name),
      __(self::$title, self::$name),
      8, self::$name, array(&$this, 'settings_page')
    );
  }


  function get_options() {
    $option = get_option(self::$option);
    if (!is_array($option)) $option = array();

    //Verify the option for 'build'
    if (!isset($option['build'])) $option['build'] = 'stable.js';
    if ($option['build'] != 'stable.js'  && 
        $option['build'] != 'edge.js'    &&
        $option['build'] != 'testing.js') {
      $option['build'] = 'stable.js';
    }

    //Verify the option for 'size'
    if (!isset($option['size'])) $option['size'] = '16';
    if ($option['size'] != '16'  && 
        $option['size'] != '32') {
      $option['size'] = '16';
    }

    //Verify text colour
    //TODO: validate colours!!
    if (!isset($option['color'])) $option['color'] = '5A5E5C';

    //Verify border colour
    if (!isset($option['border'])) $option['border'] = '5A5E5C';

    //Verify background colour
    if (!isset($option['background'])) $option['background'] = 'DCE6E2';

    //Verify the option for 'fade'
    if (!isset($option['fade'])) $option['fade'] = 'white';
    if ($option['fade'] != 'white'   && 
        $option['fade'] != 'grey'    &&
        $option['fade'] != 'black'   &&
        $option['fade'] != 'red'     &&
        $option['fade'] != 'green'   &&
        $option['fade'] != 'blue'    &&
        $option['fade'] != 'cyan'    &&
        $option['fade'] != 'magenta' &&
        $option['fade'] != 'yellow'  &&
        $option['fade'] != 'none') {
      $option['fade'] = 'white';
    }

    if (!isset($option['text'])  ||  $option['text'] == '') {
      $option['text'] = 'Click to tell your <b>{site}</b> friends about "<i>{title}</i>"';
    }

    if (!isset($option['buttons'])  ||  
        !is_array($option['buttons'])  ||  
        count($option['buttons']) == 0) {
      $option['buttons'] = array(
        'Twitter', 'Facebook', 'MySpace', '', 'Bebo', 'Delicious',
        'Digg', 'FeedBurner', 'FriendFeed', 'Google', 'LinkedIn',
        'LiveJournal', 'Reddit', 'StumbleUpon', 'Technorati', 'Yahoo'
      );
    }

    for ($i=0; $i<count($option['buttons']); $i++) {
      if ($option['buttons'][$i] != 'Bebo'        &&  $option['buttons'][$i] != 'Delicious'   && 
          $option['buttons'][$i] != 'Digg'        &&  $option['buttons'][$i] != 'Facebook'    && 
          $option['buttons'][$i] != 'FeedBurner'  &&  $option['buttons'][$i] != 'FriendFeed'  && 
          $option['buttons'][$i] != 'Google'      &&  $option['buttons'][$i] != 'LinkedIn'    && 
          $option['buttons'][$i] != 'LiveJournal' &&  $option['buttons'][$i] != 'MySpace'     && 
          $option['buttons'][$i] != 'Reddit'      &&  $option['buttons'][$i] != 'StumbleUpon' && 
          $option['buttons'][$i] != 'Technorati'  &&  $option['buttons'][$i] != 'Twitter'     && 
          $option['buttons'][$i] != 'Yahoo'       &&  $option['buttons'][$i] != '') {
        $option['buttons'][$i] = '';
      }
    }

    for ($i=count($option['buttons']); $i>0; --$i) {
      if ($option['buttons'][$i] == '') {
        unset($option['buttons'][$i]);
      } else {
        break;
      }
    }

    return $option;
  }


  //Adds Footer links. Based on http://striderweb.com/nerdaphernalia/2008/06/give-your-wordpress-plugin-credit/
  function add_footer_links() {
    $plugin_data = get_plugin_data( __FILE__ );
    printf('%1$s ' . __('plugin', self::$name)
      . ' | ' . __('Version', self::$name)
      . ' %2$s | '. __('by', self::$name)
      . ' %3$s<br />',
      $plugin_data['Title'],
      $plugin_data['Version'],
      $plugin_data['Author']
    );
  }

  //PHP4 compatibility
  function RepostMe() {
    $this->__construct();
  }

  private static $name   = 'repostme-icon-bar';
  private static $option = 'repostme-options';
  private static $short  = 'Repost.Me';
  private static $title  = 'Repost.Me Icon Bar';
}


// Start this plugin once all other plugins are fully loaded
add_action('init', 'RepostMe_Init');

function RepostMe_Init() {
  global $RepostMe;
  $RepostMe = new RepostMe();
}


?>
