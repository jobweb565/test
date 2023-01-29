<?php
class SimPageNav
{
protected $id;
protected $startChar;
protected $prevChar;
protected $nextChar;
protected $endChar;
public function __construct($id = 'pagination pagination-sm', $startChar = '&laquo;', $prevChar  = '&lsaquo;', $nextChar  = '&rsaquo;', $endChar   = '&raquo;')
{
$this->id = $id;
$this->startChar = $startChar;
$this->prevChar  = $prevChar;
$this->nextChar  = $nextChar;
$this->endChar   = $endChar;
}
public function getLinks($all, $limit, $start, $linkLimit = 10, $varName = 'page')
{
if ( $limit >= $all || $limit == 0 ) {
return NULL;
}
$pages = 0;
$needChunk = 0;
$queryVars = array();
$pagesArr = array();
$htmlOut = '';
$link = NULL;
$purl = parse_url('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
if (isset($purl['query'])) { $pqu = $purl['query']; } else { $pqu = ''; }
parse_str($pqu, $queryVars);
if( isset($queryVars[$varName]) ) {
unset( $queryVars[$varName] );
}
$qe = http_build_query($queryVars);
if (empty($qe)) { $qee = ''; } else { $qee = '&'; }
$link  = $purl['path'].'?'.$qe.$qee;
$pages = ceil( $all / $limit );
for( $i = 0; $i < $pages; $i++) {
$pagesArr[$i+1] = $i * $limit+1;
}
$allPages = array_chunk($pagesArr, $linkLimit, true);
$needChunk = $this->searchPage( $allPages, $start );
if ( $start > 1 ) {
$htmlOut .= '<li><a href="'.$link.$varName.'=1">'.$this->startChar.'</a></li><li><a href="'.$link.$varName.'='.($start - $limit).'">'.$this->prevChar.'</a></li>';
} else {
$htmlOut .= '<li class="disabled"><a href="javascript://">'.$this->startChar.'</a></li><li class="disabled"><a href="javascript://">'.$this->prevChar.'</a></li>';
}
foreach( $allPages[$needChunk] AS $pageNum => $ofset )  {
if( $ofset == $start  ) {
$htmlOut .= '<li class="active"><a href="javascript://">'. $pageNum .'</a></li>';
continue;
}
$htmlOut .= '<li><a href="'.$link.$varName.'='. $ofset .'">'. $pageNum . '</a></li>';
}
if ( ($all) >  $start) {
$apop = array_pop($allPages);
$apop2 = array_pop($apop);
$htmlOut .= '<li><a href="'.$link.$varName.'='.( $start + $limit).'">'.$this->nextChar.'</a></li><li><a href="'.$link.$varName.'='.$apop2.'">'.$this->endChar.'</a></li>';
} else {
$htmlOut .= '<li class="disabled"><a href="javascript://">'.$this->nextChar.'</a></li><li class="disabled"><a href="javascript://">'.$this->endChar.'</a></li>';
}
return '<div class="text-center mt-4 mb-2"><ul class="'.$this->id.'">'.$htmlOut.'<ul></div>';
}
protected function searchPage( array $pagesList, /*int*/$needPage )
{
foreach( $pagesList AS $chunk => $pages  ){
if( in_array($needPage, $pages) ){
return $chunk;
}
}
return 0;
}
}