<?php // createdb.php
  require_once 'login.php';
  $conn = new mysqli($hostname, $username, $password, $database);
  if ($conn->connect_error) die("Fatal Error");

  $query = "SET NAMES utf8";
  $result = $conn->query($query);
  if (!$result) die ('<br> Ошибка при установке кодировки');
  
  $query = "CREATE TABLE user (
    id SMALLINT NOT NULL AUTO_INCREMENT,
    email VARCHAR(50) NOT NULL,
    password VARCHAR(50) NOT NULL,
    surname VARCHAR(32) NOT NULL,
    name VARCHAR(32) NOT NULL,
    middle_name VARCHAR(32) NOT NULL,
    role VARCHAR(8) NOT NULL,
    PRIMARY KEY (id)
    ) ";
    
    $result = $conn->query($query);
    if (!$result) die ('<br>error on create table user: '.$conn->error);
    echo "<br>success create user";
    
  $query = "CREATE TABLE student (
    id SMALLINT NOT NULL,
    semester TINYINT NOT NULL,
    _group TINYINT NOT NULL,
    PRIMARY KEY (id)
    ) ";

    $result = $conn->query($query);
    if (!$result) die ('<br>error on create table student: '.$conn->error);
    echo "<br>success create student";
    
  $query = "CREATE TABLE subject (
    id TINYINT NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    mark BOOLEAN NOT NULL,
    PRIMARY KEY (id)
    ) ";
    
    $result = $conn->query($query);
    if (!$result) die ('<br>error on create table subject: '.$conn->error);
    echo "<br>success create subject";
    
  $query = "CREATE TABLE mark (
    id MEDIUMINT NOT NULL AUTO_INCREMENT,
    student_id SMALLINT NOT NULL,
    subject_id TINYINT NOT NULL,
    mark TINYINT NOT NULL,
    attestation_number TINYINT NOT NULL,
    PRIMARY KEY (id)
    ) ";
  
  $result = $conn->query($query);
    if (!$result) die ('<br>error on create table mark: '.$conn->error);
    echo "<br>success create mark";
  
  $query = "CREATE TABLE expected_mark (
    id MEDIUMINT NOT NULL AUTO_INCREMENT,
    student_id SMALLINT NOT NULL,
    subject_id TINYINT NOT NULL,
    mark TINYINT NOT NULL,
    PRIMARY KEY (id)
    ) ";
    
  $result = $conn->query($query);
    if (!$result) die ('<br>error on create table expected_mark: '.$conn->error);
    echo "<br>success create expected_mark";
  
  $query = "CREATE TABLE message (
    id INT NOT NULL AUTO_INCREMENT,
    sender_id SMALLINT NOT NULL,
    recipient_id SMALLINT NOT NULL,
    message_text TEXT(10000) NOT NULL,
    _read BOOLEAN NOT NULL,
    PRIMARY KEY (id)
    ) ";
                        
  $result = $conn->query($query);
    if (!$result) die ('<br>error on create table message: '.$conn->error);
    echo "<br>success create message";
  
  $query = "CREATE TABLE subject_semester (
    semester TINYINT NOT NULL,
    subject_id TINYINT NOT NULL,
    PRIMARY KEY (semester, subject_id)
    ) ";
    
    $result = $conn->query($query);
    if (!$result) die ('<br>error on create table subject_semester: '.$conn->error);
    echo "<br>success create subject_semester";
    
  $query = "CREATE TABLE lecturer_subject (
    lecturer_id SMALLINT NOT NULL,
    subject_id TINYINT NOT NULL,
    PRIMARY KEY (lecturer_id, subject_id)
    ) ";
    
    $result = $conn->query($query);
    if (!$result) die ('<br>error on create table lecturer_subject: '.$conn->error);
    echo "<br>success create lecturer_subject";
    
  ?>