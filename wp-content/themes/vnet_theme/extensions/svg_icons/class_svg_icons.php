<?php

class SvgIcons
{
  private $ico_path = CURRENT_PATH . 'img/icons/';
  private $img_path = CURRENT_PATH . 'img/';

  private $ico = array(
    // 'in'      => 'in.svg',
    // 'fb'      => 'fb.svg',
    // 'you'     => 'youtube.svg',
    // 'tel'     => 'telegram.svg',
    // 'login'   => 'login.svg',
    // 'search'  => 'search.svg',
    // 'star'    => 'star.svg'
  );

  function __construct()
  { }

  public function set_ico($key, $value)
  {
    if (isset($this->ico[$key])) {
      $this->ico[$key] = $value;
      return true;
    } else {
      return false;
    }
  }

  public function add_ico($key, $value)
  {
    if (empty($this->ico[$key])) {
      $this->ico[$key] = $value;
      return true;
    } else {
      return true;
    }
  }

  public function set_path($new)
  {
    $this->ico_path = $new;
  }





  private function no_file ( $name ) {
    return '<span style="color:red;">[svg]</span><strong>' . $name . '</strong><span style="color:red;">[/svg]</span>';
  }



  public function get_svg ($path, $hide = false)
  {
    if ( $hide ) return;
    $file = $this->img_path . $path . '.svg';
    if ( !file_exists ( $file ) ) {
      return $this->no_file($path);
    }
    return $this->clear_svg( file_get_contents ( $file ) );
  }


  public function get_ico($name = false)
  {
    if (!$name) return;
    if ($name) {
      if (!isset($this->ico[$name])) {
        if (file_exists($this->ico_path . $name . '.svg')) {
          return $this->clear_svg(file_get_contents($this->ico_path . $name . '.svg'));
        } else {
          return $this->no_file($name);
        }
      }
      if (file_exists($this->ico_path . $this->ico[$name])) {
        return $this->clear_svg(file_get_contents($this->ico_path . $this->ico[$name]));
      } else {
        return $this->no_file($name);
      }
    } else {
      $res = array();

      foreach ($this->ico as $key => $name) {
        if (file_exists($this->ico_path . $this->ico[$name])) {
          $res[$key] = $this->clear_svg(file_get_contents($this->ico_path . $this->ico[$name]));
        } else {
          $res[$key] = $this->no_file($name);
        }
      }

      return $res;
    }
  }





  public function clear_svg($file)
  {
    $file = preg_replace("/<\?xml[^>]+>/u", '', $file);
    $file = preg_replace("/xmlns[\s]*=[\s]*\"[^\"]*\"/u", '', $file);
    $file = preg_replace("/xmlns[\s]*:xlink[\s]*=[\s]*\"[^\"]*\"/u", '', $file);
    $file = preg_replace("/<!--(?!-->).*/u", '', $file);
    $file = preg_replace("/<[\s]*title(?!title[\s]*>).*/u", '', $file);
    $file = preg_replace("/<[\s]*desc(?!desc[\s]*>).*/u", '', $file);
    // $file = preg_replace("/id[\s]*=[\s]*\"[^\"]*\"/u", '', $file);
    return $file;
  }



  public function list_icons($display_name = false)
  {
    $icons = $this->get_icons_path();
    echo '<table>';
    foreach ($icons as $path) {

      if ($display_name) {
        echo '<td>' . $path . '</td>';
      }

      echo '<td>' . file_get_contents($path) . '</td>';
      echo '</tr>';
    }
    echo '</table>';
  }



  // public function remove_fill () {
  //   $icons = $this->get_icons_path();
  //   foreach ( $icons as $ico ) {
  //     $file = file_get_contents( $ico );
  //     $res = preg_replace( '/[\s]*fill[\s]*=[\s]*\"[^\\"]*\"/', '', $file );
  //     file_put_contents( $ico, $res );
  //   }
  // }



  public function get_icons_path()
  {
    $scan = scandir($this->ico_path);
    $res = [];
    foreach ($scan as $file_name) {
      if ($file_name === '.' || $file_name === '..' || !is_file($this->ico_path . $file_name)) continue;
      $ex   = explode('.', $file_name);
      $ext  = $ex[count($ex) - 1];
      if ($ext !== 'svg') continue;
      $res[] = $this->ico_path . $file_name;
    }
    return $res;
  }
}

// class SvgIcons
// {


//   private $ico_path = ABSPATH . 'wp-content/themes/vnet_theme/img/icons/';

//   private $ico = array(
//     'in'      => 'instagram.svg',
//     'fb'      => 'facebook.svg',
//     'you'     => 'youtube.svg',
//     'tel'     => 'telegram.svg',
//     'login'   => 'login.svg',
//     'search'  => 'search.svg',
//     'star'    => 'star.svg'
//   );

//   function __construct()
//   { }

//   public function set_ico($key, $value)
//   {
//     if (isset($this->ico[$key])) {
//       $this->ico[$key] = $value;
//       return true;
//     } else {
//       return false;
//     }
//   }

//   public function add_ico($key, $value)
//   {
//     if (empty($this->ico[$key])) {
//       $this->ico[$key] = $value;
//       return true;
//     } else {
//       return true;
//     }
//   }

//   public function set_path($new)
//   {
//     $this->ico_path = $new;
//   }


//   public function get_ico($name = false)
//   {
//     if ($name) {
//       if ( !isset ( $this->ico[$name] ) ) {
//         if ( file_exists( $this->ico_path . $name . '.svg' ) ) {
//           return  file_get_contents( $this->ico_path . $name . '.svg' );
//         } else {
//           return false;
//         }
//       }
//       if (file_exists($this->ico_path . $this->ico[$name])) {
//         return file_get_contents($this->ico_path . $this->ico[$name]);
//       } else {

//       }
//     } else {
//       $res = array();

//       foreach ($this->ico as $key => $name) {
//         if (file_exists($this->ico_path . $this->ico[$name] ) ) {
//           $res[$key] = file_get_contents($this->ico_path . $this->ico[$name]);
//         } else {
//           $res[$key] = false;
//         }
//       }

//       return $res;
//     }
//   }



//   public function list_icons ( $display_name = false ) {
//     $icons = $this->get_icons_path();
//     echo '<table>';
//     foreach ( $icons as $path ) {

//       if ( $display_name ) {
//         echo '<td>'.$path.'</td>';
//       }

//       echo '<td>'.file_get_contents ( $path ).'</td>';
//       echo '</tr>';
//     }
//     echo '</table>';
//   }



//   // public function remove_fill () {
//   //   $icons = $this->get_icons_path();
//   //   foreach ( $icons as $ico ) {
//   //     $file = file_get_contents( $ico );
//   //     $res = preg_replace( '/[\s]*fill[\s]*=[\s]*\"[^\\"]*\"/', '', $file );
//   //     file_put_contents( $ico, $res );
//   //   }
//   // }



//   public function get_icons_path () {
//     $scan = scandir ( $this->ico_path );
//     $res = [];
//     foreach ( $scan as $file_name ) {
//       if ( $file_name === '.' || $file_name === '..' || !is_file( $this->ico_path . $file_name ) ) continue;
//       $ex   = explode('.', $file_name);
//       $ext  = $ex[count($ex) - 1];
//       if ( $ext !== 'svg' ) continue;
//       $res[] = $this->ico_path . $file_name;
//     }
//     return $res;
//   }

// }
