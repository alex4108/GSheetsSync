<?php
ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
/***********************************************************
CONFIG SECTION -- SET THESE VARIABLES CAREFULLY!
************************************************************/
$version = 1.1;
$firstName = "Alex";
$lastName = "Schittko";
$calendarName = "Shifts (Develop)";
$leftBound = "BN";
$rightBound = "CA";

/*************************************************************
STOP CONFIGURING HERE!
************************************************************/
/****
TODO

* Add a switch to support emptying the calendar, in case we write too many times
* Handle Google OAuth in a WebForm
* WebForm to handle configuration options


require_once __DIR__ . '/vendor/autoload.php';
require_once 'functions.php';

define('CREDENTIALS_PATH', 'C:/Users/aschittko/Documents/granburyScheduleSync/credentials/sheets.googleapis.com-calendarSync.json');
define('CREDENTIALS_PATH2', 'C:/Users/aschittko/Documents/granburyScheduleSync/credentials/sheets.googleapis.com-calendarSync-2.json');
define('CLIENT_SECRET_PATH', 'C:/Users/aschittko/Documents/granburyScheduleSync/credentials/client_secret_976487652194-cc9ht4e0g1g095htrbij0o4civ6sa7bo.apps.googleusercontent.com.json');
$client = getClient();
$calendarService = new Google_Service_Calendar($client);

class SHEET_FUNCTIONS {
	
	function __construct( $left, $right, $firstName, $lastName ) {
			$this->leftBoundary = $left;
			$this->rightBoundary = $right;
			$this->client = getClient();
			$this->realName = $firstName . " " . $lastName;
			$this->s = new Google_Service_Sheets($this->client);
	}

	function getSpreadsheet( $range ) {
		$spreadsheetId = '1eZ3bLDeAZKIBT9yJj5pwDxRTynQsM8YPEWrgNgnvVR8';
	
		// The A1 notation of the values to retrieve.
		
		$response = $this->s->spreadsheets_values->get( $spreadsheetId, $range );
		//print_r($response->values[0]);
		return $response->getValues();
		/*
		// TODO: Change code below to process the `response` object:
		echo '<pre>$RESPONSE', var_export( $response, true ), '</pre>', "\n";

		echo '<pre>$response->getValues', var_export( $response->getValues(), true ), '</pre>', "\n";
		*/
	
	}



	function getUserRow(  ) {
		$range = "Master!A1:A71";
		$sheet = $this->getSpreadsheet( $range );
		//print_r($sheet);
		foreach($sheet as $upperKey => $employee) {
			foreach($employee as $key => $value) {	
				if ( $employee[0] == $this->realName ) {
					//print_r($employee);
					$this->userRow = $upperKey+1; // Return array position
					return $upperKey+1; // Return array position
				}
			}
		}
		return 'FAIL';
	}


	function getDates(  ) {
		$range = "Master!" . $this->leftBoundary . "1:" . $this->rightBoundary . "2";
		
		// The A1 notation of the values to retrieve.
		$data = $this->getSpreadsheet( $range );
		return $data;
		
	}
	
	
		/*
			array( // Whole schedule
				array( // Each day's schedule
					'id' => int,
					'date' => date YYYY-MM-DD,
					'dayType' => string, possible: 0 - WORK, 1- PTO (Lime Green), 2- OFF (Gray), 3 - HOLIDAY (YELLOW), 4 - EMERGENCY SUPPORT (DARKBLUE)
					'scheduleIn' => time HH:MM,
					'seheduleOut' => time HH:MM,
					
				),
			)
		*/
	function getSchedule( $userRow ) {
		$dates = $this->getDates(  );
		
		$schedule = array();
	
		foreach( $dates as $keyUpper => $valueUpper ) { // Encode the Date on each entry
			foreach( $valueUpper as $key => $value ) {
				
				
				
				//print_r( preg_split( "/[\s-]+/", $value)[0] . "\n" );
				$month = preg_split( "/[\s-]+/", $value)[1];
				//$month = DateTime::createFromFormat( '!M', ;
				$day = preg_split( "/[\s-]+/", $value)[0];
				if ( strlen( $day ) == 1 ) {
				
					$day = "0" . $day;
					
				}
				//$day = DateTime::createFromFormat( '!D', $valueUpper[1] ); 
				$date = date("Y") .'-'. $month .'-'. $day;// Date in yyyy-mm-dd
				//rint_r($date);
				
				$date = date('Y-m-d', strtotime( $date ) );
				
				if ($date != "1970-01-01") {
					
					
					array_push( $schedule, 
					array( // Each day's schedule
						'date' => $date,
						'dateCode' => $dates[1][$key],
						/*'dayType' => string, possible: 0 - WORK, 1- PTO (Lime Green), 2- OFF (Gray), 3 - HOLIDAY (YELLOW), 4 - EMERGENCY SUPPORT (DARKBLUE)
						'scheduleIn' => time HH:MM,
						'seheduleOut' => time HH:MM,
						*/
					)
					);
				}
				
			}
			break;
		}
		
		//print_r('BOUNDARY' . $this->leftBoundary);
		//print_r('BLEFT '  . $this->leftBoundary . ' BRIGHT ' . $this->rightBoundary . ' ROW '  . $this->userRow);
		$scheduleRow = $this->getSpreadsheet( 'Master!' . $this->leftBoundary . $this->userRow . ':' . $this->rightBoundary . $this->userRow);
		
		//print_r($scheduleRow[0]);
		//die();
		
		
		foreach( $schedule as $k => $v ) { // Encode the time on each entry
			
			$scheduleIn = null;
			$scheduleOut = null;
			
			$textInCell = $scheduleRow[0][$k];
				//print_r($textInCell . "\n");
				// Special Cases
				switch( $textInCell ) {
					
					case $textInCell == "OFF" || $textInCell == "":
						$dayType = 2;
					break;
					
					case $textInCell == "D/O":
						$dayType = 2;
					break;
					
					case $textInCell == "PTO":
						$dayType = 1;
					break;
					
					case $textInCell == "HOLIDAY":
						$dayType = 3;
					break;
					
					case $textInCell == "EMERGENCY SUPPORT":
						$dayType = 4;
					break;

					default:
						
						switch ( substr( $textInCell, -1 ) ) {
							case "A":
								$time = $textInCell . "M";
								$dayType = 0;
							break;
							case "P":
								$time = $textInCell . "M";
								$dayType = 0;
							break;
						}
						$scheduleOut = explode( '-', $time )[1];
						$scheduleIn = explode( '-', $time )[0];
						
						$scheduleOutHalf = substr($scheduleOut, -2);
						$scheduleOutDigits = substr($scheduleOut, 0, -2);
						
						if ( $scheduleOutHalf == "PM" ) {
							$scheduleOut = intval( $scheduleOutDigits ) + 12;
							
						
						}
						
						else if ($scheduleOutHalf == "AM" ){
							// Schedule out is AM, so schedule In is PM
							$scheduleIn = intval( $scheduleIn ) + 12;
						}
						//print_r('IN: ' . $scheduleIn . ' OUT: ' . $scheduleOut . "\n");
						
						
					
						
				}		
				
				
				$schedule[$k]['dayType'] = $dayType;			
				$schedule[$k]['scheduleIn'] = $scheduleIn;
				$schedule[$k]['scheduleOut'] = $scheduleOut;
					
		}
		//print_r($schedule);
		return $schedule;
	}
}


