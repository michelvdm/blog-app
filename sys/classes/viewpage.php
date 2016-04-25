<?php defined('BASE') or die('No access');

class ViewPage{

	function __construct( $data ){ $this->data=$data; }

	function renderIndex(){
		extract( $this->data );
		out( '<ul class="list">' );
		foreach( $content as $item ){
			$dt=new DateTime( $item['publishdate'] );
			out( '<li><a href="'.ROOT.'/post/'.$item['slug'].'">' );
			out( '<div class="dateBlock"><b>'.$dt->format('Y').'</b><i>'.$dt->format('d').'</i>'.$dt->format('Y').'</div>' );
			out( '<h2>'.$item['subject'].'</h2>' );
			out( '<p class="info"> Published '.$this->friendlyDate( $dt).'.</p>' );
			out( '<p>'.$item['description'].'</p>' );
			out( '</a></li>' );
		}
		out( '</ul>' );
		$this->renderPagination( $pagination, '/index/' );
	}

	function friendlyDate( $time ){
		$timestamp=$time->getTimestamp();
		$difference=time()-$timestamp;
		$periods=array('second', 'minute', 'hour', 'day', 'week', 'month', 'years');
		$lengths=array( 60, 60, 24, 7, 4.35, 12);
		$ending='ago';
	 	$arr_len = count($lengths);
		for( $j=0; $j<$arr_len && $difference>=$lengths[$j]; $j++) $difference/=$lengths[$j];
		$difference=round($difference);
		if( $difference!=1 ) $periods[$j].= 's';
		$text="$difference $periods[$j] $ending";
	 	if( $j>2 ){
			if( $j==3 && $difference==1 ) $text='yesterday at '. date( 'g:i a', $timestamp );
			else if( $j==3 ) $text='last '.date('l \a\\t g:i a', $timestamp);
			else if( $j<6 && !($j==5 && $difference==12 ) ) $text=date( 'F j \a\\t g:i a', $timestamp );
			else $text=date('F j, Y \a\\t g:i a', $timestamp);
		}
	 	return $text;
	}

	function getLink( $num ){ $link=$this->pagePath.(($num==1)?'':$num); return $link==ROOT.'/index/'?(ROOT.'/'):$link; }
	function tag( $num ){ return $num==$this->here?('<span class="on">'.$num.'</span>'):('<a href="'.$this->getLink($num).'">'.$num.'</a>'); }

	function renderPagination( $val, $path ){
		extract( $val );
		if($numPages<2) return;
		$here=$this->here=$currentPage;
		$base=$this->pagePath=ROOT.$path;
		$lastpage=$numPages;
		$adjacents=3;
		$aa=$adjacents*2;
		$prev='<svg><use xlink:href="'.ROOT.'/inc/icons.svg#leftarrow-icon"/></svg>';
		$next='<svg><use xlink:href="'.ROOT.'/inc/icons.svg#rightarrow-icon"/></svg>';
		$s='<span>...</span>';
		out( '<nav class="pagination">' );
		out( $here>1?( '<a href="'.$this->getLink($here-1).'" class=prev>'.$prev.'</a>' ):('<span class="prev">'.$prev.'</span>') );
		if( $lastpage<$aa+7 ) for( $i=1; $i<=$lastpage; $i++ ) out( $this->tag( $i ) );
		else{
			if( $here<$aa+1 ){
				for( $i=1; $i<4+$aa; $i++ ) out( $this->tag( $i ) );
				out( $s.$this->tag($lastpage-1).$this->tag( $lastpage ) );
			}elseif( $lastpage-$aa>$here && $here>$aa ){	
				out( $this->tag(1).$this->tag(2).$s );
				for( $i=$here-$adjacents; $i<=$here+$adjacents; $i++ ) out( $this->tag( $i ) );
				out( $s.$this->tag( $lastpage-1 ).$this->tag( $lastpage ) );
			}
			else{
				out( $this->tag(1).$this->tag(2).$s );
				for( $i=$lastpage-(2+$aa); $i<=$lastpage; $i++) out( $this->tag( $i ) );
			}
		}
		out( $here<$numPages?( '<a href='.$this->getLink( $here+1 ).' class=next>'.$next.'</a>' ):( '<span class=next>'.$next.'</span>' ) );
		out( '</nav>' );
	}

	function renderPost(){
		$item=$this->data['content'];
		$dt=new DateTime( $item['publishdate'] );
		out( '<p class="info">Published on '.$dt->format('l d F Y').'.</p>' );
		out( '<p class="intro">'.$item['description'].'</p>' );
		out( $item['body'] );
		out( '<nav class="prevNext">' );
		foreach ($this->data['prevNext'] as $key => $value) {
			$isNext=$key=='next';
			if($value) out( '<a href="'.ROOT.'/post/'.$value['slug'].'" class="'.$key.'" title="'.($isNext?'Newer':'Older').'"><svg><use xlink:href="'.ROOT.'/inc/icons.svg#'.($isNext?'right':'left').'arrow-icon"/></svg>'.$value['subject'].'</a>' );
			else out( '<span class="'.$key.'"></span>' );	
		}
		echo '</nav>';
	}

	function renderPage(){ out( $this->data['content'] ); }
	function render404(){ out( $this->data['content'] ); }

	function renderTopNav(){
		$menu=$this->data['menu'];
		$here=$this->data['topActive'];
		$out=array( '<nav><ul>' );
		foreach( $menu as $key=>$item ){
			$class=($key==$here)?' class="on"':'';
			$out[]='<li><a href="'.ROOT.$item['link'].'"'.$class.'>'.$item['label'].'</a></li>';
		}
		$out[]='</ul></nav>';
		$this->process( join( '', $out ) );
	}

	function process( $val ){
		extract( $this->data );
		if( isset($type) && $type=='404' ) http_response_code(404);
		$tmp=split( '{{', $val );
		echo $tmp[0];
		for( $i=1; $i< sizeof($tmp); $i++){
			$tmp2=split( '}}', $tmp[ $i ] );
			switch($tmp2[0]){
				case 'path': echo ROOT; break;
				case 'renderTime': echo round( microtime( true )-START_TIME , 3 ); break;
				case 'meta':
					out( '<title>'.$app['title'].($this->data['request'][0]=='index'?'':' | '.$title ).'</title>' );
					out( '<meta name="description" value="'.( isset( $content['description'] )?$content['description']:$app['desc'] ).'">' );
					break;
				case 'topNav': $this->renderTopNav(); break;
				case 'title': echo isset($title)?$title:'Error: title is missing'; break;
				case 'content':
					$fn='render'.ucfirst($type);
					if( method_exists( __CLASS__,  $fn ) ) call_user_func( array( $this, $fn ) );
					else out( '<p>Error - no page method: '.$fn.'</p>' );
					break;
				default: out( 'Error - no rule for key: '.$tmp2[0] ); 
			}
			echo $tmp2[1];
		}
	}

	function render(){
		$file=BASE.'/content/'.$this->data['template'];
		$this->process( file_get_contents( $file ) );
	}

}
