<?php header("Content-Type: text/css"); ?>
/* <style > /* */

:root{
    --footer-height: 64px;
}

*{
    box-sizing: border-box;
    /* user-select: none; */
    pointer-events: visible;
    /* user-select: none; */
    transition: all 0.1s ease-in-out;
}
body{
    margin: 0;
    min-height: 100vh;
    padding-bottom: var(--footer-height);
    position: relative;
}
footer {
    height: var(--footer-height);
    margin: 0;
    padding: 16px;
    background: #464646;
    color: white;
    position: absolute;
    bottom: 0;
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    align-content: center;
    align-items: center;
    font-family: monospace;
    justify-content: center;
    left: 0;
    right: 0;
}
footer a{
    color: lime;
    text-decoration: none;
}
footer a:hover{
    text-decoration: underline;
}
main{
    padding: 16px;
}
img{
    width: 100%;
    height: 100%;
    object-fit: contain;
    cursor: none;
    pointer-events: none;
}

#event_5 {
    user-select: none;
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: fit-content;
    transition: all 0.3s ease-in-out;
    z-index: 999999999;
}

/* .top {
    background: #1c1b18;
    padding: 8px 32px 16px 32px;
} */

.top {
    
}

.flex-between {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.top-logo {
    width: 150px;
    height: 150px;
    cursor: pointer;
    padding: 16px;
}
.top-logout-btn {
    color: red;
    font-family: monospace;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    padding: 16px 64px;
}
.top-logo:hover, .top-logout-btn:hover {
    opacity: 0.8;
}
.top-logo:active, .top-logout-btn:active {
    opacity: 1;
}

nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
}

.nav-item {
    padding: 16px 32px;
    /* border: 1px solid lightgrey; */
    border-radius: 6px;
    color: #e7e7e7;
    cursor: pointer;
    text-decoration: none;
}
.nav-item.active, .nav-item.active *{
    color: lime;
    border-color: green;
    pointer-events: none;
    font-weight: bold;
}
.nav-item:hover{
    color: orange;
    border-color: orange;
}

.pageTitle {
    font-weight: bold;
    font-size: 18px;
    margin-top: 16px;
    text-align: center;
    font-family: monospace;
}
.ordersListContainer td{
    text-align: center;
}
.ordersListContainer th{
    color: #464646;
}
.ordersListContainer th, .ordersListContainer td{
    font-size: 11px;
    padding: 4px;
}
.ordersListContainer table {
    margin: 8px;
    width: calc(100% - 16px);
    border: 1px solid gray;
    border-style: dashed;
}
thead tr th{
    border-bottom: 1px solid gray;
    border-bottom-style: dashed;
}
.ordersListContainer {
    display: flex;
    flex-wrap: wrap;
    align-items: stretch;
    justify-content: space-evenly;
    align-content: stretch;
    flex-direction: row;
}
.ordersListItem {
    width: 600px;
    margin: 16px auto;
    padding: 32px;
    box-shadow: 5px 5px 10px -9px black;
    font-family: sans-serif;
    box-sizing: border-box;
    border-radius: 6px;
    border: 1px solid #e5e5e5;
}
.ordersListItem:hover{
    /* transform: scale(1.01); */
}
/* .ordersListItem:nth-child(even){
    background: #f5f5f5;
}
.ordersListItem:nth-child(odd){
    background: #efefef;
} */

span.name {
    margin: 4px;
    display: inline-block;
    font-size: 13px;
}
span.value {
    margin: 4px;
    display: inline-block;
    font-size: 13px;
    font-weight: bold;
    color: #464646;
    letter-spacing: 1px;
}

form.search-in-orders {
    margin: 32px auto;
    display: flex;
    max-width: 450px;
    text-align: center;
    border: 1px solid #ff0077;
    border-radius: 6px;
    height: auto;
    flex-direction: row;
    flex-wrap: nowrap;
    align-content: stretch;
    justify-content: space-between;
    align-items: stretch;
    overflow: hidden;
}
form.search-in-orders select {
    width: 100%;
    padding: 8px;
    font-size: 14px;
    border: 0px;
    outline: none;
}
form.search-in-orders input[type='text']{
    width: 100%;
    padding: 8px;
    font-size: 14px;
    border: 0px;
    outline: none;
}
form.search-in-orders button {
    outline: none;
    border: none;
    background: #ff0077;
    color: white;
    cursor: pointer;
}

.new_date {
    width: 100%;
    display: block;
    text-align: center;
    font-size: 17px;
    font-weight: bold;
    margin-bottom: 16px;
    color: green;
    margin-top: 64px;
}
.new_price {
    width: 100%;
    display: block;
    text-align: center;
    font-size: 20px;
    color: #ff0047;
    margin-top: 8px;
    font-family: sans-serif;
}
.new_price span{
    font-weight: bold;
}