class CALENDAR_FUNCTIONS {
	function __construct( $firstName, $eventsToSchedule, $calendarName ) {
			$this->client = getClient2();
			$this->firstName = $firstName;
			$this->s = new Google_Service_Calendar($this->client);
			$this->eventsToSchedule = $eventsToSchedule;
			$this->calendarName = $calendarName;
			$this->timezone_word = "America/Chicago";
			$this->timezone_numeric = "-06:00";
			// Set a calendar to write to
			foreach( $this->s->calendarList->listCalendarList()->getItems() as $k => $cal) {
				if ($cal->summary == $this->calendarName) {
					$this->calendar = $this->s->calendarList->listCalendarList()[$k];
					//print_r($this->calendar);
				}
			}
	}
	
	function sample() {
		
		$optParams = array(
		  'maxResults' => 10,
		  'orderBy' => 'startTime',
		  'singleEvents' => TRUE,
		  'timeMin' => date('c'),
		);
		$results = $this->s->events->listEvents($this->calendar->id, $optParams);

		if (count($results->getItems()) == 0) {
		  print "No upcoming events found.\n";
		} else {
		  print "Upcoming events:\n";
		  foreach ($results->getItems() as $event) {
			$start = $event->start->dateTime;
			if (empty($start)) {
			  $start = $event->start->date;
			}
			//var_dump( $event );
		  }
		}

	
	}
	
