<?php
	class Hike {
		var $id;
		var $title;
		var $name;
		var $participants;
		var $weather;
		var $description;
		var $startdate;
		var $enddate;
		var $mapfile;
		var $distance;
		var $observationPoints;
		var $track;
		var $userId;
		var $localId;

		function set_id($new_id) {
			$this->id = $new_id;
		}
		function get_id() {
			return $this->id;
		}
		function set_title($new_title) {
			$this->title = $new_title;
		}
		function get_title() {
			return $this->title;
		}
		function set_name($new_name) {
			$this->name = $new_name;
		}
		function get_name() {
			return $this->name;
		}
		function set_participants($new_participants) {
			$this->participants = $new_participants;
		}
		function get_participants() {
			return $this->participants;
		}
		function set_weather($new_weather) {
			$this->weather = $new_weather;
		}
		function get_weather() {
			return $this->weather;
		}
		function set_description($new_description) {
			$this->description = $new_description;
		}
		function get_description() {
			return $this->description;
		}
		function set_startdate($new_startdate) {
			$this->startdate = $new_startdate;
		}
		function get_startdate() {
			return $this->startdate;
		}
		function set_enddate($new_enddate) {
			$this->enddate = $new_enddate;
		}
		function get_enddate() {
			return $this->enddate;
		}
		function set_mapfile($new_mapfile) {
			$this->mapfile = $new_mapfile;
		}
		function get_mapfile() {
			return $this->mapfile;
		}
		function set_distance($new_distance) {
			$this->distance = $new_distance;
		}
		function get_distance() {
			return $this->distance;
		}
		function set_observationPoints($new_observationPoints) {
			$this->observationPoints = $new_observationPoints;
		}
		function get_observationPoints() {
			return $this->observationPoints;
		}
		function set_track($new_track) {
			$this->track = $new_track;
		}
		function get_track() {
			return $this->track;
		}
		function set_userId($new_userId) {
			$this->userId = $new_userId;
		}
		function get_userId() {
			return $this->userId;
		}
		function set_localId($new_localId) {
			$this->localId = $new_localId;
		}
		function get_localId() {
			return $this->localId;
		}
	}
?>