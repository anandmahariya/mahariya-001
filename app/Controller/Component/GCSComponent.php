<?php
class GCSComponent extends Component {
    
    public $components = array('Common');
    var $token = "AIzaSyCWfp9e5tUEgwcUac8PyzNEOEBabyrRflE"; 
    var $result_format = "json"; 
    var $rpp = 10; 
    
      
    private function get_result_xml($uri,$debug=false){  
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $uri); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_REFERER']); 
        $body = curl_exec($ch); 
        curl_close($ch); 
        
        echo $body;
        exit;
        
        if(!$doc = new SimpleXmlElement($body, LIBXML_NOCDATA)) 
        { 
            echo "Could not parse the xml.";     
        } 
         
        if($debug){debug($doc);} 
         
        return $doc; 
    } 
     
    /** 
     * Retrieves the spelling suggestion - if there is one 
     * 
     * @xml = the simple xml object 
     **/ 
     private function get_spelling_suggestion($xml) 
     { 
         return strip_tags($xml->Spelling->Suggestion); 
     } 
      
     
    /** 
     * Gets the total results based upon what is returned 
     * 
     * @xml = the simple xml object 
     **/ 
    private  function get_total_results($xml)   
    {   
        return $xml->RES->M; 
    } 
     
     
    /** 
     * Builds a simple array out of the xml result set 
     * 
     * @xml = the simple xml object 
     **/ 
    private function get_results_array($xml) 
    { 
        $r_ar = array(); 
        $i = 0; 
        foreach($xml->RES->R as $res) 
        { 
            $r_ar[$i]['link'] = $res->U; 
            $r_ar[$i]['title'] = $res->T; 
            $r_ar[$i]['description'] = $res->S; 
             
            $i++; 
        } 
        return $r_ar;     
    } 
     
    /** 
     * gets the "viewing results x - xx text" 
     * 
     * */ 
    function get_result_text($start, $page_count) 
    { 
        return "Viewing results ".($start+1)." to ".($start+$page_count); 
    } 
     
    /** 
     * returns the html links for paging 
     * 
     **/ 
    private function get_paging_links($start, $term, $page_count) 
    { 
        $prev= ""; 
        $next = ""; 
        $curr_page = "&nbsp;Page&nbsp;1&nbsp;"; 
         
         
        //get the current page 
        if($start > 0) 
        { 
            $curr_page = "Page&nbsp;".(1 + ($start / $this->rpp)); 
        } 
         
         
        if($start > 0) 
        { 
            $prev = "<a href='".$this->search_url."?term=".$term."&start=".($start-$this->rpp)."'>&lsaquo;&nbsp;Previous</a>&nbsp;"; 
        } 
         
        if($page_count >= $this->rpp) 
        { 
            $next = "&nbsp;<a href='".$this->search_url."?term=".$term."&start=".($start+$this->rpp)."'>Next&nbsp;&rsaquo;</a>"; 
         
        } 
         
        if($next != "" && $prev != "") 
        { 
            $split = ""; 
        } 
         
        return $prev."<strong>".$curr_page."</strong>".$next;     
    } 
     
    /** 
     * run_search = call this from the controller 
     * @term- the term to search for 
     * @start - which result to start at (for paging) 
     * @debug - setting to true will output the xml to the screen 
     * */ 
    public function run_search($term, $start=0, $debug=false) 
    { 
        $return = array(); 
        $end = $start+$this->rpp; 
        $search_uri =sprintf("https://www.googleapis.com/customsearch/v1?key=%s&cx=%s&q=%s&alt=json",urlencode($this->token),'000699741646373429586:zoncff__ucw',urlencode($term));
        //echo $search_uri = "https://www.googleapis.com/customsearch/v1?key=".urlencode($this->token)."&start=".$start."&num=".$this->rpp."&output=".$this->result_format."&q=".urlencode($term);
        //$search_uri = "http://www.google.com/cse?cx=".urlencode($this->token)."&client=google-csbe&start=".$start."&num=".$this->rpp."&output=".$this->result_format."&q=".urlencode($term); 
        $results = $this->get_result_xml($search_uri, $debug); 
        $return['spelling'] = $this->get_spelling_suggestion($results); 
        $return['total'] = $this->get_total_results($results); 
        $return['results'] = $this->get_results_array($results); 
        $return['paging'] = $this->get_paging_links($start, $term, sizeof($return['results'])); 
        $return['result_text'] = $this->get_result_text($start, sizeof($return['results'])); 
        return $return; 
    }  
}