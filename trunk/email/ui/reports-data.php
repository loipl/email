<?php 
class Data
{
	private $end_point = 'http://api.matchquota.com/email/api/report/7CmCznYgpQgpOrV5PKf3RSbM98UTlZ/';
	private $fetch_url;
	private $metric;
	private $type;
	private $interval;
	private $intervalSet;
	private $time;
	private $start;
	private $end;
	private $response;
    private $buildTimeCols = array();
	public function __construct($data)
	{
		$this->metric   = (isset($data['metric'])) ? $data['metric'] : 'sender';
		$this->type     = (isset($data['type'])) ? $data['type'] : 'open';
		$this->interval = (isset($data['interval'])) ? $data['interval'] : 'day';
		$this->time     = $data['time'];
		$this->setTimeFrame();
        $this->fetch_url = $this->end_point.$this->metric.'/'.$this->type.'&start='.$this->start.'&end='.$this->end
		.'&interval='.$this->interval;
		$this->buildTimeCols();
	}
    private function buildTimeCols()
	{
		switch ( $this->interval )
		{
	        case 'year':
			    $this->buildTimeCols['Year'] = 'year';
		    break;
	        case 'month':
			    $this->buildTimeCols['Year'] = 'year';
				$this->buildTimeCols['Month'] = 'month';
		    break;
	        case 'day':
			    $this->buildTimeCols['Year'] = 'year';
				$this->buildTimeCols['Month'] = 'month';
				$this->buildTimeCols['Day'] = 'day';
		    break;	
	        case 'hour':
			    $this->buildTimeCols['Year'] = 'year';
				$this->buildTimeCols['Month'] = 'month';
				$this->buildTimeCols['Day'] = 'day';
				$this->buildTimeCols['Hour'] = 'hour';
		    break;	
	        case 'minute':
			    $this->buildTimeCols['Year'] = 'year';
				$this->buildTimeCols['Month'] = 'month';
				$this->buildTimeCols['Day'] = 'day';
				$this->buildTimeCols['Hour'] = 'hour';
				$this->buildTimeCols['Minute'] = 'minute';
		    break;
		}
	}
	