	function checkIfCalendarEventExists() {
		
		
		return false;
	}	
	
	function parseSchedule ( $schedule ){
		
		
		$optParams = array(
		  'maxResults' => 10,
		  'orderBy' => 'startTime',
		  'singleEvents' => TRUE,
		  'timeMin' => date('c'),
		);
		// Get all events for comparison
		$this->calendarEvents = $this->s->events->listEvents($this->calendar->id, $optParams);
				
		/*
		if ( count($this->calendarEvents->getItems()) == 0 ) {
			print "No events on Calendar. \n";
		}
		*/
		
		// Parse events on schedul
		foreach( $schedule as $k => $v ) {
			if (in_array($v['dayType'], $this->eventsToSchedule)) { 
				// Get the Time Strings to put in the API call
					
					$formatted_timeString = "YYYY-MM-DDTHH:MM:00";
					$hour = substr( $v['scheduleIn'], 0, 2 );
					
					$timeString_in = $v['date'] . "T" . substr( $v['scheduleIn'], 0, 2 ) . ":00:00";
					//$v['scheduleOut'] = "2AM";
					
					if ( substr( $v['scheduleOut'], -2 ) == "AM" ) { // CHECK: Getting off at 2am is the next day!
						$dateTime = new DateTime($v['date']);
						$dateTime->modify('+1 day');
						$timeString_out = $dateTime->format('Y-m-d');
					}
					else {
						$timeString_out = $v['date'];
					}
					//print_r(substr($v['scheduleOut'], 1, 1));
					
					
					$timeString_out .= "T" . substr( $v['scheduleOut'], 0, 2 ) . ":00:00";
					
					if ( !in_array($timeString_out, $this->calendarEvents->getItems()) && !in_array($timeString_in, $this->calendarEvents->getItems() )) { // Event does not exist
						$thisEventData = array('time_in' => $timeString_in, 'time_out' => $timeString_out);
					

						// Filter Title
						
						switch( $v['dayType'] ) {
							case 0: 
								$params = array();
								$weekdays = array("MON", "TUE", "WED", "THU", "FRI");
								if ( in_array($v['dateCode'], $weekdays) && $hour < 12) {
										$thisEventData['title'] = $this->firstName . " @ Office";
								}
								else {
										$thisEventData['title'] = $this->firstName . " @ WFH";
								}
									
							
								
							break;
							case 1: 
								$thisEventData['title'] = $this->firstName . " @ PTO Day";
							break;
							case 2: 
								// Woohoo!
							break;
							case 3: 
								$thisEventData['title'] = $this->firstName . " Work Holiday";
								// Happy Holidays
							break;
							case 4: 
								$thisEventData['title'] = $this->firstName . " Work Standby";
							break;
							default: print( "ERROR!" );
						}
								
						// Add recipients
						/*
						$thisEventData['attendees'] = array();
						foreach( $this->attendees as $a ) {
								array_push( $thisEventData['attendees'], $a );
						}
						*/
						print_r($thisEventData);
						// Create the event!

						
						$event = new Google_Service_Calendar_Event(array(
							'summary' => $thisEventData['title'],
							'start' => array(
								'dateTime' => $thisEventData['time_in'].$this->timezone_numeric,
								'timeZone' => $this->timezone_word,
							),
							'end' => array(
								'dateTime' => $thisEventData['time_out'].$this->timezone_numeric,
								'timeZone' => $this->timezone_word,
							),
							//'attendees' => $thisEventData['attendees'],
							'reminders' => array(
								'useDefault' => TRUE,
							)
							)
						);
						$event = $this->s->events->insert($this->calendar->id, $event);
						printf("Event created: %s \n", $event->htmlLink);
						// Create calendar event for this schedule entry
					}
					
					
					
			}
		}
	}
	
	
}
// Comma separated list, 0 - WORK, 1- PTO (Lime Green), 2- OFF (Gray), 3 - HOLIDAY (YELLOW), 4 - EMERGENCY SUPPORT (DARKBLUE)
$permitted_events = array(0, 1, 4);

$sheets = new SHEET_FUNCTIONS($leftBound, $rightBound, $firstName, $lastName);
$calendar = new CALENDAR_FUNCTIONS( $firstName, $permitted_events, $calendarName );
$schedule = $sheets->getSchedule( $sheets->getUserRow() );

//$calendar->sample();
print_r( $calendar->parseSchedule( $schedule ) );
