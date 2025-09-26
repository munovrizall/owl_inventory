<h1 align="center"> OWL Inventory </h1> <br>
<p align="center">
  <a href="#">
    <img alt="owl inventory" title="owl inventory" src="https://i.imgur.com/aOrTUkR.png" width="360">
  </a>
</p>

<p align="center">
  Integrated Inventory Management IoT Devices.
</p>

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->

## Table of Contents

- [Introduction](#introduction)
- [Features](#features)
- [Setup](#setup)
- [Team](#team)
- [Acknowledgments](#acknowledgments)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## Introduction

This repository contains the source code and documentation for an Inventory Management System designed to streamline the production and monitoring processes of IoT devices. The system is capable of handling various stages of production, starting from tracking raw materials to monitoring deployed devices at client locations using MQTT protocol. I worked on this project during my internship at PT. Origin Wiracipta Lestari in the Research and Development division for a duration of 2 months.

![PHP](https://img.shields.io/badge/Built_with-PHP-blue?logo=php)
![HTML](https://img.shields.io/badge/Built_with-HTML-orange?logo=html5)
![CSS](https://img.shields.io/badge/Built_with-CSS-blueviolet?logo=css3)
![JavaScript](https://img.shields.io/badge/Built_with-JavaScript-yellow?logo=javascript)
![jQuery](https://img.shields.io/badge/Built_with-jQuery-blue?logo=jquery)

## Features

A few of the things you can do with this system:

- Recording the necessary materials for manufacturing IoT devices
- Producing IoT devices and generating Serial Number barcodes, MAC Addresses for each IoT device
- Recording the prices of materials and production costs for IoT devices
- Tracking the inventory of materials and the stock of IoT devices on hand
- Input collaborating clients
- Input devices that require maintenance based on client requests
- Creating PDF reports for IoT device maintenance events
- Monitoring IoT devices located at client sites
- Viewing historical records of all transactions conducted
- User account management system, with two roles: admin and user
- And much more...

<p align="center">
  <img src = "https://i.imgur.com/JtY4m8H.jpeg" width=1080>
</p>

## Setup

1. Clone this project into the C:\xampp\htdocs directory if you are using XAMPP, or into the C:\laragon\www directory if you are using Laragon.

```
git clone https://github.com/munovrizall/owl_inventory.git
```

2. Open phpMyAdmin and import the databaseinventory.sql file located in owl_inventory/databaseinventory.sql.

3. Start XAMPP or Laragon, and open url localhost/owl_inventory in your browser

4. Login with username "admin" and password "admin"

5. You are ready to go!

## Team

<table>
  <tbody>
    <tr>
      <td align="center" valign="top" width="33%"><a href="https://github.com/munovrizall"><img src="https://avatars.githubusercontent.com/u/85984439?v=4" width="100px;" alt="Mohammad Novrizal Sugiarto"/><br /><sub><b>Mohammad Novrizal Sugiarto</b></sub></a><br /></td>
      <td align="center" valign="top" width="33%"><a href="https://github.com/ryanffirdaus"><img src="https://avatars.githubusercontent.com/u/136410140?v=4" width="100px;" alt="Ryan Faatih Firdaus"/><br /><sub><b>Ryan Faatih Firdaus</b></sub></a><br /></td>
      <td align="center" valign="top" width="33%"><a href="https://github.com/rikimr"><img src="https://avatars.githubusercontent.com/u/96761115?v=4" width="100px;" alt="Riki Muhamad Rifai"/><br /><sub><b>Riki Muhamad Rifai</b></sub></a><br /></td>
    </tr>
  </tbody>
</table>

## Acknowledgments

Thanks to [OWL](http://web.owl-plantation.com:8080/index.php) for giving me the opportunity to intern in this company.
