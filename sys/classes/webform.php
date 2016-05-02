<?php defined('BASE') or die('No access');

class WebForm{

	function start($action=''){ out( "<form method=\"post\" action=\"$action\">" ); }
	function hidden($name, $val){ out( "<input name=\"$name\" value=\"$val\" type=\"hidden\">" ); }
	function input($name, $label, $val='', $opt=''){ out( "<li><label for=\"f$name\">$label</label><input id=\"f$name\" name=\"$name\" value=\"$val\" $opt></li>" ); }
	function area($name, $label, $val='', $opt=''){ out( "<li><label for=\"f$name\">$label</label><textarea id=\"f$name\" name=\"$name\" $opt>$val</textarea></li>" ); }
	function body($name, $label, $val='', $opt=''){ out( "<li class=\"richtext\"><label for=\"f$name\">$label</label><textarea id=\"f$name\" name=\"$name\" $opt>$val</textarea></li>" ); }

	function select($name, $label, $enum, $val='', $opt=''){
		out( "<li><label for=\"f$name\">$label</label><select id=\"f$name\" name=\"$name\" $opt>" );
		foreach($enum as $p){
			$sel=($p==$val)?' selected':'';
			out( "<option value=\"$p\"$sel>$p</option>" );
		}
		out( '</select></li>' );
	}

	function selectTags($enum, $val='', $opt=''){
		$val=explode( ', ', $val );
		out( '<li class="checks"><span class="label">Tags: </span>' );
		foreach ($enum as $key=>$value){
			$name=$value['name'];
			$checked=in_array( $name , $val )?' checked':''; 	
			out( "<input id=\"fTag$name\" name=\"tags[]\" type=\"checkbox\" value=\"$name\"$checked><label for=\"fTag$name\">$name</label>" );
		}
		out('<br><label for="fNewTags" class="label">New tags: </label><input id="fNewTags" name="newTags">' );
		out( '</li>' );
	}

	function end($back='', $label='Submit'){
		$cancel=($back=='')?'':"<a href=\"$back\">Cancel</a> ";
		out( "<div class=\"act\">$cancel<button type=\"submit\">$label</button></div>\n</form>" );
	}

}