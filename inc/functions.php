<?php
function replaceBBCode($text_post) {
    $str_search = array(
      "#\\\n#is",
      "#\[b\](.+?)\[\/b\]#is",
      "#\[i\](.+?)\[\/i\]#is",
      "#\[u\](.+?)\[\/u\]#is",
      "#\[code\](.+?)\[\/code\]#is",
      "#\[quote\](.+?)\[\/quote\]#is",
      "#\[url=(.+?)\](.+?)\[\/url\]#is",
      "#\[url\](.+?)\[\/url\]#is",
      "#\[img\](.+?)\[\/img\]#is",
      "#\[size=(.+?)\](.+?)\[\/size\]#is",
      "#\[color=(.+?)\](.+?)\[\/color\]#is",
      "#\[list\](.+?)\[\/list\]#is",
      "#\[listn](.+?)\[\/listn\]#is",
      "#\[\*\](.+?)\[\/\*\]#"
    );
    $str_replace = array(
      "<br />",
      "<b>\\1</b>",
      "<i>\\1</i>",
      "<span style='text-decoration:underline'>\\1</span>",
      "<code class='code'>\\1</code>",
      "<table width = '95%'><tr><td>Цитата</td></tr><tr><td class='quote'>\\1</td></tr></table>",
      "<a href='\\1'>\\2</a>",
      "<a href='\\1'>\\1</a>",
      "<img src='\\1' alt = 'Изображение' />",
      "<span style='font-size:\\1%'>\\2</span>",
      "<span style='color:\\1'>\\2</span>",
      "<ul>\\1</ul>",
      "<ol>\\1</ol>",
      "<li>\\1</li>"
    );
    return preg_replace($str_search, $str_replace, $text_post);
  }

function GetIn ($str, $st, $en, $match, $withtag)
{
  $str_old=$str;  if (!$match) {       $str=mb_strtoupper($str, "utf-8");
       $st=mb_strtoupper($st, "utf-8");
       $en=mb_strtoupper($en, "utf-8");
     }
  $i=mb_strpos($str,$st,0,"utf-8");
  if ($i===false) {  	  return "";
  	}
  $ie=mb_strpos($str,$en,$i+mb_strlen($st,"utf-8"),"utf-8");
  if ($ie===false) {  	  return "";
  	}
  if ($withtag) {
       $ie=$ie+mb_strlen($en,"utf-8");
     }
       else
         {
           $i=$i+mb_strlen($st,"utf-8");
         }
  return mb_substr($str_old,$i, $ie-$i,"utf-8");
}
function ChangeIn ($str, $st, $en, $match, $withtag, $newstr)
{
  $str_old=$str;
  if (!$match) {
       $str=mb_strtoupper($str, "utf-8");
       $st=mb_strtoupper($st, "utf-8");
       $en=mb_strtoupper($en, "utf-8");
     }
  $i=mb_strpos($str,$st,0,"utf-8");
  if ($i===false) {
  	  return "";
  	}
  $ie=mb_strpos($str,$en,$i+mb_strlen($st,"utf-8"),"utf-8");
  if ($ie===false) {
  	  return "";
  	}
  if ($withtag) {
       $ie=$ie+mb_strlen($en,"utf-8");
     }
       else
         {
           $i=$i+mb_strlen($st,"utf-8");
         }
  $k=mb_substr($str_old,$i, $ie-$i,"utf-8");
  return mb_substr($str_old,0,$i,"utf-8").$newstr.mb_substr($str_old,$ie,mb_strlen($str,"utf-8"),"utf-8");
}

function RemoveSlashes ($t)
{
  return mb_ereg_replace('\\\"','"',$t);
}

function encodestring($st)
{

$arr = array(
' ' => '_',
'`' => '',
'"' => '',
'&' => '_',
'#' => '_',
'%' => '_',
'@' => '_',
'’' => '_',
'!' => '_',
'/' => '_',
'\\' => '_',
',' => '_',
'-' => '_',
'–' => '_',
'.' => '',
'(' => '_',
')' => '_',
'{' => '_',
'}' => '_',
'=' => '_',
'*' => '_',
'^' => '_',
'<' => '_',
'>' => '_',
'?' => '_',
'+' => '_',
'[' => '_',
']' => '_',
':' => '_',
';' => '_',
"'" => '_',
"»" => '',
"«" => '',
'А' => 'A',
'Б' => 'B',
'В' => 'V',
'Г' => 'G',
'Д' => 'D',
'Е' => 'E',
'Ё' => 'JO',
'Ж' => 'ZH',
'З' => 'Z',
'И' => 'I',
'Й' => 'JJ',
'К' => 'K',
'Л' => 'L',
'М' => 'M',
'Н' => 'N',
'О' => 'O',
'П' => 'P',
'Р' => 'R',
'С' => 'S',
'Т' => 'T',
'У' => 'U',
'Ф' => 'F',
'Х' => 'KH',
'Ц' => 'C',
'Ч' => 'CH',
'Ш' => 'SH',
'Щ' => 'SHH',
'Ъ' => '',
'Ы' => 'Y',
'Ь' => '',
'Э' => 'EH',
'Ю' => 'JU',
'Я' => 'JA',
'а' => 'a',
'б' => 'b',
'в' => 'v',
'г' => 'g',
'д' => 'd',
'е' => 'e',
'ё' => 'jo',
'ж' => 'zh',
'з' => 'z',
'и' => 'i',
'й' => 'jj',
'к' => 'k',
'л' => 'l',
'м' => 'm',
'н' => 'n',
'о' => 'o',
'п' => 'p',
'р' => 'r',
'с' => 's',
'т' => 't',
'у' => 'u',
'ф' => 'f',
'х' => 'kh',
'ц' => 'c',
'ч' => 'ch',
'ш' => 'sh',
'щ' => 'shh',
'ъ' => '',
'ы' => 'y',
'ь' => '',
'э' => 'eh',
'ю' => 'ju',
'я' => 'ja'
);
$key = array_keys($arr);
$val = array_values($arr);
$transl = str_replace($key,$val,$st );
for ($i=0;$i<strlen($transl);$i++)
{
  $k=strtolower(substr($transl,$i, 1));
  if (!(($k>="a"&&$k<="z")||($k=="_")||($k>="0"&&$k<="9")))
  {
     //echo "$i. $k<br>";
     $transl=substr_replace($transl,'_',$i, 1);
  }
}
$transl = str_replace("__","_",$transl );
$transl = str_replace("__","_",$transl );
$transl = str_replace("__","_",$transl );
$transl = str_replace("__","_",$transl );
$transl = str_replace("__","_",$transl );
$transl = str_replace("__","_",$transl );
$transl = str_replace("__","_",$transl );
$transl = str_replace("_","-",$transl );

    return nl2br(htmlspecialchars($transl));
}

?>