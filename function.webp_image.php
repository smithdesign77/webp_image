<?php
#-------------------------------------------------------------------------
# Plugin: webp_image
# Author: Yuri Haperski (wdwp@yandex.ru)
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2012 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
# The plugin's homepage is: http://dev.cmsmadesimple.org/projects/
#-------------------------------------------------------------------------
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#-------------------------------------------------------------------------

function smarty_function_webp_image($params, &$smarty)
{
  $gCms = CmsApp::get_instance();

  $root_path = $gCms->config['root_path'];

  #Get parameters from the function call
  $src = isset($params['src']) ? str_replace(array($gCms->config['root_url'] . '/', '//'), array('', '/'), $params['src']) : '';

  $original_src = isset($params['src']) ? $params['src'] : '';

  $quality = isset($params['quality']) ? $params['quality'] : 90;

  #Get source path
  $path = $root_path . '/' . urldecode($src);

  if (file_exists($path)) {

    #Get source info
    $pathinfo = pathinfo($path);

    $extension = strtolower($pathinfo['extension']);

    $destination = $root_path . '/tmp/cache/img-' . md5_file($path) . '.webp';

    # If file already exists, return it
    if (file_exists($destination) && strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false)

      return str_replace($root_path . '/', '', $destination);

    if (strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false) {

      if ($extension == 'jpeg' || $extension == 'jpg') $image = imagecreatefromjpeg($path);

      elseif ($extension == 'gif') $image = imagecreatefromgif($path);

      elseif ($extension == 'png') {

        $image = imagecreatefrompng($path);
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);
      } else return "Error: the image extension is wrong";

      #webp image creation
      if ($image !== false) imagewebp($image, $destination, $quality);

      else return "Error: the image has not been created";

      # If the browser supports webp. Hello Safari!
      if (file_exists($destination) && strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false) {

        #Return encoded image
        return str_replace($root_path . '/', '', $destination);
      } else {

        return $original_src;
      }
    } else return $original_src;
  } else {

    return 'Error: the image path is not valid';
  } //end if

} // End Function
/**
 * Help text
 */
function smarty_cms_help_function_webp_image()
{
?>
  <h3>What does this do?</h3>
  <p>This plugin converts an image to webp format.</p>
  <h3>How do I use it?</h3>
  <p>HTML: &lt;img src="{webp_image src="uploads/simplex/images/cmsmadesimple-logo.png" quality="85"}" width="227" height="59"/&gt;</p>
  <p>You can use an absolute or relative url to the image. For Gallery: </p>
  <p>&lt;a class="group" href="{webp_image src=$image->file}" title="{$image->comment}" rel="prettyPhoto[{$galleryid}]"&gt;&lt;img src="{webp_image src=$image->thumb}" alt="{$image->titlename}" /&gt;&lt;/a&gt;</p>

<?php
} // End Function
/**
 * About text
 */
function smarty_cms_about_function_webp_image()
{
?>
  <p><b>Plugin author: Yuri Haperski (wdwp@yandex.ru)</b></p>
  <p><b>Version:</b> 1.0</p>
  <p><b>Change History:</b></p>
  <p><b>20-03-2021 - Initial release (v1.0)</b></p>
  <p><b>20-11-2021 - Some improvements (v1.1)</b></p>
<?php
} // End Function
?>