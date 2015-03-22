<?php
class UtilityController extends AppController {
    
    public $components = array('RequestHandler','Common');
    public function beforefilter(){
        $this->set('title','Utilities');
        $this->set('subtitle','Control panel');
    }
    
    public function whois() {
        
          
    }
    
    public function whoisapi($type){
        $data = array();
        switch($type){
            case 'general' :
                if($this->request->data){
                    $domain = $this->request->data['search']['domain'];
                    $url = parse_url($domain);
                    if(isset($url['host'])){
                        $ip = gethostbyname ($url['host']);
                        if($ip){
                            $command = sprintf('whois -h whois.cymru.com " -v %s"',$ip);
                            $tmp = `$command`;
                            if($tmp != ''){
                                preg_match_all('/(.*)[\n]/i',$tmp,$data);
                                echo '<pre>';print_r($data[1]);echo '</pre>';
                            }
                        }
                    }else{
                        echo 'Invalid URL';
                    }
                }
                break;
        }
        exit;
    }
}

/*
if (!filter_var($ip, FILTER_VALIDATE_IP) === false) {
                        $command = sprintf('curl -H "Accept: application/json" http://whois.arin.net/rest/ip/%s',$ip);
                        //$command = sprintf('curl -H "Accept: application/json" http://whois.arin.net/rest/org/AT-88-Z');
                        //$command = sprintf('curl -H "Accept: application/json" http://whois.arin.net/rest/org/AT-88-Z/nets');
                        //$command = sprintf('curl -H "Accept: application/json" http://whois.arin.net/rest/org/AT-88-Z/asns');
                        //$command = sprintf('curl -H "Accept: application/json" http://whois.arin.net/rest/asn/AS36039');
                        $command = sprintf('curl -H "Accept: application/json" http://whois.arin.net/rest/org/AT-88-Z/pocs');
                        
                        //$command = sprintf('curl -H "Accept: application/json" http://whois.arin.net/rest/poc/GOGL/orgs');
                        
                        $response = `$command`;
                        $tmp = json_decode($response,true);
                        
                        echo '<pre>';print_r($tmp);echo '</pre>';
                        
                        if(isset($tmp['net'])){
                            //echo '<pre>';print_r($tmp['net']);echo '</pre>';
                            $data['registrationDate'] = isset($tmp['net']['registrationDate']['$']) ? date('d M, Y h:i:s A',strtotime($tmp['net']['registrationDate']['$'])) : '--';
                            $data['updateDate'] = isset($tmp['net']['updateDate']['$']) ? date('d M, Y h:i:s A',strtotime($tmp['net']['updateDate']['$'])) : '--';
                            $data['NetName'] = isset($tmp['net']['name']['$']) ? $tmp['net']['name']['$'] : '--';
                            $data['handle'] = isset($tmp['net']['handle']['$']) ? $tmp['net']['handle']['$'] : '--';
                            $data['ref'] = isset($tmp['net']['ref']['$']) ? $tmp['net']['ref']['$'] : '--';
                        }
                                    <tr><td>CIDR</td><td><?php echo isset($data->net->netBlocks->netBlock->startAddress->{'$'}) ? $data->net->netBlocks->netBlock->startAddress->{'$'} : '--'; ?>/<?php echo isset($data->net->netBlocks->netBlock->cidrLength->{'$'}) ? $data->net->netBlocks->netBlock->cidrLength->{'$'} : '--'; ?></td></tr>
                                    <tr><td>NetName</td><td><?php echo isset($data->net->name->{'$'}) ? $data->net->name->{'$'} : '--'; ?></td></tr>
                                    <tr><td>NetHandle</td><td><?php echo isset($data->net->handle->{'$'}) ? $data->net->handle->{'$'} : '--'; ?></td></tr>
                                    <tr><td>Parent</td><td><?php echo isset($data->net->parentNetRef->{'@name'}) ? $data->net->parentNetRef->{'@name'} : '--'; ?> (<?php echo isset($data->net->parentNetRef->{'@handle'}) ? $data->net->parentNetRef->{'@handle'} : '--'; ?>)</td></tr>
                                    <tr><td>Description</td><td><?php echo isset($data->net->netBlocks->netBlock->description->{'$'}) ? $data->net->netBlocks->netBlock->description->{'$'} : '--'; ?></td></tr>
                                    <tr><td>NetType</td><td><?php echo isset($data->net->netBlocks->netBlock->type->{'$'}) ? $data->net->netBlocks->netBlock->type->{'$'} : '--'; ?></td></tr>
                                    <tr><td>Version</td><td><?php echo isset($data->net->version->{'$'}) ? $data->net->version->{'$'} : '--'; ?></td></tr>
                                    <tr><td>Organization</td><td><?php echo isset($data->net->orgRef->{'@name'}) ? $data->net->orgRef->{'@name'} : '--'; ?> (<?php echo isset($data->net->orgRef->{'@handle'}) ? $data->net->orgRef->{'@handle'} : '--'; ?>)</td></tr>
                        
                    }
*/