    public function getResponse()
	{
	    $curl = curl_init();
		curl_setopt($curl,CURLOPT_URL,$this->fetch_url);  
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true); 
        $this->response = json_decode(curl_exec($curl));
	}
    private function buildTableCols()
	{
	    $table_data = array();
		$table_data[] = array('string',ucfirst($this->metric));
		foreach($this->buildTimeCols as $key=>$val)
		{
		    if($key=='Month')
			{
			    $table_data[] = array('string',$key);
			}
			else
			{
		        $table_data[] = array('number',$key);
		    }
		}
		$table_data[] = array('string',ucfirst($this->type));
		$table_data[] = array('boolean','Currently Enabled');
		return $table_data;
    }
	
    private function buildTableRows($val)
	{
	    $table_row[] = $val->metric_id;
		foreach($this->buildTimeCols as $key=>$value)
		{
		    $table_row[] = (($value=='month') ? date("M" , strtotime("2012-$val->month-01")) : intval($val->$value));
		}
		$table_row[] = $val->{'SUM(value)'};
		$table_row[] = true;
		return $table_row;
    }
	
	public function buildChartData()
	{
		$types = array();
		$types['open']        = 'Total opens';
		$types['total']       = 'Total all';
		$types['click']       = 'Total clicks';
		$types['unsubscribe'] = 'Total unsubscribes';
		$types['softbounce']  = 'Total softbounces';
		$types['complaint']   = 'Total complaints';
		$types['hardbounce']  = 'Total hardbounces';
		
		$interval_types = array();
		$interval_types['year']   = 'Yearly';
		$interval_types['month']  = 'Monthly';
		$interval_types['day']    = 'Daily';
		$interval_types['hour']   = 'Hourly';
		$interval_types['minute'] = 'By minute';
		
		
		
		$table_data = array();
		$table_data['cols'] = $this->buildTableCols();

		$t = 1;
		$line   = array();
		$x_axis = array();
		$LINE   = array();
		foreach($this->response as $key=>$val)
		{
			if($val->metric_id!='')
			{
				$x_axis[$val->year.$val->month.$val->day.$val->hour.$val->minute] = $this->getXaxis(array('year'=>$val->year,
				'month'=>$val->month,
				'day'=>$val->day,'hour'=>$val->hour,'minute'=>$val->minute));
				
				$main[$val->year.$val->month.$val->day.$val->hour.$val->minute] = $val->year.$val->month.$val->day.$val->minute;
				$lineNames[$val->metric_id] = $val->metric_id;
				
				$line[$val->year.$val->month.$val->day.$val->hour.$val->minute][$val->metric_id] = intval($val->{'SUM(value)'});
				
				$table_data['rows'][] = $this->buildTableRows($val);
			}
			$t++;
		}
		
		
		$i=1;
		$LINE[0][0] = 'Time';
		$TREND[0][] = 'Score';
		foreach($lineNames as $key=>$val)
		{
			$LINE[0][]  = $val;
			$TREND[0][] = $val;
		}
		
		$kk=1;
		foreach($main as $key=>$val)
		{
			$LINE[$i][]   = "".$x_axis[$key]."";
			$j=0;
			$TREND[$kk][] = $kk;
			foreach($lineNames as $k=>$v)
			{
				$value        = (isset($line[$key][$v])) ? $line[$key][$v] : 0;
				//$LINE[$i][]   = '';
				$LINE[$i][]   = $value;
				$LINE[$i][]   = "<div class='tooltip'> <br>".$this->type."s by ".$v." : ".$value." <br>".$x_axis[$key]." <br></div>";
				$TREND[$kk][] = $value;
				$TREND[$kk][] = "<div class='tooltip'> <br>".$this->type."s by ".$v." : ".$value." <br>".$x_axis[$key]." <br></div>";
				$j++;
			}
			$i++;
			$kk++;
		}
		if(count($LINE)>1)
		{
		    $this->intervalSet = (isset($this->intervalSet) ? $this->intervalSet : 'minute');
			$chart_title = $types[$this->type]." by ".$this->metric." (".$interval_types[$this->interval].")";
			$this->output = array('tableData'=>$table_data,'lineData'=>$LINE,'trendData'=>$TREND,'chart_title' => $chart_title , 
			'erroe'=>0, 'interval'=>$this->interval , 'intervalSet'=>$this->intervalSet);
		}
		else
		{
			$this->output = array('error'=>1 , 'msg'=>'No records found' );
		}
		return json_encode($this->output);

	}


	private function setTimeFrame()
	{
		if($this->time=='')
		{
			$this->start = '1980-01-01';
			$this->end = date("Y-m-d");
			return;
		}
		switch ( $this->time )
		{
		
			case 'today':
				$start = date("Y-m-d");
				$end = date("Y-m-d");
				$this->interval = (($this->interval=='minute')) ? 'minute' : 'hour';
				$this->intervalSet = array('hour' , 'minute');
				break;
	
			case 'yesterday':
				$start = date("Y-m-d", strtotime("yesterday"));
				$end = date("Y-m-d", strtotime("yesterday"));
				$this->interval = (($this->interval=='minute')) ? 'minute' : 'hour';
				$this->intervalSet = array('hour' , 'minute');
				break;
	
			case 'thisweek':
				$start = date("Y-m-d", strtotime("last monday"));
				$end = date("Y-m-d");
				$this->interval = (($this->interval=='hour')) ? 'hour' : 'day';
				$this->intervalSet = array('day' , 'hour' , 'minute');
				break;
				
			case 'last7':
				$start = date("Y-m-d", strtotime("-6 days"));
				$end = date("Y-m-d");
				$this->interval = (($this->interval=='hour')) ? 'hour' : 'day';
				$this->intervalSet = array('day' , 'hour');
				break;			
				
			case 'lastw':
				$start = date("Y-m-d", strtotime("last monday -7 days"));
				$end = date("Y-m-d", strtotime("last monday"));
				$this->interval = (($this->interval=='hour')) ? 'hour' : 'day';
				$this->intervalSet = array('day' , 'hour');
				break;
				
			case 'lastbw':
				$start = date("Y-m-d", strtotime("last friday -4 days"));
				$end = date("Y-m-d", strtotime("last friday"));
				$this->interval = (($this->interval=='hour')) ? 'hour' : 'day';
				$this->intervalSet = array('day' , 'hour');
				break;
	
			case 'last14':
				$start = date("Y-m-d", strtotime("-13 days"));
				$end = date("Y-m-d");
				$this->interval = (($this->interval=='hour')) ? 'hour' : 'day';
				$this->intervalSet = array('day' , 'hour');
				break;
				
			case 'thismonth':
				$start = date("Y-m").'-01';
				$end = date("Y-m-d");
				$this->interval = (($this->interval=='hour')) ? 'hour' : 'day';
				$this->intervalSet = array('day' , 'hour');
				break;
				
			case 'last30':
				$start = date("Y-m-d", strtotime("-29 days"));
				$end = date("Y-m-d");
				$this->interval = (($this->interval=='hour')) ? 'hour' : 'day';
				$this->intervalSet = array('day' , 'hour');
				break;
	
			case 'lastm':
				$TODAY = date("d");
				if($TODAY>20)
				{
					$time_stamp = time()-(3600*24*30);
				}
				else
				{
					$time_stamp = time()-(3600*24*20);
				}
				$start = date("Y-m" , $time_stamp).'-01';
				$end = date("Y-m" , $time_stamp).'-30';
				$this->interval = (($this->interval=='hour')) ? 'hour' : 'day';
				$this->intervalSet = array('day' , 'hour');
				break;
			case 'all':
				$start = '1980-01-01';
				$end = date("Y-m-d");
				
				$this->interval = (($this->interval=='month')) ? 'month' : 'year';
				$this->intervalSet = array('year' , 'month');
				break;		
			default:
				$frames_array = explode(' - ',$this->time);
				$start = (isset($frames_array[0])) ? $frames_array[0] : '1960-01-01';
				$end = (isset($frames_array[1])) ? $frames_array[1] : date("Y-m-d");
				$start_stamp = strtotime($frames_array[0]);
				$end_stamp = strtotime($frames_array[1]);
				$difference = $end_stamp - $start_stamp;
				
				if($difference<(3600*24))
				{
					$this->interval = (($this->interval=='minute')) ? 'minute' : 'hour';
					$this->intervalSet = array('hour' , 'minute');
				}
				elseif($difference<(3600*24*5))
				{
					$this->interval = (($this->interval=='day')) ? 'day' : 'hour';
					$this->intervalSet = array('day' , 'hour' , 'minute');
				}
				elseif($difference<(3600*24*30))
				{
					$this->interval = 'day';
					$this->intervalSet = array('day' , 'hour');
				}
				elseif($difference<(3600*24*30*2))
				{
					$this->interval = (($this->interval=='month')) ? 'month' : 'day';
					$this->intervalSet = array('month' , 'day');
				}
				elseif($difference<(3600*24*30*6))
				{
					$this->interval = 'month';
					$this->intervalSet = array('month' , 'day');
				}
				elseif($difference<(3600*24*30*24))
				{
					$this->interval = (($this->interval=='month')) ? 'month' : 'year';
					$this->intervalSet = array('year' , 'month');
				}
				elseif($difference>(3600*24*30*24))
				{
					$this->interval = (($this->interval=='month')) ? 'month' : 'year';
					$this->intervalSet = array('year' , 'month');
				}
				break;			
				
		}
		$this->start = $start;
		$this->end = $end;
	}
	private function getXaxis( $date )
	{
	
		switch($this->interval)
		{
			case 'year':
				$return = $date['year']; 
				break;
			case 'month':
				$month = $date['month'];
				$return = date('M', strtotime("2012-$month-01"))." ".$date['year'];
				break;
			case 'day':
				$month = $date['month'];
				$return = date('M', strtotime("2012-$month-01"))." ".$date['day'].",".$date['year'];
				break;
			case 'hour':
				$month = $date['month'];
				$return = date('M', strtotime("2012-$month-01")).", ".$date['day']." ".$date['year']." ".$date['hour'].":00";
				break;
			case 'minute':
				$month = $date['year'];
				$return = date('M', strtotime("2012-$month-01")).", ".$date['day']." ".$date['year']." ".$date['hour'].":".$date['minute'];
				break;
		}
		return $return;
	}

}