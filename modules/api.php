<?php
class GoalBackup {
    private $section; //reddit sub we intent to scrape
    private $limit; //limit of threads we need
    private $pdo; //database connection
    private $re = '/\d\'/'; //regex to filter matches
    private $domains = ["streamja.com", "streamable.com", "streamvi.com", "clippituser.tv", "streamye.com", "streamwo.com", "streamgg.com"]; //list of acceptable domains
    private $domain; //the domain we are processing
    private $content; //source code of the video page
    
    /*
    Error Codes:
    GB101 - Already existing on our database.
    GB102 - Team name is not relevant and won't give us enough traffic.
    GB103 - Probably not a goal thread, maybe a random video.
    GB104 - Invalid or Unknown video website
    GB105 - Our video servers are down
    */
    
    public function __construct($section = "soccer", $limit = 10, $dbhost, $dbname, $dbuser, $dbpass){
        $this->section = $section;
        $this->limit = $limit;
        try{
            $this->pdo = new PDO("mysql:host={$dbhost};dbname={$dbname}", $dbuser, $dbpass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $exception){
            die("Connection Error: ".$exception->getMessage());
        }
    }
    
    public function cURL($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        return curl_exec($ch);
    }

	public function scrape($single = false, $singleURL = ""){
	    ini_set('max_execution_time', 300);
	    if($single){
	        $data = json_decode($this->cURL($singleURL), true);
	        $data = $data[0];
	    }else{
	        $base_url = "https://www.reddit.com/r/".$this->section."/new.json?limit=".$this->limit."&sort=new";
	        $data = json_decode($this->cURL($base_url), true);
	    }
		$entries = array();
		$hash = "";
		foreach($data['data']['children'] as $child){
		    if($child['data']['link_flair_text'] == "Media"){
		        $redditHash = $child['data']['id'];
		        $statement = $this->pdo->prepare("SELECT * FROM goals WHERE reddit_id=?");
		        $statement->execute(array($redditHash));
		        $total = $statement->rowCount();
		        if($total > 0){
		            $hash = "GB101";
		            continue;
		        }
		        $title = $this->cleanText($child['data']['title']);
		        if(!$this->filterTeam($title)){
		            $hash = "GB102";
		            continue;
		        }
		        if(!preg_match($this->re, $title)){
		            $hash = "GB103";
		            continue;
                }
                $this->getPage($child['data']['url']);
                $this->domain = $child['data']['domain'];
		        $link = $this->getVideo();
		        if(empty($link)){
		            $hash = "GB104";
		            continue;
		        }
		        $hash = $this->generateHash($title);
		        if($this->domain == "streamye.com"){
		            continue;
		            //$video = $link;
		        }else{
		            $video = $this->uploadVideo($hash, $link);
		        }
		        if($video == ""){
		            $hash = "GB105";
		            continue;
		        }
		        $thumbnail = "";
		        //$thumbnail = $this->getThumbnail();
		        if(!empty($thumbnail)){
		            //$thumbnail = $this->uploadImage($thumbnail);
		        }
		        $great = 0;
		        if(strpos(strtolower($title), "great") !== false || strpos(strtolower($title), "nice") !== false){$great = 1;}
		        array_push($entries, array("title" => $title, "hash" => $hash, "great" => $great, "thumbnail" => $thumbnail, "video" => $video, "reddit_id" => $redditHash));
		    }
		}
		$this->saveToDB(array_reverse($entries));
		return $hash;
	}
	
	private function generateHash($string, $len=8){
        $hex = md5($string."NodeTent");
        $pack = pack('H*', $hex);
        $uid = base64_encode($pack);
        $uid = preg_replace('/[^a-zA-Z 0-9]+/', "", $uid);
        if($len<4){
            $len=4;
        }elseif($len>128){
            $len=128;
        }
        return substr($uid, 0, $len);
    }
	
	private function saveToDB($entries){
        $statement = $this->pdo->prepare("INSERT IGNORE INTO goals (gr_title, gr_time, gr_hash, is_great, thumbnail, video, reddit_id) VALUES (?,?,?,?,?,?,?)");
        foreach($entries as $goal) {
            $statement->execute(array($goal["title"], time(), $goal["hash"], $goal["great"], $goal["thumbnail"], $goal["video"], $goal["reddit_id"]));
        }
    }
    
    private function getVideo(){
        $url = "";
        switch($this->domain){
            case "streamja.com":
            case "streamable.com":
            case "streamwo.com":
            case "streamgg.com":
                $dom = new DOMDocument();
                @$dom->loadHTML($this->content);
                $xpath = new DOMXPath($dom);
                if($this->domain == "streamable.com"){
                    $hrefs = $xpath->evaluate("/html/body//video");
                }elseif($this->domain == "streamja.com"){
                    $hrefs = $xpath->evaluate("/html/body//video//source");
                }else{
                    $hrefs = $xpath->evaluate("/html/body//video//source");
                }
                for($i=0; $i<$hrefs->length; $i++){
                    $href = $hrefs->item($i);
                    $url = $href->getAttribute('src');
                    $url = filter_var($url, FILTER_SANITIZE_URL);
                    if(strpos($url, 'https://') === false){
                        $url = "https:".$url;
                    }
                }
                break;
            case "streamvi.com":
                preg_match('/<source(.*?)src="(.*?).mp4(.*?)"/i', $this->content, $mp4);
                $url = "{$mp4[2]}.mp4{$mp4[3]}";
                break;
            case "clippituser.tv":
                preg_match('/<div(.*?)data-hd-file="(.*?).mp4(.*?)"/i', $this->content, $mp4);
                $url = "{$mp4[2]}.mp4{$mp4[3]}";
                break;
            case "streamye.com":
                $data = json_decode($this->content, true);
                $url = $data['videoLink'];
                $url = "https://videodelivery.net/$url/manifest/video.m3u8";
                break;
            default:
                $url = null;
	    }
        return $url;
    }
    
    private function getThumbnail(){
        $dom = new DOMDocument();
        @$dom->loadHTML($this->content);
        $xpath = new DOMXPath($dom);
        $hrefs = $xpath->evaluate("/html/body//video");
        for($i=0; $i<$hrefs->length; $i++){
            $href = $hrefs->item($i);
            $url = $href->getAttribute('poster');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            if(strpos($url, 'https://') === false && $url != ""){
                $url = "https:".$url;
            }
            return $url;
        }
    }
    
    private function uploadVideo($hash, $link){
        //use imgur now for all except > 60sec, use s3 otherwise
        require_once "catbox.php";
        $mp4 = video2Catbox($link);
        if($mp4){
            return $mp4;
        }else{
            return "";
            //require_once "s3/setup.php";
            //return uploadToS3($hash, $link);
        }
        
    }
    
    private function uploadImage($image, $client_id="06059d21ae79933"){
        $file = file_get_contents($image);
        $url = 'https://api.imgur.com/3/image.json';
        $headers = array("Authorization: Client-ID $client_id");
        $pvars  = array('image' => base64_encode($file));
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL=> $url,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_POST => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $pvars
        ));
        $result = curl_exec($curl);
        curl_close($curl);
        $pms = json_decode($result, true);
        if($pms['success'] == true && $pms['status'] == 200){
            return $pms['data']['link'];
        }
        return "";
    }
    
    private function getPage($url){
        $ch = curl_init();
        if($this->getDomain($url) == "streamye.com"){
            $id = explode("/v/", $url);
            $url = "https://streamye.com/api/videos/".$id[1];
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $this->content = curl_exec($ch);
    }
    
    private function filterTeam($match){
        $acceptedTeams = ['Tottenham', 'Arsenal', 'Burnley', 'Crystal Palace', 'Wolves', 'Wolvehampton', 'Fulham', 'River Plate', 'Chelsea', 'Liverpool',
        'Southampton', 'Barcelona', 'Boca Junior', 'Bournemouth', 'Juventus', 'Bayern', 'Munich', 'Manchester City', 'Manchester United', 'Manchester',
        'Man Utd', 'Man City', 'Milan', 'Everton', 'Real Madrid', 'Madrid', 'Barca', 'Atletico Madrid', 'Dortmund', 'Leicester', 'Newcastle', 'Inter',
        'West Brom', 'Sheffield', 'Aston Villa', 'PSG', 'West Ham', 'Leeds', 'Napoli'];
        $choppedString = explode(" ", $match);
        if(empty(array_intersect(array_map("strtolower", $acceptedTeams), array_map("strtolower", $choppedString)))){
            return false;
        }
        return true;
    }
    
    function getDomain($url){
        $pieces = parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : '';
        if(preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)){
            return $regs['domain'];
        }
        return false;
    }
	
	private function cleanText($orig_text){
        $text = $orig_text;
        $replace = [
            '’' => '\'', '&lt;' => '', '&gt;' => '', '&#039;' => '', '&amp;' => '',
            '&quot;' => '', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'Ae',
            '&Auml;' => 'A', 'Å' => 'A', 'Ā' => 'A', 'Ą' => 'A', 'Ă' => 'A', 'Æ' => 'Ae',
            'Ç' => 'C', 'Ć' => 'C', 'Č' => 'C', 'Ĉ' => 'C', 'Ċ' => 'C', 'Ď' => 'D', 'Đ' => 'D',
            'Ð' => 'D', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ē' => 'E',
            'Ę' => 'E', 'Ě' => 'E', 'Ĕ' => 'E', 'Ė' => 'E', 'Ĝ' => 'G', 'Ğ' => 'G',
            'Ġ' => 'G', 'Ģ' => 'G', 'Ĥ' => 'H', 'Ħ' => 'H', 'Ì' => 'I', 'Í' => 'I',
            'Î' => 'I', 'Ï' => 'I', 'Ī' => 'I', 'Ĩ' => 'I', 'Ĭ' => 'I', 'Į' => 'I',
            'İ' => 'I', 'Ĳ' => 'IJ', 'Ĵ' => 'J', 'Ķ' => 'K', 'Ł' => 'K', 'Ľ' => 'K',
            'Ĺ' => 'K', 'Ļ' => 'K', 'Ŀ' => 'K', 'Ñ' => 'N', 'Ń' => 'N', 'Ň' => 'N',
            'Ņ' => 'N', 'Ŋ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
            'Ö' => 'Oe', '&Ouml;' => 'Oe', 'Ø' => 'O', 'Ō' => 'O', 'Ő' => 'O', 'Ŏ' => 'O',
            'Œ' => 'OE', 'Ŕ' => 'R', 'Ř' => 'R', 'Ŗ' => 'R', 'Ś' => 'S', 'Š' => 'S',
            'Ş' => 'S', 'Ŝ' => 'S', 'Ș' => 'S', 'Ť' => 'T', 'Ţ' => 'T', 'Ŧ' => 'T',
            'Ț' => 'T', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'Ue', 'Ū' => 'U',
            '&Uuml;' => 'Ue', 'Ů' => 'U', 'Ű' => 'U', 'Ŭ' => 'U', 'Ũ' => 'U', 'Ų' => 'U',
            'Ŵ' => 'W', 'Ý' => 'Y', 'Ŷ' => 'Y', 'Ÿ' => 'Y', 'Ź' => 'Z', 'Ž' => 'Z',
            'Ż' => 'Z', 'Þ' => 'T', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a',
            'ä' => 'ae', '&auml;' => 'ae', 'å' => 'a', 'ā' => 'a', 'ą' => 'a', 'ă' => 'a',
            'æ' => 'ae', 'ç' => 'c', 'ć' => 'c', 'č' => 'c', 'ĉ' => 'c', 'ċ' => 'c',
            'ď' => 'd', 'đ' => 'd', 'ð' => 'd', 'è' => 'e', 'é' => 'e', 'ê' => 'e',
            'ë' => 'e', 'ē' => 'e', 'ę' => 'e', 'ě' => 'e', 'ĕ' => 'e', 'ė' => 'e',
            'ƒ' => 'f', 'ĝ' => 'g', 'ğ' => 'g', 'ġ' => 'g', 'ģ' => 'g', 'ĥ' => 'h',
            'ħ' => 'h', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ī' => 'i',
            'ĩ' => 'i', 'ĭ' => 'i', 'į' => 'i', 'ı' => 'i', 'ĳ' => 'ij', 'ĵ' => 'j',
            'ķ' => 'k', 'ĸ' => 'k', 'ł' => 'l', 'ľ' => 'l', 'ĺ' => 'l', 'ļ' => 'l',
            'ŀ' => 'l', 'ñ' => 'n', 'ń' => 'n', 'ň' => 'n', 'ņ' => 'n', 'ŉ' => 'n',
            'ŋ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'oe',
            '&ouml;' => 'oe', 'ø' => 'o', 'ō' => 'o', 'ő' => 'o', 'ŏ' => 'o', 'œ' => 'oe',
            'ŕ' => 'r', 'ř' => 'r', 'ŗ' => 'r', 'š' => 's', 'ù' => 'u', 'ú' => 'u',
            'û' => 'u', 'ü' => 'ue', 'ū' => 'u', '&uuml;' => 'ue', 'ů' => 'u', 'ű' => 'u',
            'ŭ' => 'u', 'ũ' => 'u', 'ų' => 'u', 'ŵ' => 'w', 'ý' => 'y', 'ÿ' => 'y',
            'ŷ' => 'y', 'ž' => 'z', 'ż' => 'z', 'ź' => 'z', 'þ' => 't', 'ß' => 'ss',
            'ſ' => 'ss', 'ый' => 'iy', 'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G',
            'Д' => 'D', 'Е' => 'E', 'Ё' => 'YO', 'Ж' => 'ZH', 'З' => 'Z', 'И' => 'I',
            'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
            'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F',
            'Х' => 'H', 'Ц' => 'C', 'Ч' => 'CH', 'Ш' => 'SH', 'Щ' => 'SCH', 'Ъ' => '',
            'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'YU', 'Я' => 'YA', 'а' => 'a',
            'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo',
            'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l',
            'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's',
            'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e',
            'ю' => 'yu', 'я' => 'ya'
        ];
        $text = str_replace(array_keys($replace), $replace, $text);
        return preg_replace('/[^\00-\255]+/u', '', $text);
    }
}
?>